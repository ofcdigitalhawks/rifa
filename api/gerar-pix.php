<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Função para gerar dados falsos com a API do 4Devs (mantida)
function gerarDadosFalsos() {
    $dados_post = http_build_query([
        'acao' => 'gerar_pessoa',
        'sexo' => 'I',
        'pontuacao' => 'S',
        'idade' => '0',
        'cep_estado' => '',
        'cep_cidade' => '',
        'txt_qtde' => '1'
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://www.4devs.com.br/ferramentas_online.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $dados_post,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded"
        ]
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    return $data[0] ?? null;
}

// === Recebe os dados reais enviados via POST ===
$input = json_decode(file_get_contents('php://input'), true);
$nome = $input['nome'] ?? 'Anônimo';
$telefone = preg_replace('/\D/', '', $input['telefone'] ?? '');
$amount = intval($input['amount'] ?? 0);

// UTM capturados via query string
$utm_source = $input['utm_source'] ?? $_GET['utm_source'] ?? null;
$utm_medium = $input['utm_medium'] ?? $_GET['utm_medium'] ?? null;
$utm_campaign = $input['utm_campaign'] ?? $_GET['utm_campaign'] ?? null;
$utm_term = $input['utm_term'] ?? $_GET['utm_term'] ?? null;
$utm_content = $input['utm_content'] ?? $_GET['utm_content'] ?? null;

// Verifica valor mínimo
if ($amount < 500) {
  echo json_encode([
    "erro" => true,
    "mensagem" => "O valor mínimo permitido é R$ 5,00"
  ]);
  exit;
}

// Gera dados aleatórios (complementares) usando 4Devs
$dadosFake = gerarDadosFalsos();
if (!$dadosFake) {
  echo json_encode([
    "erro" => true,
    "mensagem" => "Erro ao gerar dados do cliente automaticamente"
  ]);
  exit;
}

// === REDIRECIONAR PARA O NOVO GERAR.PHP ===
// Montar URL com os parâmetros
$valorDecimal = number_format($amount / 100, 2, '.', '');
$utmQuery = http_build_query([
    'utm_source' => $utm_source,
    'utm_medium' => $utm_medium,
    'utm_campaign' => $utm_campaign,
    'utm_term' => $utm_term,
    'utm_content' => $utm_content
]);

$baseUrl = "http://localhost:8000"; // Servidor PHP
$gerarUrl = $baseUrl . "/gerar.php?value=" . $valorDecimal . "&" . $utmQuery;

// Fazer requisição para o novo gerar.php
$ch = curl_init($gerarUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'nome' => $nome,
    'telefone' => $telefone,
    'amount' => $amount,
    'email' => $dadosFake['email'],
    'cpf' => preg_replace('/\D/', '', $dadosFake['cpf']),
    'endereco' => $dadosFake['endereco'],
    'numero' => $dadosFake['numero'],
    'bairro' => $dadosFake['bairro'],
    'cidade' => $dadosFake['cidade'],
    'estado' => $dadosFake['estado_sigla'],
    'cep' => preg_replace('/\D/', '', $dadosFake['cep'])
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        "erro" => true,
        "mensagem" => "Erro ao processar pagamento"
    ]);
    exit;
}

$result = json_decode($response, true);

// Adaptar resposta para o formato antigo esperado pelo frontend
if (!$result['error'] && $result['QRCode']) {
    echo json_encode([
        "pix_code" => $result['QRCode'],
        "pix_qr_code" => $result['QRCode'], 
        "transaction_id" => $result['identifier'],
        "amount" => $result['valor'] ?? $amount,
        "customer" => [
            "name" => $nome,
            "email" => $dadosFake['email'],
            "cpf" => $dadosFake['cpf'],
            "phone" => $telefone
        ]
    ]);
} else {
    echo json_encode([
        "erro" => true,
        "mensagem" => $result['message'] ?? "Erro ao gerar PIX"
    ]);
}
?>

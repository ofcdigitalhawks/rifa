<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$hash = $_GET['hash'] ?? '';

if (empty($hash)) {
    echo json_encode([
        "erro" => true,
        "mensagem" => "ID da transação não fornecido"
    ]);
    exit;
}

// === REDIRECIONAR PARA O NOVO VERIFICAR.PHP ===
$baseUrl = "http://localhost:8000"; // Servidor PHP
$verificarUrl = $baseUrl . "/verificar.php?id=" . urlencode($hash);

// Fazer requisição para o novo verificar.php
$ch = curl_init($verificarUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        "erro" => true,
        "mensagem" => "Erro ao verificar status da transação"
    ]);
    exit;
}

$result = json_decode($response, true);

// Adaptar resposta para o formato antigo esperado pelo frontend
if ($result['success']) {
    $status = strtoupper($result['status'] ?? 'PENDING');
    
    echo json_encode([
        "success" => true,
        "status" => $status,
        "transaction_id" => $result['transaction_id'] ?? $hash,
        "amount" => $result['amount'],
        "payment_method" => $result['method'] ?? 'PIX',
        "created_at" => $result['created_at'],
        "updated_at" => $result['updated_at'],
        "customer" => $result['customer'],
        "pix_code" => $result['pixCode'],
        "pix_qr_code" => $result['pixQrCode'],
        "expires_at" => $result['expires_at'],
        "paid_at" => $result['paid_at'],
        // Manter compatibilidade com o frontend existente
        "erro" => $status === 'APPROVED' ? false : null,
        "approved" => $status === 'APPROVED'
    ]);
} else {
    echo json_encode([
        "erro" => true,
        "mensagem" => $result['message'] ?? "Erro ao verificar status da transação",
        "success" => false,
        "status" => 'ERROR'
    ]);
}
?>

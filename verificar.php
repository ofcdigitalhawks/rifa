<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'database.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'status' => 'error', 'message' => 'ID não fornecido']);
    exit;
}

$id = trim(preg_replace('/[^a-zA-Z0-9\-]/', '', $_GET['id']));

try {
    $apiUrl = 'https://pay.nivopayoficial.com.br/api/v1/transaction.getPayment';
    $secretKey = '8b220b0b-0d49-4977-8aee-53585f8c6df9';

    $ch = curl_init($apiUrl . '?id=' . urlencode($id));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $secretKey
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        throw new Exception("Erro na consulta da API Nivopay: $curlError");
    }

    $apiData = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Erro ao decodificar JSON da Nivopay");
    }

    $status = strtoupper($apiData['status'] ?? 'UNKNOWN');

    // === ATUALIZAR BANCO INTERNO APENAS SE STATUS NÃO FOR PENDING ===
    if ($status !== 'PENDING') {
        $db = new SimpleDB();
        $db->updateStatus($id, $status);
        $db->insert([
            'payment_id' => $id,
            'status' => $status,
            'amount' => $apiData['amount'] ?? 0,
            'action' => 'VERIFIED'
        ]);
    }

    echo json_encode([
        'success' => true,
        'status' => $status,
        'transaction_id' => $apiData['id'] ?? $id,
        'amount' => $apiData['amount'] ?? null,
        'method' => $apiData['method'] ?? null,
        'created_at' => $apiData['createdAt'] ?? null,
        'updated_at' => $apiData['updatedAt'] ?? null,
        'customer' => $apiData['customer'] ?? null,
        'pixCode' => $apiData['pixCode'] ?? null,
        'pixQrCode' => $apiData['pixQrCode'] ?? null
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => 'Erro ao verificar o status do pagamento',
        'debug' => $e->getMessage()
    ]);
}

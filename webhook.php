<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'database.php';

// Log do webhook para debug
$logFile = __DIR__ . '/webhook.log';

try {
    // Captura o payload JSON da Nivopay
    $input = file_get_contents('php://input');
    $webhookData = json_decode($input, true);
    
    // Log da requisição
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Webhook recebido: " . $input . "\n", FILE_APPEND);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido recebido');
    }
    
    // Verifica se é um pagamento aprovado
    $status = strtoupper($webhookData['status'] ?? '');
    $paymentId = $webhookData['paymentId'] ?? '';
    $totalValue = $webhookData['totalValue'] ?? 0;
    
    // === SALVAR NO BANCO INTERNO ===
    $db = new SimpleDB();
    
    if ($status === 'APPROVED' && !empty($paymentId)) {
        // Log do pagamento aprovado
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Pagamento APROVADO - ID: {$paymentId} - Valor: {$totalValue}\n", FILE_APPEND);
        
        // Atualizar status no banco interno
        $db->updateStatus($paymentId, $status);
        $db->insert([
            'payment_id' => $paymentId,
            'status' => $status,
            'amount' => $totalValue,
            'customer_name' => $webhookData['customer']['name'] ?? '',
            'customer_email' => $webhookData['customer']['email'] ?? '',
            'customer_cpf' => $webhookData['customer']['cpf'] ?? '',
            'payment_method' => $webhookData['paymentMethod'] ?? '',
            'action' => 'WEBHOOK_APPROVED'
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Webhook processado com sucesso',
            'paymentId' => $paymentId,
            'status' => $status
        ]);
    } else {
        // Log de outros status
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Status: {$status} - ID: {$paymentId}\n", FILE_APPEND);
        
        // Salvar outros status também
        $db->insert([
            'payment_id' => $paymentId,
            'status' => $status,
            'amount' => $totalValue,
            'action' => 'WEBHOOK_' . $status
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Webhook recebido',
            'status' => $status
        ]);
    }
    
} catch (Exception $e) {
    // Log do erro
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERRO: " . $e->getMessage() . "\n", FILE_APPEND);
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Responde com status 200 para a Nivopay
http_response_code(200);
?>
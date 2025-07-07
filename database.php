<?php

class SimpleDB {
    private $dbFile;
    
    public function __construct($dbFile = 'pagamentos.db') {
        $this->dbFile = __DIR__ . '/' . $dbFile;
        $this->initDB();
    }
    
    private function initDB() {
        if (!file_exists($this->dbFile)) {
            file_put_contents($this->dbFile, json_encode([]));
        }
    }
    
    private function readDB() {
        $data = file_get_contents($this->dbFile);
        return json_decode($data, true) ?: [];
    }
    
    private function writeDB($data) {
        file_put_contents($this->dbFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    public function insert($record) {
        $data = $this->readDB();
        $record['id'] = uniqid();
        $record['created_at'] = date('Y-m-d H:i:s');
        $data[] = $record;
        $this->writeDB($data);
        return $record['id'];
    }
    
    public function findByPaymentId($paymentId) {
        $data = $this->readDB();
        foreach ($data as $record) {
            if ($record['payment_id'] === $paymentId) {
                return $record;
            }
        }
        return null;
    }
    
    public function getAll() {
        return $this->readDB();
    }
    
    public function updateStatus($paymentId, $status) {
        $data = $this->readDB();
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['payment_id'] === $paymentId) {
                $data[$i]['status'] = $status;
                $data[$i]['updated_at'] = date('Y-m-d H:i:s');
                $this->writeDB($data);
                return true;
            }
        }
        return false;
    }
    
    public function clearAll() {
        $this->writeDB([]);
        return true;
    }
}

?>
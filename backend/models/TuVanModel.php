<?php
require_once '../config/db.php';

class TuVanModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function saveTuVan($hoten, $email, $sdt, $mota, $anhPath = null) {
        $stmt = $this->pdo->prepare("INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, anh) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$hoten, $email, $sdt, $mota, $anhPath]);
    }

    public function getAllTuVan() {
        $stmt = $this->pdo->query("SELECT * FROM tuvan ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function readFromFile($filepath) {
        if (file_exists($filepath)) {
            return file_get_contents($filepath);
        }
        return null;
    }

    public function processFileData($data) {
        // Example: parse CSV or text
        $lines = explode("\n", $data);
        $processed = [];
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $processed[] = strtoupper(trim($line)); // Example processing
            }
        }
        return $processed;
    }
}
?>
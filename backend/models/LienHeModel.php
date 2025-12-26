<?php
require_once __DIR__ . '/../config/db.php';

class LienHeModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Save contact message to database
     */
    public function saveLienHe($hoten, $email, $sdt, $noidung) {
        try {
            $sql = "INSERT INTO lienhe (HOTEN, EMAIL, SDT, NOIDUNGTN, TRANGTHAI) VALUES (?, ?, ?, ?, 'Chưa xử lý')";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$hoten, $email, $sdt, $noidung]);
        } catch(PDOException $e) {
            error_log("Error saving contact: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all contact messages
     */
    public function getAllLienHe() {
        try {
            $sql = "SELECT * FROM lienhe ORDER BY NgayTao DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error getting contacts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get contact by ID
     */
    public function getLienHeById($id) {
        try {
            $sql = "SELECT * FROM lienhe WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Error getting contact: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update contact status and response
     */
    public function updateLienHe($id, $trangthai, $phanhoi = null) {
        try {
            $sql = "UPDATE lienhe SET TRANGTHAI = ?, PHANHOI = ?, NgayCapNhat = NOW() WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$trangthai, $phanhoi, $id]);
        } catch(PDOException $e) {
            error_log("Error updating contact: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete contact by ID
     */
    public function deleteLienHe($id) {
        try {
            $sql = "DELETE FROM lienhe WHERE ID = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Error deleting contact: " . $e->getMessage());
            return false;
        }
    }
}
?>

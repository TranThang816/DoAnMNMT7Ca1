<?php
require_once __DIR__ . '/../config/db.php';

class AuthModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getUserByUsername($username) {
        $sql = "SELECT * FROM nhanvien WHERE TAIKHOAN = ? LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function verifyLogin($username, $password) {
        $user = $this->getUserByUsername($username);
        if (!$user) return false;
        if (isset($user['TRANGTHAI']) && strtolower($user['TRANGTHAI']) === 'inactive') return false;
        if (!isset($user['MATKHAU'])) return false;
        if (password_verify($password, $user['MATKHAU'])) {
            return $user;
        }
        return false;
    }
}
?>

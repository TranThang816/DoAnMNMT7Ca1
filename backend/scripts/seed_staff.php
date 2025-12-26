<?php
require_once __DIR__ . '/../config/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nhanvien (
        ID INT(11) NOT NULL AUTO_INCREMENT,
        TAIKHOAN VARCHAR(100) NOT NULL,
        MATKHAU VARCHAR(255) NOT NULL,
        HOTEN VARCHAR(100) DEFAULT NULL,
        EMAIL VARCHAR(100) DEFAULT NULL,
        VAITRO VARCHAR(20) DEFAULT 'STAFF',
        TRANGTHAI VARCHAR(20) DEFAULT 'ACTIVE',
        NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ID),
        UNIQUE KEY uniq_taikhoan (TAIKHOAN)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

    $username = 'staff';
    $password = '123456';
    $hoten = 'Nhân viên Hỗ trợ';
    $email = 'staff@example.com';

    // Check if user exists
    $stmt = $pdo->prepare('SELECT ID FROM nhanvien WHERE TAIKHOAN = ? LIMIT 1');
    $stmt->execute([$username]);
    if (!$stmt->fetch()) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO nhanvien (TAIKHOAN, MATKHAU, HOTEN, EMAIL, VAITRO, TRANGTHAI) VALUES (?, ?, ?, ?, ?, ?)');
        $ins->execute([$username, $hash, $hoten, $email, 'STAFF', 'ACTIVE']);
        echo "Seeded default staff account: username=staff, password=123456\n";
    } else {
        echo "Staff account already exists.\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

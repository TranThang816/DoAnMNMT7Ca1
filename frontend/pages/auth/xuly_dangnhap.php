<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../backend/models/AuthModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === '' || $password === '') {
    header('Location: login.php?err=' . urlencode('Vui lòng nhập đầy đủ thông tin'));
    exit;
}

$model = new AuthModel();
$user = $model->verifyLogin($username, $password);

if (!$user) {
    header('Location: login.php?err=' . urlencode('Sai tài khoản hoặc mật khẩu'));
    exit;
}

// Success: set session
$_SESSION['nv_id'] = $user['ID'];
$_SESSION['nv_name'] = $user['HOTEN'] ?? $user['TAIKHOAN'];
$_SESSION['role'] = $user['VAITRO'] ?? 'STAFF';

// Redirect to staff dashboards
header('Location: ../staff/staff_tuvan.php');
exit;

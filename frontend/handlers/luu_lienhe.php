<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include model
require_once __DIR__ . '/../../backend/models/LienHeModel.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../webLienHe.html');
    exit;
}

// Validate and sanitize input
$hoten = isset($_POST['hoten']) ? trim($_POST['hoten']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$sdt = isset($_POST['sdt']) ? trim($_POST['sdt']) : '';
$noidungtn = isset($_POST['noidungtn']) ? trim($_POST['noidungtn']) : '';

// Validation
$errors = [];
if (empty($hoten)) {
    $errors[] = "Vui lòng nhập họ tên";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}
if (empty($noidungtn)) {
    $errors[] = "Vui lòng nhập nội dung tin nhắn";
}

if (!empty($errors)) {
    echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
    exit;
}

// Save to database
try {
    $model = new LienHeModel();
    $result = $model->saveLienHe($hoten, $email, $sdt, $noidungtn);
    
    if ($result) {
        echo "<script>
            alert('✅ Gửi tin nhắn thành công!\\n\\nCảm ơn bạn đã liên hệ với chúng tôi.\\nChúng tôi sẽ phản hồi sớm nhất có thể.');
            window.location.href = '../webLienHe.html';
        </script>";
    } else {
        echo "<script>
            alert('❌ Có lỗi xảy ra! Vui lòng thử lại sau.');
            window.history.back();
        </script>";
    }
} catch(Exception $e) {
    error_log("Error saving contact: " . $e->getMessage());
    echo "<script>
        alert('❌ Lỗi hệ thống! Vui lòng thử lại sau.');
        window.history.back();
    </script>";
}

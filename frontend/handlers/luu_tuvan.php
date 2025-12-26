<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include model
require_once __DIR__ . '/../../backend/models/TuVanModel.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../webTuVan.html');
    exit;
}

// Validate and sanitize input
$hoten = isset($_POST['hoten']) ? trim($_POST['hoten']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$sdt = isset($_POST['sdt']) ? trim($_POST['sdt']) : '';
$mota = isset($_POST['mota']) ? trim($_POST['mota']) : '';
$doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : null;

// Validation
$errors = [];
if (empty($hoten)) {
    $errors[] = "Vui lòng nhập họ tên";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email không hợp lệ";
}
if (empty($sdt)) {
    $errors[] = "Vui lòng nhập số điện thoại";
}
if (empty($mota)) {
    $errors[] = "Vui lòng mô tả triệu chứng";
}

if (!empty($errors)) {
    echo "<script>alert('" . implode("\\n", $errors) . "'); window.history.back();</script>";
    exit;
}

// Handle file upload
$anhPath = null;
if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = dirname(__DIR__) . '/uploads/tuvan/';
    
    // Create directory if not exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileExtension = strtolower(pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $fileName = uniqid('tuvan_') . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['anh']['tmp_name'], $uploadPath)) {
            $anhPath = 'uploads/tuvan/' . $fileName;
        }
    }
}

// Save to database
try {
    $model = new TuVanModel();
    $result = $model->saveTuVan($hoten, $email, $sdt, $mota, $anhPath, $doctor_id);
    
    if ($result) {
        echo "<script>
            alert('✅ Gửi yêu cầu tư vấn thành công!\\n\\nChúng tôi sẽ liên hệ với bạn sớm nhất có thể.\\nVui lòng kiểm tra email hoặc điện thoại.');
            window.location.href = '../webTuVan.html';
        </script>";
    } else {
        echo "<script>
            alert('❌ Có lỗi xảy ra! Vui lòng thử lại sau.');
            window.history.back();
        </script>";
    }
} catch(Exception $e) {
    error_log("Error saving consultation: " . $e->getMessage());
    echo "<script>
        alert('❌ Lỗi hệ thống! Vui lòng thử lại sau.');
        window.history.back();
    </script>";
}

<?php
require_once __DIR__ . '/../../backend/models/TuVanModel.php';

$model = new TuVanModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $trangthai = isset($_POST['trangthai']) ? trim($_POST['trangthai']) : 'Chờ xử lý';
    $phanhoi = isset($_POST['phanhoi']) ? trim($_POST['phanhoi']) : null;

    if ($id <= 0) {
        echo "<script>alert('ID không hợp lệ'); window.location.href = 'admin_tuvan.php';</script>";
        exit;
    }

    $ok = $model->updateTuVan($id, $trangthai, $phanhoi);
    if ($ok) {
        echo "<script>alert('Đã cập nhật yêu cầu tư vấn'); window.location.href = 'admin_tuvan.php';</script>";
    } else {
        echo "<script>alert('Cập nhật thất bại'); window.history.back();</script>";
    }
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: admin_tuvan.php');
    exit;
}
$item = $model->getTuVanById($id);
if (!$item) {
    echo "<script>alert('Không tìm thấy yêu cầu'); window.location.href = 'admin_tuvan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phản hồi Tư vấn #<?= htmlspecialchars($item['ID']) ?></title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .form-container { max-width: 800px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 20px; }
        .field { margin-bottom: 12px; }
        select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .actions { margin-top: 16px; }
        .btn { padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-submit { background: #28a745; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; text-decoration: none; }
    </style>
</head>
<body>
<header>
    <div class="logo-container"><img src="../../assets/logo.png" alt="Logo"><span>Trung tâm tư vấn sức khỏe - Admin</span></div>
    <nav>
        <ul>
            <li><a href="../../index.html">Trang chủ</a></li>
            <li><a href="admin_tuvan.php">Quản lý Tư vấn</a></li>
            <li><a href="admin_lienhe.php">Quản lý Liên hệ</a></li>
        </ul>
    </nav>
</header>

<div class="form-container">
    <h2>Phản hồi / Cập nhật yêu cầu #<?= htmlspecialchars($item['ID']) ?></h2>
    <p><strong>Bệnh nhân:</strong> <?= htmlspecialchars($item['HOTEN']) ?> — <strong>Email:</strong> <?= htmlspecialchars($item['EMAIL']) ?> — <strong>SĐT:</strong> <?= htmlspecialchars($item['SDT']) ?></p>
    <form method="POST" action="phan_hoi_tuvan.php">
        <input type="hidden" name="id" value="<?= intval($item['ID']) ?>">
        <div class="field">
            <label>Trạng thái</label>
            <select name="trangthai" required>
                <?php
                    $options = ['Chờ xử lý', 'Đang xử lý', 'Hoàn thành'];
                    foreach ($options as $opt) {
                        $sel = ($item['TRANGTHAI'] === $opt) ? 'selected' : '';
                        echo "<option value=\"$opt\" $sel>$opt</option>";
                    }
                ?>
            </select>
        </div>
        <div class="field">
            <label>Phản hồi (ghi chú cho bệnh nhân)</label>
            <textarea name="phanhoi" rows="6" placeholder="Nhập nội dung phản hồi..."><?= htmlspecialchars($item['PHANHOI'] ?? '') ?></textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-submit">Lưu cập nhật</button>
            <a href="admin_tuvan.php" class="btn btn-back">Hủy</a>
        </div>
    </form>
</div>

<footer><p>&copy; 2025 Tư vấn Sức khỏe - Admin Panel</p></footer>
</body>
</html>

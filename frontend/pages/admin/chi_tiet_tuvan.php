<?php
require_once __DIR__ . '/../../backend/models/TuVanModel.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: admin_tuvan.php');
    exit;
}

$model = new TuVanModel();
$item = $model->getTuVanById($id);
if (!$item) {
    echo "<script>alert('Không tìm thấy yêu cầu tư vấn!'); window.location.href = 'admin_tuvan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Tư vấn #<?= htmlspecialchars($item['ID']) ?></title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .detail-container { max-width: 1000px; margin: 30px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
        .detail-header { background: cadetblue; color: #fff; padding: 16px 20px; }
        .detail-body { padding: 20px; }
        .field { margin-bottom: 12px; }
        .label { color: #666; font-size: 0.95em; }
        .value { font-weight: 500; }
        .actions { margin-top: 20px; }
        .btn { display: inline-block; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-back { background: #6c757d; color: #fff; }
        .btn-reply { background: #28a745; color: #fff; }
        .image-preview { margin-top: 10px; }
        .image-preview img { max-width: 100%; height: auto; border-radius: 6px; }
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

<div class="detail-container">
    <div class="detail-header">
        <h2>Chi tiết yêu cầu tư vấn #<?= htmlspecialchars($item['ID']) ?></h2>
    </div>
    <div class="detail-body">
        <div class="field"><span class="label">Họ tên:</span> <span class="value"><?= htmlspecialchars($item['HOTEN']) ?></span></div>
        <div class="field"><span class="label">Email:</span> <span class="value"><?= htmlspecialchars($item['EMAIL']) ?></span></div>
        <div class="field"><span class="label">SĐT:</span> <span class="value"><?= htmlspecialchars($item['SDT']) ?></span></div>
        <div class="field"><span class="label">Triệu chứng:</span> <div class="value" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($item['MOTA'])) ?></div></div>
        <div class="field"><span class="label">Chuyên khoa:</span> <span class="value"><?= htmlspecialchars($item['TENKHOA'] ?? 'Chưa xác định') ?></span></div>
        <div class="field"><span class="label">Bác sĩ:</span> <span class="value"><?= htmlspecialchars($item['TenBacSi'] ?? 'Chưa phân') ?></span></div>
        <div class="field"><span class="label">Trạng thái:</span> <span class="value"><?= htmlspecialchars($item['TRANGTHAI']) ?></span></div>
        <div class="field"><span class="label">Ngày tạo:</span> <span class="value"><?= date('d/m/Y H:i', strtotime($item['NgayTao'])) ?></span></div>
        <?php if (!empty($item['NgayCapNhat'])): ?>
        <div class="field"><span class="label">Cập nhật lần cuối:</span> <span class="value"><?= date('d/m/Y H:i', strtotime($item['NgayCapNhat'])) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($item['ANH'])): ?>
        <div class="field image-preview">
            <span class="label">Ảnh triệu chứng:</span>
            <div><img src="<?= '../../' . htmlspecialchars($item['ANH']) ?>" alt="Ảnh triệu chứng"></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($item['PHANHOI'])): ?>
        <div class="field"><span class="label">Phản hồi:</span> <div class="value" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($item['PHANHOI'])) ?></div></div>
        <?php endif; ?>
        <div class="actions">
            <a href="admin_tuvan.php" class="btn btn-back">← Quay lại</a>
            <a href="phan_hoi_tuvan.php?id=<?= intval($item['ID']) ?>" class="btn btn-reply">Phản hồi / Cập nhật</a>
        </div>
    </div>
</div>

<footer><p>&copy; 2025 Tư vấn Sức khỏe - Admin Panel</p></footer>
</body>
</html>

<?php
require_once __DIR__ . '/../backend/models/TuVanModel.php';

$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$doctor = null;

if ($doctor_id > 0) {
    $model = new TuVanModel();
    $bacsis = $model->getAllBacSi();
    foreach ($bacsis as $bs) {
        if ($bs['ID'] === $doctor_id) {
            $doctor = $bs;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch tư vấn với bác sĩ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .booking-container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        .booking-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .doctor-card { background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .doctor-card h2 { color: cadetblue; margin-top: 0; }
        .doctor-card p { margin: 10px 0; line-height: 1.6; }
        .doctor-card .label { font-weight: 600; color: #333; }
        .consultation-form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .consultation-form h2 { color: cadetblue; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .form-group button { background: #007bff; color: #fff; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 1em; }
        .form-group button:hover { background: #0056b3; }
        @media (max-width: 768px) {
            .booking-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<header>
    <div class="logo-container">
        <img src="assets/logo.png" alt="Logo">
        <span>Trung tâm tư vấn sức khỏe</span>
    </div>
    <nav>
        <ul class="main-menu">
            <li><a href="index.html">Trang chủ</a></li>
            <li><a href="webTinTuc.html">Tin tức</a></li>
            <li><a href="webBS.html">Bác sĩ</a></li>
            <li><a href="webTuVan.html">Tư vấn</a></li>
            <li><a href="webLienHe.html">Liên hệ</a></li>
            <li><a href="pages/auth/login.php">Đăng nhập</a></li>
        </ul>
    </nav>
</header>

<div class="booking-container">
    <h1 style="text-align: center; color: cadetblue;">Đặt lịch tư vấn sức khỏe</h1>
    
    <div class="booking-layout">
        <!-- Doctor Info -->
        <div class="doctor-card">
            <h2>Thông tin bác sĩ</h2>
            <?php if ($doctor): ?>
                <p><span class="label">Tên:</span> <?= htmlspecialchars($doctor['HOTEN']) ?></p>
                <p><span class="label">Chuyên khoa:</span> <?= htmlspecialchars($doctor['TENKHOA'] ?? 'N/A') ?></p>
                <p><span class="label">Chức vụ:</span> <?= htmlspecialchars($doctor['CHUCVU'] ?? 'N/A') ?></p>
                <p><span class="label">Kinh nghiệm:</span></p>
                <p style="padding-left: 20px;"><?= htmlspecialchars($doctor['KINHNGHIEM'] ?? 'N/A') ?></p>
                <?php if (!empty($doctor['EMAIL'])): ?>
                    <p><span class="label">Email:</span> <?= htmlspecialchars($doctor['EMAIL']) ?></p>
                <?php endif; ?>
                <?php if (!empty($doctor['SDT'])): ?>
                    <p><span class="label">Điện thoại:</span> <?= htmlspecialchars($doctor['SDT']) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p style="color: #666; font-style: italic;">Chọn bác sĩ từ trang <a href="webBS.html">Bác sĩ</a> để xem thông tin chi tiết.</p>
            <?php endif; ?>
        </div>

        <!-- Consultation Form -->
        <div class="consultation-form">
            <h2>Yêu cầu tư vấn</h2>
            <form action="handlers/luu_tuvan.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                <?php if ($doctor): ?>
                    <input type="hidden" name="doctor_id" value="<?= intval($doctor['ID']) ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="hoten">Họ và tên *</label>
                    <input type="text" id="hoten" name="hoten" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="sdt">Số điện thoại *</label>
                    <input type="tel" id="sdt" name="sdt" pattern="[0-9]{10,11}" required>
                </div>
                
                <div class="form-group">
                    <label for="mota">Mô tả triệu chứng *</label>
                    <textarea id="mota" name="mota" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="anh">Ảnh (tùy chọn)</label>
                    <input type="file" id="anh" name="anh" accept="image/*">
                </div>
                
                <div class="form-group">
                    <button type="submit">Gửi yêu cầu tư vấn</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2025 Tư vấn Sức khỏe. Mọi quyền được bảo lưu.</p>
</footer>

<script>
function validateForm() {
    const hoten = document.getElementById('hoten').value.trim();
    const email = document.getElementById('email').value.trim();
    const sdt = document.getElementById('sdt').value.trim();
    const mota = document.getElementById('mota').value.trim();
    
    if (hoten === '' || email === '' || sdt === '' || mota === '') {
        alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
        return false;
    }
    
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('Email không đúng định dạng!');
        return false;
    }
    
    const phonePattern = /^[0-9]{10,11}$/;
    if (!phonePattern.test(sdt)) {
        alert('Số điện thoại phải có 10-11 chữ số!');
        return false;
    }
    
    if (mota.length < 10) {
        alert('Vui lòng mô tả triệu chứng chi tiết hơn (ít nhất 10 ký tự)!');
        return false;
    }
    
    return true;
}
</script>
</body>
</html>

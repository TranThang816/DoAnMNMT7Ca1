<?php
require_once __DIR__ . '/../backend/models/TuVanModel.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($keyword !== '') {
    $model = new TuVanModel();
    
    // Search in specialties
    $khoas = $model->getAllKhoa();
    $matchedKhoas = array_filter($khoas, function($k) use ($keyword) {
        return stripos($k['TENKHOA'], $keyword) !== false || stripos($k['MOTA'], $keyword) !== false;
    });
    
    // Search in doctors
    $bacsis = $model->getAllBacSi();
    $matchedBacSis = array_filter($bacsis, function($bs) use ($keyword) {
        return stripos($bs['HOTEN'], $keyword) !== false || 
               stripos($bs['CHUCVU'], $keyword) !== false ||
               stripos($bs['KINHNGHIEM'], $keyword) !== false;
    });
    
    $results = [
        'khoas' => array_values($matchedKhoas),
        'bacsis' => array_values($matchedBacSis),
        'keyword' => $keyword
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm: <?= htmlspecialchars($keyword) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .search-page { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .search-header { margin-bottom: 30px; }
        .search-header h1 { color: cadetblue; }
        .search-results { margin-top: 20px; }
        .result-section { margin-bottom: 40px; }
        .result-section h2 { color: #2f6f74; border-bottom: 2px solid #2f6f74; padding-bottom: 10px; }
        .result-item { background: #fff; padding: 15px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .result-item h3 { margin-top: 0; color: cadetblue; }
        .no-results { text-align: center; padding: 40px; color: #666; }
        .back-link { display: inline-block; margin-bottom: 20px; color: cadetblue; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<header>
    <div class="logo-container">
        <img src="assets/logo.png" alt="Logo">
        <span>Tư vấn Sức khỏe</span>
    </div>
    <div class="search-container">
        <form method="GET" action="search.php">
            <input type="text" name="q" placeholder="Tìm kiếm bác sĩ, chuyên khoa, bài viết..." value="<?= htmlspecialchars($keyword) ?>" required>
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
        </form>
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

<div class="search-page">
    <a href="index.html" class="back-link">← Quay lại trang chủ</a>
    
    <div class="search-header">
        <h1>Kết quả tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</h1>
        <?php if ($keyword !== ''): ?>
            <p>Tìm thấy <?= count($results['khoas']) + count($results['bacsis']) ?> kết quả</p>
        <?php endif; ?>
    </div>

    <?php if ($keyword === ''): ?>
        <div class="no-results">
            <p>Vui lòng nhập từ khóa tìm kiếm</p>
        </div>
    <?php elseif (count($results['khoas']) === 0 && count($results['bacsis']) === 0): ?>
        <div class="no-results">
            <p>Không tìm thấy kết quả phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>"</p>
            <p>Vui lòng thử lại với từ khóa khác</p>
        </div>
    <?php else: ?>
        <div class="search-results">
            <?php if (!empty($results['khoas'])): ?>
                <div class="result-section">
                    <h2>Chuyên khoa (<?= count($results['khoas']) ?>)</h2>
                    <?php foreach ($results['khoas'] as $khoa): ?>
                        <div class="result-item">
                            <h3><?= htmlspecialchars($khoa['TENKHOA']) ?></h3>
                            <p><?= htmlspecialchars($khoa['MOTA']) ?></p>
                            <a href="webTuVan.html" style="color: cadetblue;">Đăng ký tư vấn →</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($results['bacsis'])): ?>
                <div class="result-section">
                    <h2>Bác sĩ (<?= count($results['bacsis']) ?>)</h2>
                    <?php foreach ($results['bacsis'] as $bs): ?>
                        <div class="result-item">
                            <h3><?= htmlspecialchars($bs['HOTEN']) ?></h3>
                            <p><strong>Chức vụ:</strong> <?= htmlspecialchars($bs['CHUCVU']) ?></p>
                            <p><strong>Kinh nghiệm:</strong> <?= htmlspecialchars($bs['KINHNGHIEM']) ?></p>
                            <?php if (!empty($bs['EMAIL'])): ?>
                                <p><strong>Email:</strong> <?= htmlspecialchars($bs['EMAIL']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($bs['SDT'])): ?>
                                <p><strong>SĐT:</strong> <?= htmlspecialchars($bs['SDT']) ?></p>
                            <?php endif; ?>
                            <a href="webTuVan.html" style="color: cadetblue;">Đặt lịch tư vấn →</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 Tư vấn Sức khỏe. Mọi quyền được bảo lưu.</p>
</footer>
</body>
</html>

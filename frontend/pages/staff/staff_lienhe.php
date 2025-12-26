<?php
require_once __DIR__ . '/../../../backend/auth/middleware.php';
require_staff();
require_once __DIR__ . '/../../../backend/models/LienHeModel.php';

$model = new LienHeModel();
$danhsach = $model->getAllLienHe();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân viên - Quản lý Liên hệ</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .staff-container { max-width: 1400px; margin: 30px auto; padding: 20px; }
        .staff-header { background: #2f6f74; color: #fff; padding: 16px; border-radius: 8px; }
        .filter-section { margin: 16px 0; background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .filter-section select, .filter-section input { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 6px; }
        .table-wrap { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #e9f4f5; font-weight: 600; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 0.9em; }
        .status-new { background: #fff3cd; color: #856404; }
        .status-replied { background: #d4edda; color: #155724; }
        .btn { padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer; font-size: 0.9em; }
        .btn-view { background: #17a2b8; color: #fff; }
        .btn-update { background: #28a745; color: #fff; }
        .legend { font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
<header>
    <div class="logo-container"><img src="../../assets/logo.png" alt="Logo"><span>Trung tâm tư vấn sức khỏe - Nhân viên</span></div>
    <nav>
        <ul>
            <li><a href="../../index.html">Trang chủ</a></li>
            <li><a href="staff_tuvan.php">NV Quản lý Tư vấn</a></li>
            <li><a href="staff_lienhe.php">NV Quản lý Liên hệ</a></li>
            <li><a href="../../pages/auth/logout.php">Đăng xuất (<?= htmlspecialchars($_SESSION['nv_name'] ?? 'NV') ?>)</a></li>
        </ul>
    </nav>
</header>

<div class="staff-container">
    <div class="staff-header">
        <h2>NV Quản lý Liên hệ</h2>
        <p class="legend">Xem, lọc và cập nhật trạng thái tin nhắn liên hệ. Không có quyền xóa.</p>
    </div>

    <div class="filter-section">
        <label>Trạng thái:</label>
        <select id="filterStatus" onchange="filterTable()">
            <option value="">Tất cả</option>
            <option value="Chưa xử lý">Chưa xử lý</option>
            <option value="Đã phản hồi">Đã phản hồi</option>
        </select>
        <label>Tìm kiếm:</label>
        <input type="text" id="searchInput" placeholder="Tên, email, SĐT..." onkeyup="filterTable()">
    </div>

    <div class="table-wrap">
        <table id="staffContactTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($danhsach)): ?>
                <tr><td colspan="8" style="text-align:center; padding:20px;">Chưa có tin nhắn liên hệ</td></tr>
            <?php else: ?>
                <?php foreach ($danhsach as $item): ?>
                    <tr>
                        <td><?= $item['ID'] ?></td>
                        <td><?= htmlspecialchars($item['HOTEN']) ?></td>
                        <td><?= htmlspecialchars($item['EMAIL']) ?></td>
                        <td><?= htmlspecialchars($item['SDT'] ?? 'N/A') ?></td>
                        <td><?= mb_substr(htmlspecialchars($item['NOIDUNGTN']), 0, 60) ?>...</td>
                        <td>
                            <?php $cls = ($item['TRANGTHAI'] == 'Chưa xử lý') ? 'status-new' : 'status-replied'; ?>
                            <span class="status <?= $cls ?>"><?= htmlspecialchars($item['TRANGTHAI']) ?></span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($item['NgayTao'])) ?></td>
                        <td>
                            <a class="btn btn-view" href="../../pages/admin/chi_tiet_lienhe.php?id=<?= intval($item['ID']) ?>">Xem</a>
                            <a class="btn btn-update" href="../../pages/admin/phan_hoi_lienhe.php?id=<?= intval($item['ID']) ?>">Cập nhật</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer><p>&copy; 2025 Tư vấn Sức khỏe - Staff</p></footer>

<script>
function filterTable() {
    const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#staffContactTable tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (!cells.length) return;
        const hoten = cells[1].textContent.toLowerCase();
        const email = cells[2].textContent.toLowerCase();
        const sdt = cells[3].textContent.toLowerCase();
        const status = cells[5].textContent.toLowerCase();
        const matchesSearch = hoten.includes(searchInput) || email.includes(searchInput) || sdt.includes(searchInput);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}
</script>
</body>
</html>

<?php
require_once __DIR__ . '/../../../backend/auth/middleware.php';
require_staff();
require_once __DIR__ . '/../../../backend/models/TuVanModel.php';

$model = new TuVanModel();
$danhsach = $model->getAllTuVan();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân viên - Quản lý Tư vấn</title>
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
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-done { background: #d4edda; color: #155724; }
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
        <h2>NV Quản lý Tư vấn</h2>
        <p class="legend">Xem, lọc và cập nhật trạng thái yêu cầu tư vấn. Không có quyền xóa.</p>
    </div>

    <div class="filter-section">
        <label>Trạng thái:</label>
        <select id="filterStatus" onchange="filterTable()">
            <option value="">Tất cả</option>
            <option value="Chờ xử lý">Chờ xử lý</option>
            <option value="Đang xử lý">Đang xử lý</option>
            <option value="Hoàn thành">Hoàn thành</option>
        </select>
        <label>Tìm kiếm:</label>
        <input type="text" id="searchInput" placeholder="Tên, email, SĐT..." onkeyup="filterTable()">
    </div>

    <div class="table-wrap">
        <table id="staffConsultationTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Triệu chứng</th>
                    <th>Chuyên khoa</th>
                    <th>Bác sĩ</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($danhsach)): ?>
                <tr><td colspan="10" style="text-align:center; padding:20px;">Chưa có yêu cầu tư vấn</td></tr>
            <?php else: ?>
                <?php foreach ($danhsach as $item): ?>
                    <tr>
                        <td><?= $item['ID'] ?></td>
                        <td><?= htmlspecialchars($item['HOTEN']) ?></td>
                        <td><?= htmlspecialchars($item['EMAIL']) ?></td>
                        <td><?= htmlspecialchars($item['SDT']) ?></td>
                        <td><?= mb_substr(htmlspecialchars($item['MOTA']), 0, 60) ?>...</td>
                        <td><?= htmlspecialchars($item['TENKHOA'] ?? 'Chưa xác định') ?></td>
                        <td><?= htmlspecialchars($item['TenBacSi'] ?? 'Chưa phân') ?></td>
                        <td>
                            <?php 
                                $statusClass = 'status-pending';
                                if ($item['TRANGTHAI'] == 'Đang xử lý') $statusClass = 'status-processing';
                                if ($item['TRANGTHAI'] == 'Hoàn thành') $statusClass = 'status-done';
                            ?>
                            <span class="status <?= $statusClass ?>"><?= htmlspecialchars($item['TRANGTHAI']) ?></span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($item['NgayTao'])) ?></td>
                        <td>
                            <a class="btn btn-view" href="../../pages/admin/chi_tiet_tuvan.php?id=<?= intval($item['ID']) ?>">Xem</a>
                            <a class="btn btn-update" href="../../pages/admin/phan_hoi_tuvan.php?id=<?= intval($item['ID']) ?>">Cập nhật</a>
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
    const rows = document.querySelectorAll('#staffConsultationTable tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (!cells.length) return;
        const hoten = cells[1].textContent.toLowerCase();
        const email = cells[2].textContent.toLowerCase();
        const sdt = cells[3].textContent.toLowerCase();
        const status = cells[7].textContent.toLowerCase();
        const matchesSearch = hoten.includes(searchInput) || email.includes(searchInput) || sdt.includes(searchInput);
        const matchesStatus = !statusFilter || status.includes(statusFilter);
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}
</script>
</body>
</html>

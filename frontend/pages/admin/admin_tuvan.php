<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tư vấn - Admin</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        .admin-header {
            background: cadetblue;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .admin-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #5f9ea0;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .status-processing {
            background: #cce5ff;
            color: #004085;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn-reply {
            background: #28a745;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .filter-section select, .filter-section input {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .admin-table {
                overflow-x: scroll;
            }
            th, td {
                font-size: 0.85em;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="../../assets/logo.png" alt="Logo">
            <span>Trung tâm tư vấn sức khỏe - Admin</span>
        </div>
        <nav>
            <ul>
                <li><a href="../../index.html">Trang chủ</a></li>
                <li><a href="admin_tuvan.php">Quản lý Tư vấn</a></li>
                <li><a href="admin_lienhe.php">Quản lý Liên hệ</a></li>
                <li><a href="../../webBS.html">Bác sĩ</a></li>
            </ul>
        </nav>
    </header>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Quản lý Yêu cầu Tư vấn</h1>
            <p>Danh sách tất cả các yêu cầu tư vấn từ bệnh nhân</p>
        </div>

        <div class="filter-section">
            <label>Lọc theo trạng thái:</label>
            <select id="filterStatus" onchange="filterTable()">
                <option value="">Tất cả</option>
                <option value="Chờ xử lý">Chờ xử lý</option>
                <option value="Đang xử lý">Đang xử lý</option>
                <option value="Hoàn thành">Hoàn thành</option>
            </select>
            
            <label>Tìm kiếm:</label>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Tên, email, SĐT...">
        </div>

        <div class="admin-table">
            <?php
            require_once __DIR__ . '/../../backend/models/TuVanModel.php';
            
            $model = new TuVanModel();
            $danhsach = $model->getAllTuVan();
            ?>
            
            <table id="consultationTable">
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
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 20px;">Chưa có yêu cầu tư vấn nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($danhsach as $item): ?>
                            <tr>
                                <td><?= $item['ID'] ?></td>
                                <td><?= htmlspecialchars($item['HOTEN']) ?></td>
                                <td><?= htmlspecialchars($item['EMAIL']) ?></td>
                                <td><?= htmlspecialchars($item['SDT']) ?></td>
                                <td><?= mb_substr(htmlspecialchars($item['MOTA']), 0, 50) ?>...</td>
                                <td><?= $item['TENKHOA'] ?? 'Chưa xác định' ?></td>
                                <td><?= $item['TenBacSi'] ?? 'Chưa phân' ?></td>
                                <td>
                                    <?php 
                                    $statusClass = 'status-pending';
                                    if ($item['TRANGTHAI'] == 'Đang xử lý') $statusClass = 'status-processing';
                                    if ($item['TRANGTHAI'] == 'Hoàn thành') $statusClass = 'status-completed';
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $item['TRANGTHAI'] ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($item['NgayTao'])) ?></td>
                                <td>
                                    <button class="action-btn btn-view" onclick="viewDetail(<?= $item['ID'] ?>)">Xem</button>
                                    <button class="action-btn btn-reply" onclick="replyConsultation(<?= $item['ID'] ?>)">Phản hồi</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Tư vấn Sức khỏe - Admin Panel</p>
    </footer>

    <script>
        function filterTable() {
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('consultationTable');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                
                if (cells.length > 0) {
                    const hoten = cells[1].textContent.toLowerCase();
                    const email = cells[2].textContent.toLowerCase();
                    const sdt = cells[3].textContent.toLowerCase();
                    const status = cells[7].textContent.toLowerCase();
                    
                    const matchesSearch = hoten.includes(searchInput) || 
                                        email.includes(searchInput) || 
                                        sdt.includes(searchInput);
                    const matchesStatus = statusFilter === '' || status.includes(statusFilter);
                    
                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        }
        
        function viewDetail(id) {
            window.location.href = 'chi_tiet_tuvan.php?id=' + id;
        }
        
        function replyConsultation(id) {
            window.location.href = 'phan_hoi_tuvan.php?id=' + id;
        }
    </script>
</body>
</html>

<?php
require_once __DIR__ . '/../backend/models/TuVanModel.php';

$model = new TuVanModel();
$doctors = $model->getAllBacSi();

echo "<h2>Danh sách bác sĩ trong database:</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Tên</th><th>Chuyên khoa</th></tr>";

foreach ($doctors as $doc) {
    echo "<tr>";
    echo "<td>" . $doc['ID'] . "</td>";
    echo "<td>" . htmlspecialchars($doc['HOTEN']) . "</td>";
    echo "<td>" . htmlspecialchars($doc['TENKHOA'] ?? 'N/A') . "</td>";
    echo "</tr>";
}

echo "</table>";
?>

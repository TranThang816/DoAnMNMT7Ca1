<?php
$conn = new mysqli("localhost", "root", "", "tuvansuckhoe");
if ($conn->connect_error) {
    die("❌ Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ form
$hoten = $_POST['hoten'];
$email = $_POST['email'];
$sdt   = $_POST['sdt'];
$mota  = $_POST['mota'];

// Bản đồ từ khóa → chuyên khoa (TENKHOA)
$chuyenkhoa_map = [
    "tim" => "TIM MACH",
    "tim mạch" => "TIM MACH",
    "nội tiết" => "NỘI TIẾT",
    "gút" => "NỘI TIẾT",
    "đường huyết" => "NỘI TIẾT",
    "nhi" => "NHI KHOA",
    "trẻ em" => "NHI KHOA",
    "tai" => "TAI MŨI HỌNG",
    "mũi" => "TAI MŨI HỌNG",
    "họng" => "TAI MŨI HỌNG",
    "viêm họng" => "TAI MŨI HỌNG",
    "phụ khoa" => "SẢN PHỤ KHOA",
    "sản" => "SẢN PHỤ KHOA",
    "da" => "DA LIỄU",
    "mụn" => "DA LIỄU",
    "ung thư" => "UNG BƯỚU",
    "ung bướu" => "UNG BƯỚU",
    "khó thở" => "TIM MACH",       // hoặc HÔ HẤP nếu bạn tạo bảng riêng
    "ho" => "TAI MŨI HỌNG",
    "sổ mũi" => "TAI MŨI HỌNG",
    "mũi" => "TAI MŨI HỌNG",
    "viêm họng" => "TAI MŨI HỌNG",
    "nghẹt mũi" => "TAI MŨI HỌNG",
    "đau ngực" => "TIM MACH",
    "tim" => "TIM MACH",
    "tim mạch" => "TIM MACH"
];

// B1: Xác định IDKHOA phù hợp
$idkhoa = null;
foreach ($chuyenkhoa_map as $keyword => $tenkhoa) {
    if (stripos($mota, $keyword) !== false) {
        $query = "SELECT IDKHOA FROM khoa WHERE TENKHOA = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $tenkhoa);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $idkhoa = $row['IDKHOA'];
        }
        $stmt->close();
        break;
    }
}

// B2: Tìm bác sĩ thuộc khoa phù hợp
$idbacsi = null;
if ($idkhoa !== null) {
    $query = "SELECT ID FROM bacsi WHERE IDKHOA = ? ORDER BY RAND() LIMIT 1"; // random bác sĩ
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idkhoa);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $idbacsi = $row['ID'];
    }
    $stmt->close();
}

// B3: Insert vào bảng tư vấn
// ✅ Nếu có bác sĩ → lưu cả IDKHOA và IDBACSI
if ($idbacsi !== null && $idkhoa !== null) {
    $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, IDKHOA, IDBACSI) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $hoten, $email, $sdt, $mota, $idkhoa, $idbacsi);

// ✅ Nếu chỉ có IDKHOA (chưa tìm ra bác sĩ)
} else if ($idkhoa !== null) {
    $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA, IDKHOA) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $hoten, $email, $sdt, $mota, $idkhoa);

// ✅ Không tìm ra khoa nào phù hợp
} else {
    $sql = "INSERT INTO tuvan (HOTEN, EMAIL, SDT, MOTA) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $hoten, $email, $sdt, $mota);
}

$stmt->execute();

// B4: Phản hồi cho người dùng
if ($stmt->affected_rows > 0) {
    echo "<script>alert('✅ Gửi yêu cầu tư vấn thành công!'); window.location.href = 'webTuVan.html';</script>";
} else {
    echo "<script>alert('❌ Gửi thất bại! Vui lòng thử lại'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>

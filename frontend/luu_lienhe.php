    <?php
    // Kết nối database
    $conn = new mysqli("localhost", "root", "", "tuvansuckhoe");
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Nhận dữ liệu từ form
    $hoten = $_POST['hoten'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];
    $noidungtn = $_POST['noidungtn'];

    // Lưu vào bảng `tuvan`
    $sql = "INSERT INTO lienhe (HOTEN, EMAIL, SDT, NOIDUNGTN) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $hoten, $email, $sdt, $noidungtn);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('✅ Đã gửi thành công!'); window.location.href = 'webTuVan.html';</script>";
    } else {
        echo "❌ Lỗi khi lưu dữ liệu!";
    }

    $stmt->close();
    $conn->close();
    ?>

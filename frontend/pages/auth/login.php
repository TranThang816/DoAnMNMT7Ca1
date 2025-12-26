<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$err = isset($_GET['err']) ? $_GET['err'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Nhân viên</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        .login-container { max-width: 420px; margin: 60px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.12); overflow: hidden; }
        .login-header { background: cadetblue; color: #fff; padding: 18px; }
        .login-body { padding: 18px; }
        .login-body input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 12px; }
        .login-body button { width: 100%; padding: 10px; border: none; border-radius: 6px; background: #2f6f74; color: #fff; cursor: pointer; }
        .error { color: #dc3545; margin-bottom: 8px; }
        .info { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
<header>
    <div class="logo-container"><img src="../../assets/logo.png" alt="Logo"><span>Trung tâm tư vấn sức khỏe</span></div>
    <nav>
        <ul>
            <li><a href="../../index.html">Trang chủ</a></li>
            <li><a href="login.php">Đăng nhập</a></li>
        </ul>
    </nav>
</header>

<div class="login-container">
    <div class="login-header"><h2>Đăng nhập Nhân viên</h2></div>
    <div class="login-body">
        <?php if ($err): ?>
            <div class="error"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <form method="POST" action="xuly_dangnhap.php" onsubmit="return validateLogin()">
            <input type="text" name="username" id="username" placeholder="Tài khoản" required>
            <input type="password" name="password" id="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
            <p class="info">Dành cho nhân viên quản lý tư vấn và liên hệ.</p>
        </form>
    </div>
</div>

<footer><p>&copy; 2025 Tư vấn Sức khỏe</p></footer>
<script>
function validateLogin(){
    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value.trim();
    if (u.length < 3 || p.length < 3) { alert('Tài khoản/mật khẩu quá ngắn'); return false; }
    return true;
}
</script>
</body>
</html>

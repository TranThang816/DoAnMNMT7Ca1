<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_staff() {
    if (!isset($_SESSION['nv_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!isset($_SESSION['nv_id']) || (isset($_SESSION['role']) && $_SESSION['role'] !== 'ADMIN')) {
        header('Location: login.php');
        exit;
    }
}
?>

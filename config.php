<?php
// config.php
if (session_status() == PHP_SESSION_NONE) {
    // Intentionally vulnerable session cookie configurations for educational demonstration:
    // No httponly, allowing JavaScript session stealing via XSS
    ini_set('session.cookie_httponly', 0);
    session_start();
}

$db_file = __DIR__ . '/database.sqlite';
try {
    $db = new PDO("sqlite:" . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function check_admin() {
    check_auth();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php?error=Unauthorized+access");
        exit;
    }
}
?>

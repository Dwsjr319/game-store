<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'fdb1032.awardspace.net';
$db_user = '4674539_ddd';
$db_pass = 'ObjectiveX319';
$db_name = '4674539_ddd';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die('Database connection error.');
}

function alert_and_back($msg) {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.history.back();</script>";
    exit;
}
function alert_and_redirect($msg, $url) {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    $href = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.location.href='$href';</script>";
    exit;
}
function require_login_or_redirect($redirect = 'login.html') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: $redirect");
        exit;
    }
}
function get_user_password_hash(mysqli $conn, int $user_id): ?string {
    $sql = "SELECT password FROM registration WHERE user_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $hash = null;
    if ($res = $stmt->get_result()) {
        if ($row = $res->fetch_assoc()) {
            $hash = $row['password'] ?? null;
        }
    }
    $stmt->close();
    return $hash;
}
function verify_current_password(mysqli $conn, int $user_id, string $password_plain): void {
    $hash = get_user_password_hash($conn, $user_id);
    if (!$hash || !password_verify($password_plain, $hash)) {
        alert_and_back('Current password is incorrect.');
    }
}
function normalize_phone(?string $raw): string {
    $raw = $raw ?? '';
    $keep = preg_replace('/[^\d+]/', '', $raw);
    $keep = preg_replace('/^\++/', '+', $keep);
    return $keep;
}

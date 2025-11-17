<?php

$db_host = 'fdb1032.awardspace.net'; 
$db_user = '4674539_ddd';  
$db_pass = 'ObjectiveX319';
$db_name = '4674539_ddd';

session_start();

function alert_and_back($msg) {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.history.back();</script>";
    exit;
}

function alert_and_redirect($msg, $redirectUrl = 'login.html') {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    $url  = htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.location.href='$url';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request method.');
}

$identifier = trim($_POST['identifier'] ?? ''); // username or email
$password   = (string)($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
    alert_and_back('Please enter both your username/email and password.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');

    $sql = "SELECT user_id, username, email, password 
            FROM registration 
            WHERE username = ? OR email = ? 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$row = $result->fetch_assoc()) {
        alert_and_back('No account found with that username/email.');
    }

    $storedHash = $row['password'];

    if (!password_verify($password, $storedHash)) {
        alert_and_back('Incorrect password. Please try again.');
    }

    if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE registration SET password = ? WHERE user_id = ?");
        $upd->bind_param('si', $newHash, $row['user_id']);
        $upd->execute();
        $upd->close();
    }

    $_SESSION['user_id']  = (int)$row['user_id'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email']    = $row['email'];

    alert_and_redirect('Login successful!', 'index.php');

} catch (mysqli_sql_exception $e) {
    alert_and_back('A server error occurred while logging you in. Please try again.');
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) $stmt->close();
    if (isset($conn) && $conn instanceof mysqli) $conn->close();
}

<?php
require __DIR__ . '/app_bootstrap.php';
require_login_or_redirect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request.');
}

$user_id = (int)$_SESSION['user_id'];
$new_email = trim($_POST['new_email'] ?? '');
$current_password = (string)($_POST['current_password'] ?? '');

if ($new_email === '') {
    alert_and_back('Email is required.');
}
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    alert_and_back('Please enter a valid email address.');
}
if ($current_password === '') {
    alert_and_back('Current password is required.');
}

verify_current_password($conn, $user_id, $current_password);

$sql = "SELECT user_id FROM registration WHERE email = ? AND user_id <> ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $new_email, $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->fetch_assoc()) {
    $stmt->close();
    alert_and_back('That email is already in use.');
}
$stmt->close();

$upd = $conn->prepare("UPDATE registration SET email = ? WHERE user_id = ?");
$upd->bind_param('si', $new_email, $user_id);
$upd->execute();
$upd->close();

alert_and_redirect('Email updated successfully.', 'account.html#email');

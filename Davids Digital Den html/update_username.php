<?php
require __DIR__ . '/app_bootstrap.php';
require_login_or_redirect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request.');
}

$user_id = (int)$_SESSION['user_id'];
$new_username = trim($_POST['new_username'] ?? '');
$current_password = (string)($_POST['current_password'] ?? '');

if ($new_username === '') {
    alert_and_back('Username is required.');
}
if (!preg_match('/^[A-Za-z0-9_-]{3,30}$/', $new_username)) {
    alert_and_back('Username must be 3–30 chars, letters/numbers/underscore/hyphen only.');
}
if ($current_password === '') {
    alert_and_back('Current password is required.');
}

verify_current_password($conn, $user_id, $current_password);

$chk = $conn->prepare("SELECT user_id FROM registration WHERE username = ? AND user_id <> ? LIMIT 1");
$chk->bind_param('si', $new_username, $user_id);
$chk->execute();
$res = $chk->get_result();
if ($res && $res->fetch_assoc()) {
    $chk->close();
    alert_and_back('That username is already taken.');
}
$chk->close();

$upd = $conn->prepare("UPDATE registration SET username = ? WHERE user_id = ?");
$upd->bind_param('si', $new_username, $user_id);
$upd->execute();
$upd->close();

$_SESSION['username'] = $new_username;

alert_and_redirect('Username updated successfully.', 'account.html#username');

<?php
require __DIR__ . '/app_bootstrap.php';
require_login_or_redirect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request.');
}

$user_id = (int)$_SESSION['user_id'];
$current_password = (string)($_POST['current_password'] ?? '');
$new_password = (string)($_POST['new_password'] ?? '');
$confirm_password = (string)($_POST['confirm_password'] ?? '');

if ($current_password === '' || $new_password === '' || $confirm_password === '') {
    alert_and_back('All password fields are required.');
}
if (strlen($new_password) < 8) {
    alert_and_back('New password must be at least 8 characters.');
}
if ($new_password !== $confirm_password) {
    alert_and_back('New password and confirmation do not match.');
}

verify_current_password($conn, $user_id, $current_password);

$old_hash = get_user_password_hash($conn, $user_id);
if ($old_hash && password_verify($new_password, $old_hash)) {
    alert_and_back('New password must be different from the current password.');
}

$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

$upd = $conn->prepare("UPDATE registration SET password = ? WHERE user_id = ?");
$upd->bind_param('si', $new_hash, $user_id);
$upd->execute();
$upd->close();

alert_and_redirect('Password updated. Please use the new password next time you log in.', 'account.html#password');

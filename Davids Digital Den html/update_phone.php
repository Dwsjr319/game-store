<?php
require __DIR__ . '/app_bootstrap.php';
require_login_or_redirect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request.');
}

$user_id = (int)$_SESSION['user_id'];
$new_phone_raw = trim($_POST['new_phone'] ?? '');
$current_password = (string)($_POST['current_password'] ?? '');

if ($current_password === '') {
    alert_and_back('Current password is required.');
}

$normalized = normalize_phone($new_phone_raw);
$digits = preg_replace('/\D/', '', $normalized);
if ($normalized !== '' && (strlen($digits) < 7 || strlen($digits) > 15)) {
    alert_and_back('Please enter a valid phone number (7–15 digits; optional + at start).');
}

verify_current_password($conn, $user_id, $current_password);

$upd = $conn->prepare("UPDATE registration SET phone_number = ? WHERE user_id = ?");
$upd->bind_param('si', $normalized, $user_id);
$upd->execute();
$upd->close();

alert_and_redirect('Phone number updated.', 'account.html#phone');

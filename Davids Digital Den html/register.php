<?php

$db_host = 'fdb1032.awardspace.net';
$db_user = '4674539_ddd'; 
$db_pass = '[Password]'; 
$db_name = '4674539_ddd';  


function alert_and_back($msg) {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.history.back();</script>";
    exit;
}


function alert_and_redirect($msg, $redirectUrl = 'register.html') {
    $safe = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    $url  = htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8');
    echo "<script>alert('$safe'); window.location.href='$url';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    alert_and_back('Invalid request method.');
}

$username    = isset($_POST['username'])     ? trim($_POST['username'])     : '';
$email       = isset($_POST['email'])        ? trim($_POST['email'])        : '';
$password    = isset($_POST['password'])     ? (string)$_POST['password']   : '';
$confirm     = isset($_POST['confirm'])      ? (string)$_POST['confirm']    : '';
$phone_input = isset($_POST['phonenumber'])  ? trim($_POST['phonenumber'])  : '';
$birth_month = (int)($_POST['birth_month'] ?? 0);
$birth_day   = (int)($_POST['birth_day'] ?? 0);
$birth_year  = (int)($_POST['birth_year'] ?? 0);

if (!$birth_month || !$birth_day || !$birth_year) {
    alert_and_back('Please enter your complete birth date.');
}

$currentYear = (int)date('Y');
if ($birth_year < 1900 || $birth_year > $currentYear) {
    alert_and_back('Please enter a valid birth year.');
}
if ($birth_day < 1 || $birth_day > 31) {
    alert_and_back('Please enter a valid birth day (1–31).');
}
if ($birth_month < 1 || $birth_month > 12) {
    alert_and_back('Please select a valid month.');
}

$monthsWith30Days = [4, 6, 9, 11]; // Apr, Jun, Sep, Nov
if (in_array($birth_month, $monthsWith30Days, true) && $birth_day > 30) {
    alert_and_back('That month only has 30 days.');
}
if ($birth_month === 2) {
    $isLeapYear = ($birth_year % 4 === 0 && ($birth_year % 100 !== 0 || $birth_year % 400 === 0));
    if ($birth_day > 29) {
        alert_and_back('February has at most 29 days.');
    }
    if ($birth_day === 29 && !$isLeapYear) {
        alert_and_back("February $birth_year does not have 29 days.");
    }
}


$birthDate = DateTime::createFromFormat('Y-n-j', "$birth_year-$birth_month-$birth_day");
$errors = DateTime::getLastErrors();

if ($birthDate === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
    alert_and_back('Please enter a valid date. Check the day and month combination.');
}

$today = new DateTime();
$age = $today->diff($birthDate)->y;

if ($age < 0 || $age > 120) {
    alert_and_back('Please enter a realistic birth date.');
}

if ($username === '') {
    alert_and_back('Username is required.');
}
if (preg_match('/\s/', $username)) {
    alert_and_back('Username cannot contain spaces.');
}
if (!preg_match('/^[A-Za-z0-9_-]{3,30}$/', $username)) {
    alert_and_back('Username must be 3-30 characters and use only letters, numbers, underscores, or hyphens.');
}

if ($email === '') {
    alert_and_back('Email is required.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    alert_and_back('Please enter a valid email address.');
}

if ($password === '' || $confirm === '') {
    alert_and_back('Password and Confirm Password are required.');
}
if (strlen($password) < 8) {
    alert_and_back('Password must be at least 8 characters long.');
}
if ($password !== $confirm) {
    alert_and_back('Passwords do not match.');
}


$normalized_phone = preg_replace('/[^\d+]/', '', $phone_input);
$normalized_phone = preg_replace('/^\++/', '+', $normalized_phone);

$digits_only = preg_replace('/\D/', '', $normalized_phone);
if ($digits_only !== '' && (strlen($digits_only) < 7 || strlen($digits_only) > 15)) {
    alert_and_back('Please enter a valid phone number (7–15 digits).');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $conn->set_charset('utf8mb4');

    $checkSql = "SELECT user_id, username, email FROM registration WHERE username = ? OR email = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('ss', $username, $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (strcasecmp($row['username'], $username) === 0) {
            alert_and_back('That username is already taken. Please choose another.');
        }
        if (strcasecmp($row['email'], $email) === 0) {
            alert_and_back('That email is already registered. Try logging in or use another email.');
        }
		if (strcasecmp($row['phonenumber'], $normalized_phone) === 0) {
            alert_and_back('That phone number is already registered to an account. Please try using another phone number.');
        }
    }
    $checkStmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insertSql = "INSERT INTO registration (username, email, password, phone_number, age) VALUES (?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param('ssssi', $username, $email, $password_hash, $normalized_phone, $age);
    $insertStmt->execute();
    $insertStmt->close();

    alert_and_redirect('Registration successful!', 'login.html');

} catch (mysqli_sql_exception $e) {
    alert_and_back('A server error occurred while creating your account. Please try again.');
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

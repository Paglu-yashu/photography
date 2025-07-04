<?php
if (strpos($_SERVER['REQUEST_URI'], 'dashboard.php/dashboard.php') !== false) {
    header('HTTP/1.1 400 Bad Request');
    die('Redirect loop detected');
}
require_once __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Dashboard';
include __DIR__ . '/includes/header-admin.php';
?>

<h1>Admin Dashboard</h1>
<p>Welcome, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>!</p>


<?php include __DIR__ . '/includes/footer-admin.php'; ?>

<?php
// Absolute path inclusion
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/photography-portfolio/includes/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/photography-portfolio/includes/functions.php';

session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

$login_error = '';

// Login form handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple credential check (in production, use password_verify())
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
        exit;
    } else {
        $login_error = 'Invalid username or password';
    }
}
// Replace all header() redirects with:
function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
// Usage:
redirect('admin/dashboard.php');
?>

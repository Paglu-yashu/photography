<?php
// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change if needed
define('DB_PASS', '');      // Change if needed
define('DB_NAME', 'photography_portfolio');

// Site configuration
define('SITE_TITLE', 'Creative Lens Photography');

// Dynamic site URL detection
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_path = str_replace('/index.php', '', $_SERVER['PHP_SELF']);
define('SITE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_path);

// Admin credentials (CHANGE THESE IN PRODUCTION!)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'securepassword123'); // Change this

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Check database connection
if (!$conn->ping()) {
    die("Database connection failed: " . $conn->error);
} else {
    error_log("Database connection successful");
}

// Session configuration
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Full URL detection
$full_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$base_path = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim($full_url . $base_path, '/'));

// Then use for all redirects:
header('Location: ' . BASE_URL . '/admin/dashboard.php');

?>

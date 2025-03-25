<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Define admin pages
$admin_pages = ['delete.php', 'edit.php', 'admin.php', 'result.php'];

// Restrict access to admin pages for non-admin users
if (in_array(basename($_SERVER['PHP_SELF']), $admin_pages) && $_SESSION['user_type'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

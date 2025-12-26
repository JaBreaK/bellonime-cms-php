<?php
require_once __DIR__ . '/../../core/connection.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please login to access this page');
    redirect(ADMIN_URL . 'login.php');
}

// Set admin variables for template use
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : '';
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : '';
?>
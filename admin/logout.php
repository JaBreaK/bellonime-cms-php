<?php
require_once __DIR__ . '/../core/connection.php';

// Destroy session
session_destroy();

// Redirect to login page
setFlashMessage('success', 'Anda telah berhasil logout');
redirect(ADMIN_URL . 'login.php');
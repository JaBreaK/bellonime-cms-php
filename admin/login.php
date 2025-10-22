<?php
require_once __DIR__ . '/../core/connection.php';
require_once __DIR__ . '/../core/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(ADMIN_URL);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        setFlashMessage('error', 'Username dan password harus diisi');
    } else {
        // Get admin from database
        $admin = getAdminByUsername($username);
        
        if ($admin && verifyPassword($password, $admin['password'])) {
            // Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // Update last login
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
            $stmt->execute([':id' => $admin['id']]);
            
            setFlashMessage('success', 'Login berhasil! Selamat datang kembali, ' . $admin['username']);
            redirect(ADMIN_URL);
        } else {
            setFlashMessage('error', 'Username atau password salah');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel Bellonime</title>
    
    <!-- Tailwind CSS -->
    <link href="<?= ASSETS_URL ?>dist/css/input.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="bg-white rounded-full p-3">
                    <img class="h-16 w-auto" src="<?= ASSETS_URL ?>images/logo.png" alt="Bellonime">
                </div>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-white">Admin Panel</h2>
            <p class="mt-2 text-sm text-gray-200">Masuk ke dashboard Bellonime</p>
        </div>
        
        <!-- Login Form -->
        <div class="bg-white rounded-lg shadow-xl p-8">
            <?php if (hasFlashMessage('error')): ?>
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= getFlashMessage('error') ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (hasFlashMessage('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?= getFlashMessage('success') ?></span>
                </div>
            <?php endif; ?>
            
            <form class="space-y-6" method="POST">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input 
                        id="username" 
                        name="username" 
                        type="text" 
                        required 
                        class="input-field"
                        placeholder="Masukkan username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="input-field"
                        placeholder="Masukkan password"
                    >
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Ingat saya
                        </label>
                    </div>
                </div>
                
                <div>
                    <button 
                        type="submit" 
                        class="w-full btn-primary text-center py-3"
                    >
                        Masuk
                    </button>
                </div>
            </form>
            
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Informasi Login</span>
                    </div>
                </div>
                
                <div class="mt-6 text-sm text-gray-600 bg-gray-50 p-4 rounded">
                    <p class="font-medium mb-2">Default Login:</p>
                    <p>Username: <span class="font-mono bg-gray-200 px-2 py-1 rounded">admin</span></p>
                    <p>Password: <span class="font-mono bg-gray-200 px-2 py-1 rounded">admin123</span></p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center">
            <p class="text-white text-sm">
                &copy; <?= date('Y') ?> Bellonime. All rights reserved.
            </p>
            <div class="mt-2">
                <a href="<?= BASE_URL ?>" class="text-gray-200 hover:text-white text-sm transition-colors duration-200">
                    ← Kembali ke Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
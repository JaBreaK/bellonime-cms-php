<!DOCTYPE html>

<html lang="id" x-data="{ sidebar: localStorage.getItem('sidebarOpen') === null ? true : (localStorage.getItem('sidebarOpen') === 'true') }" 

              x-init="$watch('sidebar', value => localStorage.setItem('sidebarOpen', value))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Admin Panel - Bellonime</title>
    <meta name="description" content="Admin Panel Bellonime">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= ASSETS_URL ?>images/favicon.ico">
    
    <!-- Tailwind CSS -->
    <link href="<?= ASSETS_URL ?>dist/css/input.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Sidebar transition */
        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Flash Messages -->
    <?php if (hasFlashMessage('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= getFlashMessage('success') ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlashMessage('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= getFlashMessage('error') ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    <?php endif; ?>
    
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside :class="sidebar ? 'w-64' : 'w-16'" class="sidebar-transition bg-gray-800 text-white flex-shrink-0">
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <div x-show="sidebar" class="flex items-center">
                    <img class="h-8 w-auto" src="<?= ASSETS_URL ?>images/logo.png" alt="Bellonime">
                    <span class="ml-2 text-lg font-bold">Admin</span>
                </div>
                <button @click="sidebar = !sidebar" class="text-gray-400 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            
            <nav class="mt-4">
                <a href="<?= ADMIN_URL ?>" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-gray-700 text-white' : '' ?>">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="sidebar" class="ml-3">Dashboard</span>
                </a>
                
                <a href="<?= ADMIN_URL ?>manage-anime.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'manage-anime.php') ? 'bg-gray-700 text-white' : '' ?>">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                    </svg>
                    <span x-show="sidebar" class="ml-3">Manajemen Anime</span>
                </a>
                
                <a href="<?= ADMIN_URL ?>manage-episode.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'manage-episode.php') ? 'bg-gray-700 text-white' : '' ?>">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebar" class="ml-3">Manajemen Episode</span>
                </a>
                
                <a href="<?= ADMIN_URL ?>manage-genre.php" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200 <?= (basename($_SERVER['PHP_SELF']) == 'manage-genre.php') ? 'bg-gray-700 text-white' : '' ?>">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span x-show="sidebar" class="ml-3">Manajemen Genre</span>
                </a>
                
                <a href="<?= BASE_URL ?>" target="_blank" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span x-show="sidebar" class="ml-3">Lihat Website</span>
                </a>
            </nav>
            
            <!-- User Menu -->
            <div class="absolute bottom-0 left-0  p-4 border-t border-gray-700">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center">
                        <span class="text-sm font-medium"><?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?></span>
                    </div>
                    <div x-show="sidebar" class="ml-3">
                        <p class="text-sm font-medium"><?= $_SESSION['admin_username'] ?></p>
                        <p class="text-xs text-gray-400"><?= $_SESSION['admin_role'] ?></p>
                    </div>
                </div>
                <a href="<?= ADMIN_URL ?>logout.php" class="flex items-center mt-3 text-gray-300 hover:text-white transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebar" class="ml-2 text-sm">Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800"><?= isset($pageTitle) ? $pageTitle : 'Admin Panel' ?></h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            <?= date('d M Y, H:i') ?>
                        </span>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
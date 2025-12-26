<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Bellonime</title>
    <meta name="description" content="<?= isset($pageDescription) ? $pageDescription : 'Nonton anime streaming subtitle Indonesia' ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link href="<?= ASSETS_URL ?>dist/css/input.css" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-dark-900 text-white min-h-screen">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>" class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-primary-600">BELLO</span>
                    <span class="text-2xl font-bold text-white">NIME</span>
                </a>
                
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="<?= BASE_URL ?>" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'nav-link-active' : '' ?>">
                        Home
                    </a>
                    <a href="<?= BASE_URL ?>anime-list.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'anime-list.php' ? 'nav-link-active' : '' ?>">
                        Browse
                    </a>
                    
                    <!-- Genre Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="nav-link flex items-center gap-1">
                            Genre
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute top-full left-0 mt-2 w-48 bg-dark-700 rounded-lg shadow-xl border border-white/10 py-2">
                            <?php
                            $genres = getAllGenres();
                            foreach ($genres as $g): ?>
                                <a href="<?= BASE_URL ?>genre.php?slug=<?= $g['slug'] ?>" 
                                   class="block px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-dark-600">
                                    <?= $g['name'] ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Search & Mobile Menu -->
                <div class="flex items-center gap-2">
                    <!-- Search -->
                    <div x-data="{ open: false, query: '' }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                             class="absolute top-full right-0 mt-2 w-72 bg-dark-700 rounded-lg shadow-xl border border-white/10 p-3">
                            <form action="<?= BASE_URL ?>anime-list.php" method="GET">
                                <input type="text" name="search" x-model="query" placeholder="Search anime..." 
                                       class="input-field text-sm" autofocus>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <div x-data="{ open: false }" class="md:hidden">
                        <button @click="open = !open" class="p-2 text-gray-400 hover:text-white">
                            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        
                        <!-- Mobile Menu -->
                        <div x-show="open" x-transition class="absolute top-full left-0 right-0 bg-dark-800 border-b border-white/10 p-4">
                            <div class="flex flex-col gap-2">
                                <a href="<?= BASE_URL ?>" class="nav-link">Home</a>
                                <a href="<?= BASE_URL ?>anime-list.php" class="nav-link">Browse</a>
                                <?php foreach ($genres as $g): ?>
                                    <a href="<?= BASE_URL ?>genre.php?slug=<?= $g['slug'] ?>" class="nav-link text-sm text-gray-400">
                                        <?= $g['name'] ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Spacer for fixed navbar -->
    <div class="h-16"></div>
    
    <!-- Flash Messages -->
    <?php if (hasFlashMessage('success')): ?>
        <div class="fixed top-20 right-4 z-50 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg">
            <?= getFlashMessage('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (hasFlashMessage('error')): ?>
        <div class="fixed top-20 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-lg shadow-lg">
            <?= getFlashMessage('error') ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>
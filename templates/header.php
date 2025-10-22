<!DOCTYPE html>
<html lang="id" x-data="{ mobileMenu: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Bellonime - Nonton Anime Streaming</title>
    <meta name="description" content="<?= isset($pageDescription) ? $pageDescription : 'Nonton anime streaming subtitle Indonesia terlengkap' ?>">
    <meta name="keywords" content="anime, streaming, subtitle indonesia, anime indo, nonton anime">
    <meta name="author" content="Bellonime">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Bellonime">
    <meta property="og:description" content="<?= isset($pageDescription) ? $pageDescription : 'Nonton anime streaming subtitle Indonesia terlengkap' ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= BASE_URL . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:image" content="./assets/images/logo.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="./assets/images/favicon.ico">
    
    <!-- Tailwind CSS -->
    <link href="<?= ASSETS_URL ?>dist/css/input.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Plyr.io CSS -->
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    
    <!-- Custom CSS -->
    <style>
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
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

        /* Aspect ratio and embed responsive utilities (fallback if Tailwind aspect plugin is absent) */
        .aspect-video { aspect-ratio: 16 / 9; }
        .aspect-2-3 { aspect-ratio: 2 / 3; } /* 2:3 ratio for anime posters/cards */
        .embed-responsive { position: relative; width: 100%; }
        .embed-responsive::before { content: ""; display: block; }
        .embed-responsive-16by9::before { padding-top: 56.25%; } /* 16:9 */
        .embed-responsive > iframe,
        .embed-responsive > video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        /* Utility to make images fill their ratio container */
        .img-fill { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }

        /* Navigation link styling improvements */
        .nav-link { padding: 0.5rem 0.75rem; border-radius: 0.375rem; color: #374151; transition: all 200ms; }
        .nav-link:hover { color: #0ea5e9; background-color: rgba(2, 132, 199, 0.08); }
        .nav-link-active { color: #0ea5e9; background-color: rgba(2, 132, 199, 0.08); position: relative; }
        .nav-link-active::after { content: ""; position: absolute; left: 0.5rem; right: 0.5rem; bottom: 0.15rem; height: 2px; background: linear-gradient(90deg, rgba(14,165,233,0) 0%, rgba(14,165,233,0.6) 50%, rgba(14,165,233,0) 100%); border-radius: 9999px; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="<?= BASE_URL ?>" class="flex-shrink-0 flex items-center">
                        <img class="h-10 w-auto" src="./assets/images/logo.png" alt="Bellonime">
                        <span class="ml-2 text-xl font-bold text-primary-600">Bellonime</span>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex md:ml-10 md:space-x-4">
                        <a href="<?= BASE_URL ?>" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'nav-link-active' : '' ?>">
                            Beranda
                        </a>
                        <a href="<?= BASE_URL ?>anime-list.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'anime-list.php') ? 'nav-link-active' : '' ?>">
                            Daftar Anime
                        </a>
                        <div x-data="{ open: false }" class="relative" @mouseenter="open = true" @mouseleave="open = false">
                            <button @click="open = !open" :aria-expanded="open" aria-haspopup="true" class="nav-link flex items-center">
                                Genre
                                <svg class="ml-1 h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : 'rotate-0'" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open"
                                 @click.outside="open = false"
                                 @keydown.escape.window="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-40">
                                <div class="py-2">
                                    <?php
                                    $genres = getAllGenres();
                                    foreach ($genres as $g) {
                                        echo '<a href="' . BASE_URL . 'genre.php?slug=' . $g['slug'] . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">' . $g['name'] . '</a>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search and Mobile Menu -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div x-data="{
                        search: false,
                        query: '',
                        results: [],
                        loading: false,
                        searchTimeout: null,
                        async performSearch() {
                            if (this.query.length < 2) {
                                this.results = [];
                                return;
                            }
                            
                            this.loading = true;
                            
                            try {
                                const response = await fetch(`<?= BASE_URL ?>search.php?q=${encodeURIComponent(this.query)}`);
                                this.results = await response.json();
                            } catch (error) {
                                console.error('Search error:', error);
                                this.results = [];
                            } finally {
                                this.loading = false;
                            }
                        },
                        onInputChange() {
                            clearTimeout(this.searchTimeout);
                            this.searchTimeout = setTimeout(() => {
                                this.performSearch();
                            }, 300);
                        }
                    }" class="relative">
                        <button @click="search = !search; if (search) $nextTick(() => $refs.searchInput.focus())"
                                class="text-gray-600 hover:text-primary-600 p-2 rounded-md">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                        
                        <div x-show="search"
                             @click.away="search = false; results = []"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-96 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            
                            <!-- Search Input -->
                            <div class="p-4 border-b border-gray-200">
                                <form action="<?= BASE_URL ?>anime-list.php" method="GET" class="flex">
                                    <input type="text"
                                           name="search"
                                           x-ref="searchInput"
                                           x-model="query"
                                           @input="onInputChange()"
                                           placeholder="Cari anime..."
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-r-md transition-colors duration-200">
                                        Cari
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Search Results -->
                            <div x-show="query.length >= 2" class="max-h-96 overflow-y-auto">
                                <div x-show="loading" class="p-4 text-center text-gray-500">
                                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="mt-2 text-sm">Mencari...</span>
                                </div>
                                
                                <div x-show="!loading && results.length > 0" class="py-2">
                                    <template x-for="result in results" :key="result.id">
                                        <a :href="result.url" class="flex items-center p-3 hover:bg-gray-50 transition-colors duration-200">
                                            <img :src="result.poster" :alt="result.title" class="h-12 w-8 object-cover rounded">
                                            <div class="ml-3 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900" x-text="result.title"></h4>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="text-xs text-gray-500" x-text="result.type"></span>
                                                    <span class="text-xs text-gray-500">•</span>
                                                    <span class="text-xs text-gray-500" x-text="result.year"></span>
                                                    <span class="text-xs text-gray-500">•</span>
                                                    <span class="text-xs text-gray-500" x-text="result.episode_count + ' episode'"></span>
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </template>
                                </div>
                                
                                <div x-show="!loading && results.length === 0 && query.length >= 2" class="p-4 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm">Tidak ada hasil ditemukan</p>
                                </div>
                            </div>
                            
                            <div x-show="query.length < 2" class="p-4 text-center text-gray-500">
                                <p class="text-sm">Ketik minimal 2 karakter untuk mencari</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenu = !mobileMenu" class="text-gray-600 hover:text-primary-600 p-2 rounded-md">
                            <svg x-show="!mobileMenu" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="mobileMenu" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div x-show="mobileMenu" x-transition class="md:hidden bg-white border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="<?= BASE_URL ?>" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                    Beranda
                </a>
                <a href="<?= BASE_URL ?>anime-list.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                    Daftar Anime
                </a>
                <div x-data="{ open: false }">
                    <button @click="open = !open" :aria-expanded="open" aria-haspopup="true" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 flex justify-between items-center">
                        Genre
                        <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : 'rotate-0'" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="open"
                         x-transition
                         @keydown.escape.window="open = false"
                         class="pl-6 pr-3 pb-2">
                        <?php
                        foreach ($genres as $g) {
                            echo '<a href="' . BASE_URL . 'genre.php?slug=' . $g['slug'] . '" class="block py-1 text-sm text-gray-600 hover:text-gray-900">' . $g['name'] . '</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
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
    
    <!-- Main Content -->
    <main>
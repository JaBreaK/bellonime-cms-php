<?php
require_once 'core/connection.php';
require_once 'templates/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <h1 class="text-9xl font-bold text-primary-600">404</h1>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Halaman Tidak Ditemukan</h2>
            <p class="mt-2 text-sm text-gray-600">
                Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.
            </p>
        </div>
        
        <div class="mt-8 space-y-4">
            <a href="<?= BASE_URL ?>" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                Kembali ke Beranda
            </a>
            
            <div class="flex justify-center space-x-4">
                <a href="<?= BASE_URL ?>anime-list.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium transition-colors duration-200">
                    Daftar Anime
                </a>
                <span class="text-gray-300">•</span>
                <a href="<?= BASE_URL ?>search.php" class="text-primary-600 hover:text-primary-700 text-sm font-medium transition-colors duration-200">
                    Cari Anime
                </a>
            </div>
        </div>
        
        <div class="mt-8">
            <div class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 bg-white">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Error Code: 404
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
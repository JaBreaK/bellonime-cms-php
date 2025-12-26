    </main>
    
    <!-- Footer -->
    <footer class="bg-dark-800 border-t border-white/10 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Logo & Copyright -->
                <div class="text-center md:text-left">
                    <a href="<?= BASE_URL ?>" class="flex items-center justify-center md:justify-start gap-2 mb-2">
                        <span class="text-xl font-bold text-primary-600">BELLO</span>
                        <span class="text-xl font-bold text-white">NIME</span>
                    </a>
                    <p class="text-gray-500 text-sm">
                        &copy; <?= date('Y') ?> Bellonime. All rights reserved.
                    </p>
                </div>
                
                <!-- Links -->
                <div class="flex items-center gap-6 text-sm text-gray-400">
                    <a href="<?= BASE_URL ?>anime-list.php" class="hover:text-white transition-colors">Browse</a>
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top -->
    <button x-data="{ show: false }" 
            x-show="show" 
            @scroll.window="show = window.scrollY > 500"
            @click="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="fixed bottom-6 right-6 p-3 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg transition-all z-40">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>
    
    <!-- Scripts -->
    <script src="<?= ASSETS_URL ?>dist/js/main.js" defer></script>
</body>
</html>
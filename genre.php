<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('anime-list.php');
}

// Get genre data
$genre = getGenreBySlug($slug);

if (!$genre) {
    setFlashMessage('error', 'Genre tidak ditemukan');
    redirect('anime-list.php');
}

// Get pagination parameters
$page = (int)($_GET['page'] ?? 1);
$perPage = 12;

// Get animes by genre
$result = getAnimesByGenre($slug, $page, $perPage);

$pageTitle = $genre['name'] . ' Anime';
$pageDescription = 'Daftar anime genre ' . $genre['name'] . ' subtitle Indonesia';
?>
<?php include 'templates/header.php'; ?>

<!-- Genre Header -->
<section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-4"><?php echo $genre['name']; ?> Anime</h1>
        <p class="text-lg opacity-90"><?php echo $genre['description'] ?: 'Temukan anime genre ' . $genre['name'] . ' terbaik'; ?></p>
    </div>
</section>

<!-- Anime Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">
                <?php echo $result['total']; ?> Anime Genre <?php echo $genre['name']; ?>
            </h2>
            <a href="anime-list.php" class="text-primary-600 hover:text-primary-700 font-medium">
                ← Kembali ke Daftar Anime
            </a>
        </div>
        
        <?php if (empty($result['data'])): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada anime ditemukan</h3>
                <p class="mt-1 text-gray-500">Belum ada anime untuk genre <?php echo $genre['name']; ?></p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                <?php foreach ($result['data'] as $anime): ?>
                    <div class="group bg-white rounded-xl  overflow-hidden  transition-all duration-300">
                        <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="block">
                            <div class="relative aspect-2-3 bg-gray-200 overflow-hidden">
                                <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                                     alt="<?php echo $anime['title']; ?>"
                                     class="img-fill">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                                <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-xs">
                                    <div class="flex items-center text-white/90">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <?php echo number_format($anime['rating'], 1); ?>
                                    </div>
                                    <span class="px-2 py-1 rounded bg-blue-600/80 text-white">
                                        <?php echo $anime['episode_count']; ?> Episode
                                    </span>
                                </div>
                            </div>
                            <h3 class="mt-3 px-1 text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                                <?php echo $anime['title']; ?>
                                <span class="block h-px bg-gradient-to-r from-primary-500/0 via-primary-500/60 to-primary-500/0 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left mt-1"></span>
                            </h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($result['totalPages'] > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md  -space-x-px" aria-label="Pagination">
                        <?php if ($result['hasPrevPage']): ?>
                            <a href="?page=<?php echo $result['page'] - 1; ?>&slug=<?php echo $slug; ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $result['page'] - 2);
                        $endPage = min($result['totalPages'], $result['page'] + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?>&slug=<?php echo $slug; ?>" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $result['page'] ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($result['hasNextPage']): ?>
                            <a href="?page=<?php echo $result['page'] + 1; ?>&slug=<?php echo $slug; ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Other Genres -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Genre Lainnya</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php
            $allGenres = getAllGenres();
            foreach ($allGenres as $g):
                if ($g['id'] != $genre['id']):
            ?>
                <a href="genre.php?slug=<?php echo $g['slug']; ?>" 
                   class="bg-white hover:bg-gray-50 border border-gray-200 rounded-lg p-4 text-center transition-colors duration-200">
                    <h3 class="font-medium text-gray-900"><?php echo $g['name']; ?></h3>
                    <p class="text-sm text-gray-500 mt-1"><?php echo $g['anime_count']; ?> anime</p>
                </a>
            <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>
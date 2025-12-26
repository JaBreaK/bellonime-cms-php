<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

// Get featured anime (for hero)
$featuredAnimes = getAllAnimes(6, 0, null, true);
$heroAnime = !empty($featuredAnimes) ? $featuredAnimes[0] : null;

// Pre-process images
foreach ($featuredAnimes as &$anime) {
    $anime['poster'] = getImageUrl($anime['poster'] ?? '');
}
unset($anime);

// Get latest episodes
$latestEpisodes = getLatestEpisodes(12);

// Get ongoing anime
$ongoingAnimes = getAllAnimes(10, 0, 'Ongoing');

// Get popular anime (by views)
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT a.*, COUNT(e.id) as episode_count FROM animes a LEFT JOIN episodes e ON a.id = e.anime_id GROUP BY a.id ORDER BY a.views DESC LIMIT 10");
$popularAnimes = $stmt->fetchAll();

$pageTitle = 'Home';
?>
<?php include 'templates/header.php'; ?>

<!-- Hero Section -->
<?php if ($heroAnime): ?>
<section class="relative h-[70vh] overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?= getImageUrl($heroAnime['background'] ?? $heroAnime['poster'] ?? '') ?>" 
             alt="<?= htmlspecialchars($heroAnime['title']) ?>"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-dark-900 via-dark-900/80 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>
    </div>
    
    <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
        <div class="max-w-2xl">
            <span class="badge badge-primary mb-4">Featured</span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4"><?= htmlspecialchars($heroAnime['title']) ?></h1>
            <div class="flex items-center gap-4 mb-4 text-sm text-gray-300">
                <span><?= $heroAnime['type'] ?></span>
                <span>•</span>
                <span><?= $heroAnime['year'] ?></span>
                <span>•</span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <?= number_format($heroAnime['rating'] ?? 0, 1) ?>
                </span>
            </div>
            <p class="text-gray-300 line-clamp-3 mb-6"><?= htmlspecialchars($heroAnime['synopsis'] ?? '') ?></p>
            <div class="flex gap-3">
                <a href="detail.php?slug=<?= $heroAnime['slug'] ?>" class="btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                    </svg>
                    Watch Now
                </a>
                <a href="detail.php?slug=<?= $heroAnime['slug'] ?>" class="btn-secondary">
                    More Info
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Episodes -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Latest Episodes</h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <?php foreach ($latestEpisodes as $ep): ?>
            <a href="nonton.php?id=<?= $ep['id'] ?>" class="anime-card group">
                <div class="aspect-poster relative">
                    <img src="<?= getImageUrl($ep['poster'] ?? '') ?>" alt="<?= htmlspecialchars($ep['anime_title']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                    <div class="absolute top-2 left-2">
                        <span class="badge badge-primary text-xs">EP <?= $ep['episode_number'] ?></span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <h3 class="text-sm font-medium line-clamp-2"><?= htmlspecialchars($ep['anime_title']) ?></h3>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ongoing Anime -->
<section class="py-12 bg-dark-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Ongoing Anime</h2>
            <a href="anime-list.php?status=Ongoing" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                View All →
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php foreach ($ongoingAnimes as $anime): ?>
            <a href="detail.php?slug=<?= $anime['slug'] ?>" class="anime-card group">
                <div class="aspect-poster relative">
                    <img src="<?= getImageUrl($anime['poster'] ?? '') ?>" alt="<?= htmlspecialchars($anime['title']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute top-2 right-2">
                        <span class="badge badge-secondary text-xs"><?= $anime['episode_count'] ?? 0 ?> EP</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <h3 class="text-sm font-medium line-clamp-2"><?= htmlspecialchars($anime['title']) ?></h3>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span><?= $anime['type'] ?></span>
                            <span>•</span>
                            <span class="flex items-center">
                                <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <?= number_format($anime['rating'] ?? 0, 1) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Popular Anime -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Popular Anime</h2>
            <a href="anime-list.php?sort=popular" class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                View All →
            </a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <?php foreach ($popularAnimes as $anime): ?>
            <a href="detail.php?slug=<?= $anime['slug'] ?>" class="anime-card group">
                <div class="aspect-poster relative">
                    <img src="<?= getImageUrl($anime['poster'] ?? '') ?>" alt="<?= htmlspecialchars($anime['title']) ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <h3 class="text-sm font-medium line-clamp-2"><?= htmlspecialchars($anime['title']) ?></h3>
                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                            <span><?= number_format($anime['views']) ?> views</span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>

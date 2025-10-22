<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('index.php');
}

// Get anime data
$anime = getAnimeBySlug($slug);

if (!$anime) {
    setFlashMessage('error', 'Anime tidak ditemukan');
    redirect('index.php');
}

// Get anime genres
$genres = getAnimeGenres($anime['id']);

// Get episodes
$episodes = getEpisodesByAnimeId($anime['id']);

// Update views
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE animes SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $anime['id']]);

$pageTitle = $anime['title'];
$pageDescription = substr($anime['synopsis'], 0, 160) . '...';

// Get related anime (same genres)
$relatedAnimes = [];
if (!empty($genres)) {
    $genreIds = array_column($genres, 'id');
    $genreIdsString = implode(',', $genreIds);
    
    $sql = "SELECT DISTINCT a.*, 
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count
            FROM animes a
            JOIN anime_genre ag ON a.id = ag.anime_id
            WHERE ag.genre_id IN ($genreIdsString) AND a.id != :anime_id
            ORDER BY a.views DESC
            LIMIT 6";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':anime_id' => $anime['id']]);
    $relatedAnimes = $stmt->fetchAll();
}
?>
<?php include 'templates/header.php'; ?>

<!-- Anime Header -->
<section class="relative h-96 overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?php echo getImageUrl(($anime['background'] ?: $anime['poster']) ?? ''); ?>"
             alt="<?php echo $anime['title']; ?>"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"></div>
    </div>
    
    <div class="relative container mx-auto px-4 h-full flex items-end pb-8">
        <div class="flex flex-col md:flex-row gap-6">
            <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                 alt="<?php echo $anime['title']; ?>"
                 class="w-48 h-72 object-cover rounded-lg shadow-2xl">
            
            <div class="flex-1 text-white">
                <h1 class="text-3xl md:text-4xl font-bold mb-4"><?php echo $anime['title']; ?></h1>
                
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($genres as $genre): ?>
                        <a href="anime-list.php?genre=<?php echo $genre['slug']; ?>" 
                           class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-full transition-colors duration-200">
                            <?php echo $genre['name']; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                    <div>
                        <span class="text-gray-300">Type:</span>
                        <span class="ml-2"><?php echo $anime['type']; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-300">Status:</span>
                        <span class="ml-2"><?php echo $anime['status']; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-300">Episode:</span>
                        <span class="ml-2"><?php echo $anime['total_episodes']; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-300">Duration:</span>
                        <span class="ml-2"><?php echo $anime['duration']; ?> min</span>
                    </div>
                    <div>
                        <span class="text-gray-300">Studio:</span>
                        <span class="ml-2"><?php echo $anime['studio'] ?: '-'; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-300">Year:</span>
                        <span class="ml-2"><?php echo $anime['year']; ?></span>
                    </div>
                    <div>
                        <span class="text-gray-300">Season:</span>
                        <span class="ml-2"><?php echo $anime['season'] ?: '-'; ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-300">Rating:</span>
                        <svg class="w-4 h-4 text-yellow-400 ml-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span><?php echo number_format($anime['rating'], 1); ?></span>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center text-sm">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <?php echo number_format($anime['views']); ?> views
                    </div>
                    
                    <?php if (!empty($episodes)): ?>
                        <a href="<?php echo $episodes[0]['id'] ? 'nonton.php?id=' . $episodes[0]['id'] : '#'; ?>" 
                           class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tonton Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Synopsis -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Sinopsis</h2>
            <div class="prose prose-lg max-w-none">
                <p class="text-gray-700 leading-relaxed"><?php echo nl2br($anime['synopsis']); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Episodes -->
<?php if (!empty($episodes)): ?>
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Daftar Episode</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($episodes as $episode): ?>
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                    <a href="nonton.php?id=<?php echo $episode['id']; ?>" class="block">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-lg"><?php echo $episode['title']; ?></h3>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                    Episode <?php echo $episode['episode_number']; ?>
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?php echo date('d M Y', strtotime($episode['created_at'])); ?>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <?php echo number_format($episode['views']); ?>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center text-primary-600">
                                <span class="font-medium">Tonton Episode</span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Anime -->
<?php if (!empty($relatedAnimes)): ?>
<section class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Anime Terkait</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <?php foreach ($relatedAnimes as $related): ?>
                <div class="anime-card">
                    <a href="detail.php?slug=<?php echo $related['slug']; ?>" class="block">
                        <img src="<?php echo getImageUrl($related['poster'] ?? ''); ?>"
                             alt="<?php echo $related['title']; ?>"
                             class="w-full h-64 object-cover">
                        <div class="anime-card-overlay">
                            <div class="text-white">
                                <h3 class="font-semibold text-sm"><?php echo $related['title']; ?></h3>
                                <div class="mt-2 text-xs">
                                    <span class="px-2 py-1 bg-blue-600 text-white rounded">
                                        <?php echo $related['episode_count']; ?> Episode
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>
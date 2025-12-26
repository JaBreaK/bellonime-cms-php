<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    redirect('index.php');
}

$anime = getAnimeBySlug($slug);

if (!$anime) {
    setFlashMessage('error', 'Anime tidak ditemukan');
    redirect('index.php');
}

$genres = getAnimeGenres($anime['id']);
$episodes = getEpisodesByAnimeId($anime['id']);

// Update views
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE animes SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $anime['id']]);

$pageTitle = $anime['title'];
$pageDescription = substr($anime['synopsis'], 0, 160) . '...';
?>
<?php include 'templates/header.php'; ?>

<!-- Hero Banner -->
<section class="relative h-[50vh] overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?= getImageUrl($anime['background'] ?? $anime['poster'] ?? '') ?>" 
             alt="<?= htmlspecialchars($anime['title']) ?>"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-dark-900/60 to-dark-900/30"></div>
    </div>
</section>

<!-- Content -->
<section class="relative -mt-32 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Poster -->
            <div class="flex-shrink-0">
                <img src="<?= getImageUrl($anime['poster'] ?? '') ?>" 
                     alt="<?= htmlspecialchars($anime['title']) ?>"
                     class="w-48 md:w-64 aspect-poster object-cover rounded-xl shadow-2xl mx-auto lg:mx-0">
            </div>
            
            <!-- Info -->
            <div class="flex-1">
                <h1 class="text-3xl md:text-4xl font-bold mb-4"><?= htmlspecialchars($anime['title']) ?></h1>
                
                <!-- Genres -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($genres as $genre): ?>
                        <a href="genre.php?slug=<?= $genre['slug'] ?>" class="badge badge-secondary hover:bg-dark-400">
                            <?= $genre['name'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <!-- Meta Info -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <span class="text-gray-500 text-sm">Type</span>
                        <p class="font-medium"><?= $anime['type'] ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Status</span>
                        <p class="font-medium"><?= $anime['status'] ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Episodes</span>
                        <p class="font-medium"><?= $anime['total_episodes'] ?? count($episodes) ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Year</span>
                        <p class="font-medium"><?= $anime['year'] ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Studio</span>
                        <p class="font-medium"><?= $anime['studio'] ?: '-' ?></p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Duration</span>
                        <p class="font-medium"><?= $anime['duration'] ?> min</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Rating</span>
                        <p class="font-medium flex items-center">
                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <?= number_format($anime['rating'], 1) ?>
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Views</span>
                        <p class="font-medium"><?= number_format($anime['views']) ?></p>
                    </div>
                </div>
                
                <!-- Synopsis -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Synopsis</h3>
                    <p class="text-gray-400 leading-relaxed"><?= nl2br(htmlspecialchars($anime['synopsis'])) ?></p>
                </div>
                
                <!-- Watch Button -->
                <?php if (!empty($episodes)): ?>
                    <a href="nonton.php?id=<?= $episodes[0]['id'] ?>" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                        Watch Episode 1
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Episodes List -->
<?php if (!empty($episodes)): ?>
<section class="py-12 bg-dark-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-6">Episodes</h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php foreach ($episodes as $ep): ?>
                <a href="nonton.php?id=<?= $ep['id'] ?>" class="episode-card">
                    <div class="w-10 h-10 bg-dark-500 rounded-lg flex items-center justify-center font-bold text-primary-600">
                        <?= $ep['episode_number'] ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars($ep['title']) ?></p>
                        <p class="text-xs text-gray-500"><?= date('d M Y', strtotime($ep['created_at'])) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>
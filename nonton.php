<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    redirect('index.php');
}

$episode = getEpisodeById($id);

if (!$episode) {
    setFlashMessage('error', 'Episode tidak ditemukan');
    redirect('index.php');
}

$anime = getAnimeById($episode['anime_id']);
$allEpisodes = getEpisodesByAnimeId($anime['id']);
$nextEpisode = getNextEpisode($anime['id'], $episode['episode_number']);
$prevEpisode = getPreviousEpisode($anime['id'], $episode['episode_number']);

// Update views
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE episodes SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $episode['id']]);

$pageTitle = $episode['title'] . ' - ' . $anime['title'];
?>
<?php include 'templates/header.php'; ?>

<!-- Video Player -->
<section class="bg-black">
    <div class="max-w-6xl mx-auto">
        <div class="aspect-video bg-dark-900">
            <?php if (!empty($episode['video'])): ?>
                <video id="videoPlayer" controls class="w-full h-full" controlsList="nodownload">
                    <source src="<?= getLocalVideoUrl($episode['video']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <div class="text-center text-gray-400">
                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-lg">Video tidak tersedia</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Episode Info & Navigation -->
<section class="py-6 border-b border-white/10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold mb-1"><?= htmlspecialchars($episode['title']) ?></h1>
                <div class="flex items-center gap-3 text-sm text-gray-400">
                    <a href="detail.php?slug=<?= $anime['slug'] ?>" class="text-primary-600 hover:text-primary-500">
                        <?= htmlspecialchars($anime['title']) ?>
                    </a>
                    <span>•</span>
                    <span>Episode <?= $episode['episode_number'] ?></span>
                    <span>•</span>
                    <span><?= number_format($episode['views']) ?> views</span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <?php if ($prevEpisode): ?>
                    <a href="nonton.php?id=<?= $prevEpisode['id'] ?>" class="btn-secondary text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Prev
                    </a>
                <?php endif; ?>
                
                <?php if ($nextEpisode): ?>
                    <a href="nonton.php?id=<?= $nextEpisode['id'] ?>" class="btn-primary text-sm">
                        Next
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Episode List -->
<section class="py-8">
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-lg font-bold mb-4">All Episodes</h2>
        
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
            <?php foreach ($allEpisodes as $ep): ?>
                <a href="nonton.php?id=<?= $ep['id'] ?>" 
                   class="flex items-center justify-center h-12 rounded-lg font-medium text-sm transition-colors
                          <?= $ep['id'] == $episode['id'] ? 'bg-primary-600 text-white' : 'bg-dark-700 hover:bg-dark-600 text-gray-300' ?>">
                    <?= $ep['episode_number'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Comments -->
<section class="py-8 bg-dark-800">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-lg font-bold mb-4">Comments</h2>
        
        <!-- Comment Form -->
        <form method="POST" action="add-comment.php" class="mb-6">
            <input type="hidden" name="anime_id" value="<?= $anime['id'] ?>">
            <input type="hidden" name="episode_id" value="<?= $episode['id'] ?>">
            <textarea name="content" rows="3" placeholder="Write a comment..." class="input-field mb-3" required></textarea>
            <button type="submit" class="btn-primary text-sm">Post Comment</button>
        </form>
        
        <!-- Comments List -->
        <?php
        $comments = [];
        try {
            $stmtC = $db->prepare("SELECT c.*, COALESCE(u.username, 'Anonymous') AS username FROM comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.anime_id = :aid AND c.episode_id = :eid AND c.status = 'approved' ORDER BY c.created_at DESC");
            $stmtC->execute([':aid' => $anime['id'], ':eid' => $episode['id']]);
            $comments = $stmtC->fetchAll();
        } catch (Exception $e) {}
        ?>
        
        <div class="space-y-4">
            <?php if (empty($comments)): ?>
                <p class="text-gray-500 text-center py-8">No comments yet. Be the first!</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                    <div class="bg-dark-700 rounded-lg p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 bg-dark-500 rounded-full flex items-center justify-center text-sm font-medium">
                                <?= strtoupper(substr($c['username'], 0, 1)) ?>
                            </div>
                            <div>
                                <span class="font-medium"><?= htmlspecialchars($c['username']) ?></span>
                                <span class="text-xs text-gray-500 ml-2"><?= date('d M Y', strtotime($c['created_at'])) ?></span>
                            </div>
                        </div>
                        <p class="text-gray-300"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');
    if (video) {
        video.addEventListener('ended', function() {
            <?php if ($nextEpisode): ?>
            if (confirm('Episode selesai. Lanjut ke episode berikutnya?')) {
                window.location.href = 'nonton.php?id=<?= $nextEpisode['id'] ?>';
            }
            <?php endif; ?>
        });
    }
});
</script>

<?php include 'templates/footer.php'; ?>

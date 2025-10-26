<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    redirect('index.php');
}

// Get episode data
$episode = getEpisodeById($id);

if (!$episode) {
    setFlashMessage('error', 'Episode tidak ditemukan');
    redirect('index.php');
}

// Get anime data
$anime = getAnimeById($episode['anime_id']);

// Get all episodes for navigation
$allEpisodes = getEpisodesByAnimeId($anime['id']);

// Get next and previous episodes
$nextEpisode = getNextEpisode($anime['id'], $episode['episode_number']);
$prevEpisode = getPreviousEpisode($anime['id'], $episode['episode_number']);

// Update episode views
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("UPDATE episodes SET views = views + 1 WHERE id = :id");
$stmt->execute([':id' => $episode['id']]);

$pageTitle = $episode['title'] . ' - ' . $anime['title'];
$pageDescription = 'Nonton ' . $episode['title'] . ' subtitle Indonesia';
?>
<?php include 'templates/header.php'; ?>

<!-- Video Player Section -->
<section class="bg-black">
    <div class="container mx-auto px-4">
        <div class="aspect-video">
            <?php
                // Build per-quality EMBED and DOWNLOAD maps
                $embed1080 = $episode['embed_1080_url'] ?? '';
                $embed720  = $episode['embed_720_url'] ?? '';
                $embed480  = $episode['embed_480_url'] ?? '';

                $dl1080 = $episode['dl_1080_url'] ?? '';
                $dl720  = $episode['dl_720_url'] ?? '';
                $dl480  = $episode['dl_480_url'] ?? '';

                $initialQuality = !empty($embed1080) ? '1080' : (!empty($embed720) ? '720' : (!empty($embed480) ? '480' : ''));
                $initialEmbed   = !empty($embed1080) ? $embed1080 : (!empty($embed720) ? $embed720 : (!empty($embed480) ? $embed480 : ''));
                $initialDl      = $initialQuality === '1080' ? $dl1080 : ($initialQuality === '720' ? $dl720 : ($initialQuality === '480' ? $dl480 : ''));
            ?>

            <?php if (!empty($initialEmbed)): ?>
                <!-- EMBED Player with quality dropdown -->
                <iframe
                    id="embedPlayer"
                    src="<?php echo htmlspecialchars($initialEmbed); ?>"
                    class="w-full h-full"
                    frameborder="0"
                    allowfullscreen
                    referrerpolicy="origin">
                </iframe>

                <!-- Controls: Quality dropdown + single Download button -->
                <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <label for="qualitySelect" class="text-sm text-gray-300">Kualitas:</label>
                        <select id="qualitySelect" class="px-3 py-2 rounded bg-gray-800 text-white border border-gray-700">
                            <?php if (!empty($embed480)): ?>
                                <option value="480" data-embed="<?php echo htmlspecialchars($embed480); ?>" data-dl="<?php echo htmlspecialchars($dl480); ?>" <?php echo $initialQuality==='480'?'selected':''; ?>>480p</option>
                            <?php endif; ?>
                            <?php if (!empty($embed720)): ?>
                                <option value="720" data-embed="<?php echo htmlspecialchars($embed720); ?>" data-dl="<?php echo htmlspecialchars($dl720); ?>" <?php echo $initialQuality==='720'?'selected':''; ?>>720p</option>
                            <?php endif; ?>
                            <?php if (!empty($embed1080)): ?>
                                <option value="1080" data-embed="<?php echo htmlspecialchars($embed1080); ?>" data-dl="<?php echo htmlspecialchars($dl1080); ?>" <?php echo $initialQuality==='1080'?'selected':''; ?>>1080p</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <a id="downloadBtn"
                           href="<?php echo htmlspecialchars($initialDl); ?>"
                           target="_blank" rel="noopener"
                           class="px-4 py-2 rounded bg-primary-600 text-white hover:bg-primary-700 transition-colors <?php echo empty($initialDl) ? 'opacity-50 pointer-events-none' : ''; ?>">
                            Download
                        </a>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const select = document.getElementById('qualitySelect');
                    const iframe = document.getElementById('embedPlayer');
                    const download = document.getElementById('downloadBtn');
                    if (!select || !iframe) return;

                    function update() {
                        const opt = select.options[select.selectedIndex];
                        const embedUrl = opt.getAttribute('data-embed') || '';
                        const dlUrl = opt.getAttribute('data-dl') || '';
                        if (embedUrl) {
                            iframe.src = embedUrl;
                        }
                        if (download) {
                            if (dlUrl) {
                                download.href = dlUrl;
                                download.classList.remove('opacity-50', 'pointer-events-none');
                            } else {
                                download.href = '#';
                                download.classList.add('opacity-50', 'pointer-events-none');
                            }
                        }
                    }

                    select.addEventListener('change', update);
                    // Initialize once
                    update();
                });
                </script>
            <?php else: ?>
                <!-- No Video Available -->
                <div class="w-full h-full flex items-center justify-center bg-gray-900">
                    <div class="text-center text-white">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium">Video Tidak Tersedia</h3>
                        <p class="mt-1 text-gray-400">Video untuk episode ini belum tersedia</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Episode Info and Navigation -->
<section class="py-8 bg-gray-50 border-b">
    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Episode Info -->
            <div class="flex-1">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2"><?php echo $episode['title']; ?></h1>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="text-primary-600 hover:text-primary-700">
                            ← Kembali ke <?php echo $anime['title']; ?>
                        </a>
                        <span>•</span>
                        <span>Episode <?php echo $episode['episode_number']; ?></span>
                        <?php if ($episode['duration']): ?>
                            <span>•</span>
                            <span><?php echo $episode['duration']; ?> menit</span>
                        <?php endif; ?>
                        <span>•</span>
                        <span><?php echo number_format($episode['views']); ?> views</span>
                    </div>
                </div>
                
                <!-- Episode Navigation -->
                <div class="flex justify-between items-center">
                    <?php if ($prevEpisode): ?>
                        <a href="nonton.php?id=<?php echo $prevEpisode['id']; ?>" 
                           class="flex items-center px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Episode Sebelumnya
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                    
                    <?php if ($nextEpisode): ?>
                        <a href="nonton.php?id=<?php echo $nextEpisode['id']; ?>" 
                           class="flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors duration-200">
                            Episode Selanjutnya
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Episode List -->
            <div class="lg:w-80">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="font-medium text-gray-900">Daftar Episode</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($allEpisodes as $ep): ?>
                                <a href="nonton.php?id=<?php echo $ep['id']; ?>" 
                                   class="block px-4 py-3 hover:bg-gray-50 transition-colors duration-200 <?php echo ($ep['id'] == $episode['id']) ? 'bg-primary-50' : ''; ?>">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-sm <?php echo ($ep['id'] == $episode['id']) ? 'text-primary-600' : 'text-gray-900'; ?>">
                                                Episode <?php echo $ep['episode_number']; ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?php echo $ep['title']; ?></div>
                                        </div>
                                        <?php if ($ep['id'] == $episode['id']): ?>
                                            <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 1.414L10.586 9.5H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comments Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Komentar</h2>

            <?php
            // Fetch approved comments for this episode
            $comments = [];
            try {
                $stmtC = $db->prepare("
                    SELECT c.*, COALESCE(u.username, 'Anonymous') AS username
                    FROM comments c
                    LEFT JOIN users u ON u.id = c.user_id
                    WHERE c.anime_id = :aid AND c.episode_id = :eid AND c.status = 'approved'
                    ORDER BY c.created_at DESC
                ");
                $stmtC->execute([':aid' => $anime['id'], ':eid' => $episode['id']]);
                $comments = $stmtC->fetchAll();
            } catch (Exception $e) {
                $comments = [];
            }
            ?>

            <!-- Comment Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tinggalkan Komentar</h3>
                <form class="space-y-4" method="POST" action="add-comment.php">
                    <input type="hidden" name="anime_id" value="<?php echo (int)$anime['id']; ?>">
                    <input type="hidden" name="episode_id" value="<?php echo (int)$episode['id']; ?>">
                    <div>
                        <textarea name="content" rows="3" placeholder="Tulis komentar kamu..." class="input-field" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary">
                            Kirim Komentar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Comments List -->
            <div class="space-y-6">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $c): ?>
                        <?php
                        $username = htmlspecialchars($c['username'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8');
                        $initial  = mb_strtoupper(mb_substr($username, 0, 1, 'UTF-8'), 'UTF-8');
                        $created  = !empty($c['created_at']) ? date('d M Y, H:i', strtotime($c['created_at'])) : '';
                        $content  = htmlspecialchars($c['content'] ?? '', ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-start space-x-4">
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium"><?php echo $initial; ?></span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-900"><?php echo $username; ?></h4>
                                        <?php if ($created): ?>
                                            <span class="text-sm text-gray-500">• <?php echo $created; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mt-2 text-gray-700"><?php echo nl2br($content); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
                        Belum ada komentar. Jadilah yang pertama berkomentar!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related Episodes -->
<?php if (!empty($allEpisodes) && count($allEpisodes) > 1): ?>
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Episode Lainnya</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($allEpisodes as $related): ?>
                <?php if ($related['id'] != $episode['id']): ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                        <a href="nonton.php?id=<?php echo $related['id']; ?>" class="block">
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold">Episode <?php echo $related['episode_number']; ?></h3>
                                    <span class="text-xs text-gray-500"><?php echo number_format($related['views']); ?> views</span>
                                </div>
                                <p class="text-sm text-gray-600"><?php echo $related['title']; ?></p>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize video player
    initVideoPlayer();
    
    // Auto-play next episode (optional)
    const videoPlayer = document.getElementById('videoPlayer');
    if (videoPlayer) {
        videoPlayer.addEventListener('ended', function() {
            <?php if ($nextEpisode): ?>
            if (confirm('Episode selesai. Apakah ingin melanjutkan ke episode selanjutnya?')) {
                window.location.href = 'nonton.php?id=<?php echo $nextEpisode['id']; ?>';
            }
            <?php endif; ?>
        });
    }
});
</script>

<?php include 'templates/footer.php'; ?>

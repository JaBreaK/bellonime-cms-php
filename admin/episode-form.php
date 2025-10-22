<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo isset($episode) ? 'Edit Episode' : 'Tambah Episode'; ?>
        </h3>
        <?php if (isset($anime)): ?>
            <p class="text-sm text-gray-500 mt-1">Anime: <?php echo $anime['title']; ?></p>
        <?php endif; ?>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Anime Selection -->
                <div>
                    <label for="anime_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Anime <span class="text-red-500">*</span>
                    </label>
                    <select id="anime_id" name="anime_id" required class="input-field">
                        <option value="">Pilih Anime</option>
                        <?php
                        $animes = getAllAnimes();
                        foreach ($animes as $a):
                            $selected = (isset($episode['anime_id']) && $episode['anime_id'] == $a['id']) || 
                                       (isset($anime['id']) && $anime['id'] == $a['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $a['id']; ?>" <?php echo $selected; ?>><?php echo $a['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Episode Number -->
                <div>
                    <label for="episode_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Episode <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="episode_number" name="episode_number" min="1" required
                           value="<?php echo $episode['episode_number'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan nomor episode">
                </div>
                
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Episode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo $episode['title'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan judul episode">
                </div>
                
                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Durasi (menit)
                    </label>
                    <input type="number" id="duration" name="duration" min="0"
                           value="<?php echo $episode['duration'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan durasi episode">
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Video URL -->
                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Video URL
                    </label>
                    <input type="url" id="video_url" name="video_url"
                           value="<?php echo $episode['video_url'] ?? ''; ?>"
                           class="input-field" placeholder="https://example.com/video.mp4">
                    <p class="mt-1 text-sm text-gray-500">Link langsung ke file video (.mp4, .webm, dll)</p>
                </div>
                
                <!-- Video Embed -->
                <div>
                    <label for="video_embed" class="block text-sm font-medium text-gray-700 mb-2">
                        Video Embed Code
                    </label>
                    <textarea id="video_embed" name="video_embed" rows="6"
                              class="input-field" placeholder="<iframe src='...' ...></iframe>"><?php echo $episode['video_embed'] ?? ''; ?></textarea>
                    <p class="mt-1 text-sm text-gray-500">Kode embed dari server video (Youtube, Dailymotion, dll)</p>
                </div>
                
                <!-- Video Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preview Video
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <?php if (isset($episode['video_url']) && $episode['video_url']): ?>
                            <video controls class="w-full max-h-64" preload="metadata">
                                <source src="<?php echo $episode['video_url']; ?>" type="video/mp4">
                                Browser Anda tidak mendukung video tag.
                            </video>
                        <?php elseif (isset($episode['video_embed']) && $episode['video_embed']): ?>
                            <div class="embed-responsive embed-responsive-16by9">
                                <?php echo $episode['video_embed']; ?>
                            </div>
                        <?php else: ?>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Preview akan muncul setelah menyimpan</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Petunjuk Video:</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>Video URL:</strong> Gunakan link langsung ke file video untuk streaming langsung dari server</li>
                <li>• <strong>Video Embed:</strong> Gunakan kode embed dari platform video pihak ketiga (Youtube, Dailymotion, dll)</li>
                <li>• Prioritaskan menggunakan Video URL untuk pengalaman streaming yang lebih baik</li>
                <li>• Format video yang direkomendasikan: MP4 (H.264), WebM</li>
            </ul>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex justify-between">
            <div>
                <?php if (!isset($episode)): ?>
                    <button type="submit" name="add_more" value="1" class="btn-secondary">
                        Simpan & Tambah Lagi
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex space-x-4">
                <?php
                $cancelUrl = 'manage-episode.php';
                if (isset($episode)) {
                    $cancelUrl .= '?anime_id=' . $episode['anime_id'];
                } elseif (isset($anime)) {
                    $cancelUrl .= '?anime_id=' . $anime['id'];
                }
                ?>
                <a href="<?php echo $cancelUrl; ?>" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <?php echo isset($episode) ? 'Update Episode' : 'Simpan Episode'; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-fill anime title and next episode when anime is selected,
// and keep title in sync when episode number changes.
document.addEventListener('DOMContentLoaded', function() {
    const animeSelect = document.getElementById('anime_id');
    const epInput = document.getElementById('episode_number');
    const titleInput = document.getElementById('title');

    function buildTitle() {
        const animeTitle = animeSelect.options[animeSelect.selectedIndex]?.text || '';
        const epNum = epInput.value || '';
        if (animeTitle && epNum) {
            titleInput.value = `${animeTitle} - Episode ${epNum}`;
        }
    }

    animeSelect.addEventListener('change', function() {
        const animeId = this.value;
        if (animeId) {
            // Get next episode number for selected anime
            fetch(`get-next-episode.php?anime_id=${animeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.nextEpisode) {
                        epInput.value = data.nextEpisode;
                        buildTitle();
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });

    // Update title when episode number changes
    epInput.addEventListener('input', buildTitle);

    // Initialize if anime is preselected (e.g., when coming from filtered list)
    if (animeSelect.value) {
        animeSelect.dispatchEvent(new Event('change'));
    } else {
        buildTitle();
    }
});
</script>
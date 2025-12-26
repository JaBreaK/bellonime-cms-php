<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo isset($episode) ? 'Edit Episode' : 'Tambah Episode'; ?>
        </h3>
        <?php if (isset($anime)): ?>
            <p class="text-sm text-gray-500 mt-1">Anime: <?php echo $anime['title']; ?></p>
        <?php endif; ?>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="p-6" id="episode-form">
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
                <!-- Video File Upload -->
                <div>
                    <label for="video_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Video File (Lokal)
                    </label>
                    <input type="file" id="video_file" name="video_file" accept="video/*" class="input-field">
                    <p class="mt-1 text-sm text-gray-500">Upload file video lokal (MP4, WEBM, MKV, MOV)</p>
                    <p id="file-size-info" class="mt-1 text-sm text-blue-600 hidden"></p>
                    
                    <?php if (isset($episode['video']) && $episode['video']): ?>
                        <div class="mt-2 p-3 bg-gray-50 rounded">
                            <p class="text-sm text-gray-700">Current video: <span class="font-medium"><?= basename($episode['video']) ?></span></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Video Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preview Video
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <?php if (isset($episode['video']) && $episode['video']): ?>
                            <video controls class="w-full max-h-64 rounded">
                                <source src="<?= getLocalVideoUrl($episode['video']) ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Preview akan muncul setelah upload video.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Petunjuk Video:</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Upload file video lokal dari komputer Anda</li>
                <li>• Format yang didukung: MP4, WEBM, MKV, MOV</li>
                <li>• Pastikan ukuran file tidak melebihi batas upload server PHP Anda</li>
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

<!-- Loading Overlay with Progress -->
<div id="upload-loading" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Uploading Video...</h3>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-4 mb-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            
            <!-- Progress Text -->
            <div class="space-y-2 text-sm">
                <p class="text-gray-700">
                    <span id="progress-percent" class="font-bold text-blue-600">0%</span> complete
                </p>
                <p class="text-gray-500">
                    <span id="progress-uploaded" class="font-medium">0 MB</span> / 
                    <span id="progress-total" class="font-medium">0 MB</span>
                </p>
            </div>
            
            <p class="text-xs text-gray-400 mt-4">Jangan tutup halaman ini.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const animeSelect = document.getElementById('anime_id');
    const epInput = document.getElementById('episode_number');
    const titleInput = document.getElementById('title');
    const videoInput = document.getElementById('video_file');
    const fileSizeInfo = document.getElementById('file-size-info');
    const form = document.getElementById('episode-form');
    const loadingOverlay = document.getElementById('upload-loading');
    const progressBar = document.getElementById('progress-bar');
    const progressPercent = document.getElementById('progress-percent');
    const progressUploaded = document.getElementById('progress-uploaded');
    const progressTotal = document.getElementById('progress-total');
    const isEditing = <?php echo isset($episode) ? 'true' : 'false'; ?>;

    function buildTitle() {
        const animeTitle = animeSelect.options[animeSelect.selectedIndex]?.text || '';
        const epNum = epInput.value || '';
        if (animeTitle && epNum) {
            titleInput.value = `${animeTitle} - Episode ${epNum}`;
        }
    }

    // Format file size in MB
    function formatMB(bytes) {
        return (bytes / (1024 * 1024)).toFixed(2);
    }

    // Show file size when video selected
    videoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const sizeMB = formatMB(file.size);
            fileSizeInfo.textContent = `Selected: ${file.name} (${sizeMB} MB)`;
            fileSizeInfo.classList.remove('hidden');
            
            if (file.size > 500 * 1024 * 1024) {
                fileSizeInfo.classList.add('text-red-600');
                fileSizeInfo.classList.remove('text-blue-600');
                fileSizeInfo.textContent += ' - Warning: File sangat besar';
            } else {
                fileSizeInfo.classList.add('text-blue-600');
                fileSizeInfo.classList.remove('text-red-600');
            }
        } else {
            fileSizeInfo.classList.add('hidden');
        }
    });

    // Handle form submit with AJAX progress
    form.addEventListener('submit', function(e) {
        if (videoInput.files && videoInput.files.length > 0) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            
            // Show overlay
            loadingOverlay.classList.remove('hidden');
            const totalSize = videoInput.files[0].size;
            progressTotal.textContent = formatMB(totalSize) + ' MB';
            
            // Upload progress
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percent + '%';
                    progressPercent.textContent = percent + '%';
                    progressUploaded.textContent = formatMB(e.loaded) + ' MB';
                }
            });
            
            // Upload complete
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    window.location.href = xhr.responseURL || window.location.href;
                } else {
                    alert('Upload failed');
                    loadingOverlay.classList.add('hidden');
                }
            });
            
            // Upload error
            xhr.addEventListener('error', function() {
                alert('Upload error');
                loadingOverlay.classList.add('hidden');
            });
            
            xhr.open('POST', form.action || window.location.href);
            xhr.send(formData);
        }
    });

    animeSelect.addEventListener('change', function() {
        const animeId = this.value;
        if (!isEditing && animeId && !epInput.value) {
            fetch(`get-next-episode.php?anime_id=${animeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.nextEpisode) {
                        epInput.value = data.nextEpisode;
                    }
                    buildTitle();
                })
                .catch(error => console.error('Error:', error));
        } else {
            buildTitle();
        }
    });

    epInput.addEventListener('input', buildTitle);

    if (animeSelect.value) {
        if (!isEditing && !epInput.value) {
            animeSelect.dispatchEvent(new Event('change'));
        } else {
            buildTitle();
        }
    } else {
        buildTitle();
    }
});
</script>
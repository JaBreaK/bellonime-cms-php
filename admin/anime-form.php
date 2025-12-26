<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?= isset($anime) ? 'Edit Anime' : 'Tambah Anime' ?>
        </h3>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Anime <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?= $anime['title'] ?? '' ?>"
                           class="input-field" placeholder="Masukkan judul anime">
                </div>
                
                <!-- Synopsis -->
                <div>
                    <label for="synopsis" class="block text-sm font-medium text-gray-700 mb-2">
                        Sinopsis
                    </label>
                    <textarea id="synopsis" name="synopsis" rows="4"
                              class="input-field" placeholder="Masukkan sinopsis anime"><?= $anime['synopsis'] ?? '' ?></textarea>
                </div>
                
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe
                    </label>
                    <select id="type" name="type" class="input-field">
                        <option value="TV" <?= (isset($anime['type']) && $anime['type'] === 'TV') ? 'selected' : '' ?>>TV</option>
                        <option value="Movie" <?= (isset($anime['type']) && $anime['type'] === 'Movie') ? 'selected' : '' ?>>Movie</option>
                        <option value="OVA" <?= (isset($anime['type']) && $anime['type'] === 'OVA') ? 'selected' : '' ?>>OVA</option>
                        <option value="ONA" <?= (isset($anime['type']) && $anime['type'] === 'ONA') ? 'selected' : '' ?>>ONA</option>
                        <option value="Special" <?= (isset($anime['type']) && $anime['type'] === 'Special') ? 'selected' : '' ?>>Special</option>
                    </select>
                </div>
                
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status
                    </label>
                    <select id="status" name="status" class="input-field">
                        <option value="Ongoing" <?= (isset($anime['status']) && $anime['status'] === 'Ongoing') ? 'selected' : '' ?>>Ongoing</option>
                        <option value="Complete" <?= (isset($anime['status']) && $anime['status'] === 'Complete') ? 'selected' : '' ?>>Complete</option>
                        <option value="Upcoming" <?= (isset($anime['status']) && $anime['status'] === 'Upcoming') ? 'selected' : '' ?>>Upcoming</option>
                    </select>
                </div>
                
                <!-- Studio -->
                <div>
                    <label for="studio" class="block text-sm font-medium text-gray-700 mb-2">
                        Studio
                    </label>
                    <input type="text" id="studio" name="studio"
                           value="<?= $anime['studio'] ?? '' ?>"
                           class="input-field" placeholder="Masukkan nama studio">
                </div>
                
                <!-- Year -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun
                    </label>
                    <input type="number" id="year" name="year" min="1900" max="<?= date('Y') + 5 ?>"
                           value="<?= $anime['year'] ?? date('Y') ?>"
                           class="input-field" placeholder="Masukkan tahun rilis">
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Poster File -->
                <div>
                    <label for="poster_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Poster (Gambar Lokal)
                    </label>
                    <input type="file" id="poster_file" name="poster_file" accept="image/*"
                           class="input-field">
                    <p class="mt-1 text-sm text-gray-500">Upload gambar poster (JPG, PNG, GIF, WEBP)</p>
                    
                    <?php if (isset($anime['poster_file']) && $anime['poster_file']): ?>
                        <div class="mt-2">
                            <img id="posterPreview" src="<?= getImageUrl($anime['poster_file']) ?>"
                                 alt="Poster Preview" class="h-32 w-24 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1">Current: <?= basename($anime['poster_file']) ?></p>
                        </div>
                    <?php elseif (isset($anime['poster']) && $anime['poster']): ?>
                        <div class="mt-2">
                            <img id="posterPreview" src="<?= getImageUrl($anime['poster']) ?>"
                                 alt="Poster Preview" class="h-32 w-24 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1">Current: External URL</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Background File -->
                <div>
                    <label for="background_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Background (Gambar Lokal)
                    </label>
                    <input type="file" id="background_file" name="background_file" accept="image/*"
                           class="input-field">
                    <p class="mt-1 text-sm text-gray-500">Upload gambar background (JPG, PNG, GIF, WEBP)</p>
                    
                    <?php if (isset($anime['background_file']) && $anime['background_file']): ?>
                        <div class="mt-2">
                            <img id="backgroundPreview" src="<?= getImageUrl($anime['background_file']) ?>"
                                 alt="Background Preview" class="h-32 w-56 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1">Current: <?= basename($anime['background_file']) ?></p>
                        </div>
                    <?php elseif (isset($anime['background']) && $anime['background']): ?>
                        <div class="mt-2">
                            <img id="backgroundPreview" src="<?= getImageUrl($anime['background']) ?>"
                                 alt="Background Preview" class="h-32 w-56 object-cover rounded">
                            <p class="text-xs text-gray-500 mt-1">Current: External URL</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Total Episodes -->
                <div>
                    <label for="total_episodes" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Episode
                    </label>
                    <input type="number" id="total_episodes" name="total_episodes" min="0"
                           value="<?= $anime['total_episodes'] ?? 0 ?>"
                           class="input-field" placeholder="Masukkan total episode">
                </div>
                
                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Durasi (menit)
                    </label>
                    <input type="number" id="duration" name="duration" min="0"
                           value="<?= $anime['duration'] ?? 0 ?>"
                           class="input-field" placeholder="Masukkan durasi per episode">
                </div>
                
                <!-- Rating -->
                <div>
                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">
                        Rating
                    </label>
                    <input type="number" id="rating" name="rating" min="0" max="10" step="0.1"
                           value="<?= $anime['rating'] ?? 0 ?>"
                           class="input-field" placeholder="Masukkan rating (0-10)">
                </div>
                
                <!-- Season -->
                <div>
                    <label for="season" class="block text-sm font-medium text-gray-700 mb-2">
                        Musim
                    </label>
                    <select id="season" name="season" class="input-field">
                        <option value="">Pilih Musim</option>
                        <option value="Spring" <?= (isset($anime['season']) && $anime['season'] === 'Spring') ? 'selected' : '' ?>>Spring</option>
                        <option value="Summer" <?= (isset($anime['season']) && $anime['season'] === 'Summer') ? 'selected' : '' ?>>Summer</option>
                        <option value="Fall" <?= (isset($anime['season']) && $anime['season'] === 'Fall') ? 'selected' : '' ?>>Fall</option>
                        <option value="Winter" <?= (isset($anime['season']) && $anime['season'] === 'Winter') ? 'selected' : '' ?>>Winter</option>
                    </select>
                </div>
                
                <!-- Featured -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="featured" value="1" 
                               <?= (isset($anime['featured']) && $anime['featured']) ? 'checked' : '' ?>
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Tampilkan di halaman utama (Featured)</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Genres -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Genre
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <?php
                $genres = getAllGenres();
                foreach ($genres as $genre):
                    $checked = isset($selectedGenres) && in_array($genre['id'], $selectedGenres);
                ?>
                    <label class="flex items-center">
                        <input type="checkbox" name="genres[]" value="<?= $genre['id'] ?>"
                               <?= $checked ? 'checked' : '' ?>
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700"><?= $genre['name'] ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="manage-anime.php" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <?= isset($anime) ? 'Update Anime' : 'Simpan Anime' ?>
            </button>
        </div>
    </form>
</div>

<script>
// Live preview for Poster/Background URL inputs
document.addEventListener('DOMContentLoaded', function() {
    const posterInput = document.getElementById('poster_url');
    const backgroundInput = document.getElementById('background_url');
    const posterPreview = document.getElementById('posterPreview');
    const backgroundPreview = document.getElementById('backgroundPreview');

    function updatePreview(input, imgEl) {
        const url = input.value.trim();
        if (url) {
            imgEl.src = url;
        }
    }

    if (posterInput && posterPreview) {
        posterInput.addEventListener('input', function() {
            updatePreview(posterInput, posterPreview);
        });
    }

    if (backgroundInput && backgroundPreview) {
        backgroundInput.addEventListener('input', function() {
            updatePreview(backgroundInput, backgroundPreview);
        });
    }
});
</script>
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo isset($genre) ? 'Edit Genre' : 'Tambah Genre'; ?>
        </h3>
    </div>
    
    <form method="POST" class="p-6">
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Genre <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       value="<?php echo $genre['name'] ?? ''; ?>"
                       class="input-field" placeholder="Masukkan nama genre">
                <p class="mt-1 text-sm text-gray-500">Contoh: Action, Comedy, Drama</p>
            </div>
            
            <!-- Slug (Auto-generated) -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug
                </label>
                <input type="text" id="slug" name="slug" readonly
                       value="<?php echo $genre['slug'] ?? ''; ?>"
                       class="input-field bg-gray-50" placeholder="Slug akan otomatis dibuat">
                <p class="mt-1 text-sm text-gray-500">Slug akan otomatis dibuat dari nama genre</p>
            </div>
            
            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="4"
                          class="input-field" placeholder="Masukkan deskripsi genre"><?php echo $genre['description'] ?? ''; ?></textarea>
                <p class="mt-1 text-sm text-gray-500">Deskripsi opsional untuk menjelaskan genre ini</p>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="manage-genre.php" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                <?php echo isset($genre) ? 'Update Genre' : 'Simpan Genre'; ?>
            </button>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .replace(/-+/g, '-') // Replace multiple hyphens with single hyphen
        .trim(); // Remove leading/trailing spaces and hyphens
    
    document.getElementById('slug').value = slug;
});
</script>
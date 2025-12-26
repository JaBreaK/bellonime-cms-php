<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Daftar Episode</h3>
    </div>
    <div class="p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari episode atau anime..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       class="input-field">
            </div>
            <div>
                <select name="anime_id" class="input-field">
                    <option value="">Semua Anime</option>
                    <?php foreach ($animes as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= (($_GET['anime_id'] ?? '') == $a['id']) ? 'selected' : '' ?>>
                            <?= $a['title'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-primary">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </div>
            <div>
                <a href="manage-episode.php?action=create<?= (isset($_GET['anime_id']) && $_GET['anime_id'] !== '') ? '&anime_id=' . urlencode($_GET['anime_id']) : '' ?>"
                   class="btn-primary">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Episode
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Episode Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Anime
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Episode
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Judul
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Durasi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Kualitas
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Views
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal Upload
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($result['data'])): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada episode ditemukan
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result['data'] as $episode): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="<?= getImageUrl($episode['poster'] ?? '') ?>"
                                         alt="<?= $episode['anime_title'] ?>"
                                         class="h-10 w-10 object-cover rounded mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $episode['anime_title'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Episode <?= $episode['episode_number'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= $episode['title'] ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $episode['duration'] ?> menit
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <?php if (!empty($episode['embed_480_url'])): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            480p
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($episode['embed_720_url'])): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            720p
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($episode['embed_1080_url'])): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            1080p
                                        </span>
                                    <?php endif; ?>
                                    <?php if (empty($episode['embed_480_url']) && empty($episode['embed_720_url']) && empty($episode['embed_1080_url'])): ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= number_format($episode['views']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d M Y, H:i', strtotime($episode['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="manage-episode.php?action=edit&id=<?= $episode['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="<?= BASE_URL ?>nonton.php?id=<?= $episode['id'] ?>" 
                                       target="_blank" 
                                       class="text-green-600 hover:text-green-900" title="Preview">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="manage-episode.php?action=delete&id=<?= $episode['id'] ?>" 
                                          onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus episode ini?')"
                                          class="inline">
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($result['totalPages'] > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($result['hasPrevPage']): ?>
                    <a href="?page=<?= $result['page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&anime_id=<?= $_GET['anime_id'] ?? '' ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php if ($result['hasNextPage']): ?>
                    <a href="?page=<?= $result['page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&anime_id=<?= $_GET['anime_id'] ?? '' ?>" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?= ($result['page'] - 1) * $result['perPage'] + 1 ?></span> to 
                        <span class="font-medium"><?= min($result['page'] * $result['perPage'], $result['total']) ?></span> of 
                        <span class="font-medium"><?= $result['total'] ?></span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($result['hasPrevPage']): ?>
                            <a href="?page=<?= $result['page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&anime_id=<?= $_GET['anime_id'] ?? '' ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $result['page'] - 2);
                        $endPage = min($result['totalPages'], $result['page'] + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&anime_id=<?= $_GET['anime_id'] ?? '' ?>" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $result['page'] ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($result['hasNextPage']): ?>
                            <a href="?page=<?= $result['page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&anime_id=<?= $_GET['anime_id'] ?? '' ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
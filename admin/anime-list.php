<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Daftar Anime</h3>
    </div>
    <div class="p-6">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari anime..." 
                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       class="input-field">
            </div>
            <div>
                <select name="status" class="input-field">
                    <option value="">Semua Status</option>
                    <option value="Ongoing" <?= (($_GET['status'] ?? '') === 'Ongoing') ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Complete" <?= (($_GET['status'] ?? '') === 'Complete') ? 'selected' : '' ?>>Complete</option>
                    <option value="Upcoming" <?= (($_GET['status'] ?? '') === 'Upcoming') ? 'selected' : '' ?>>Upcoming</option>
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
                <a href="manage-anime.php?action=create" class="btn-primary">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Anime
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Anime Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Poster
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Judul
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Episode
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rating
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Featured
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
                            Tidak ada anime ditemukan
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result['data'] as $anime): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="<?= getImageUrl($anime['poster'] ?? '') ?>"
                                     alt="<?= $anime['title'] ?>" 
                                     class="h-16 w-12 object-cover rounded">
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?= $anime['title'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $anime['studio'] ?></div>
                                    <div class="text-xs text-gray-400"><?= $anime['year'] ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= $anime['type'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColor = [
                                    'Ongoing' => 'bg-green-100 text-green-800',
                                    'Complete' => 'bg-gray-100 text-gray-800',
                                    'Upcoming' => 'bg-yellow-100 text-yellow-800'
                                ];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColor[$anime['status']] ?>">
                                    <?= $anime['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= $anime['episode_count'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <?= number_format($anime['rating'], 1) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="manage-anime.php?action=toggle-featured&id=<?= $anime['id'] ?>" 
                                      onsubmit="return confirm('Ubah status featured?')">
                                    <button type="submit" class="text-<?= $anime['featured'] ? 'yellow' : 'gray' ?>-500 hover:text-<?= $anime['featured'] ? 'yellow' : 'gray' ?>-700">
                                        <svg class="h-6 w-6" fill="<?= $anime['featured'] ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="manage-anime.php?action=edit&id=<?= $anime['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="manage-episode.php?anime_id=<?= $anime['id'] ?>" 
                                       class="text-green-600 hover:text-green-900" title="Manage Episodes">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="manage-anime.php?action=delete&id=<?= $anime['id'] ?>" 
                                          onsubmit="return confirmDelete('Apakah Anda yakin ingin menghapus anime ini? Semua episode terkait juga akan dihapus.')"
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
                    <a href="?page=<?= $result['page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= $_GET['status'] ?? '' ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php if ($result['hasNextPage']): ?>
                    <a href="?page=<?= $result['page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= $_GET['status'] ?? '' ?>" 
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
                            <a href="?page=<?= $result['page'] - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= $_GET['status'] ?? '' ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $result['page'] - 2);
                        $endPage = min($result['totalPages'], $result['page'] + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= $_GET['status'] ?? '' ?>" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $result['page'] ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($result['hasNextPage']): ?>
                            <a href="?page=<?= $result['page'] + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= $_GET['status'] ?? '' ?>" 
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
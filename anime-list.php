<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$pageTitle = 'Daftar Anime';
$pageDescription = 'Daftar lengkap anime streaming subtitle Indonesia';

// Get filter parameters
$page = (int)($_GET['page'] ?? 1);
$perPage = 12;
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$type = $_GET['type'] ?? '';
$genre = $_GET['genre'] ?? '';
$year = $_GET['year'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// Build query
$sql = "SELECT a.*, 
        (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count,
        GROUP_CONCAT(g.name) as genres
        FROM animes a
        LEFT JOIN anime_genre ag ON a.id = ag.anime_id
        LEFT JOIN genres g ON ag.genre_id = g.id";

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "a.title LIKE :search";
    $params[':search'] = "%$search%";
}

if (!empty($status)) {
    $conditions[] = "a.status = :status";
    $params[':status'] = $status;
}

if (!empty($type)) {
    $conditions[] = "a.type = :type";
    $params[':type'] = $type;
}

if (!empty($genre)) {
    $conditions[] = "a.id IN (SELECT anime_id FROM anime_genre WHERE genre_id = (SELECT id FROM genres WHERE slug = :genre))";
    $params[':genre'] = $genre;
}

if (!empty($year)) {
    $conditions[] = "a.year = :year";
    $params[':year'] = $year;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

// Add sorting
switch ($sort) {
    case 'popular':
        $sql .= " GROUP BY a.id ORDER BY a.views DESC, a.rating DESC";
        break;
    case 'rating':
        $sql .= " GROUP BY a.id ORDER BY a.rating DESC";
        break;
    case 'title':
        $sql .= " GROUP BY a.id ORDER BY a.title ASC";
        break;
    case 'newest':
    default:
        $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";
        break;
}

// Get paginated results
$result = paginate($sql, $page, $perPage, $params);

// Get all genres for filter
$allGenres = getAllGenres();

// Get available years
$years = [];
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT DISTINCT year FROM animes WHERE year IS NOT NULL ORDER BY year DESC");
$stmt->execute();
$years = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include 'templates/header.php'; ?>

<!-- Page Header -->
<section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Daftar Anime</h1>
        <p class="text-lg opacity-90">Temukan anime favorit kamu dari berbagai genre</p>
    </div>
</section>

<!-- Filters -->
<section class="py-8 bg-gray-50 border-b">
    <div class="container mx-auto px-4">
        <form method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Cari anime..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="input-field">
                </div>
                <button type="submit" class="btn-primary">
                    <svg class="h-5 w-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </div>
            
            <!-- Filter Options -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="input-field">
                        <option value="">Semua Status</option>
                        <option value="Ongoing" <?php echo ($status === 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                        <option value="Complete" <?php echo ($status === 'Complete') ? 'selected' : ''; ?>>Complete</option>
                        <option value="Upcoming" <?php echo ($status === 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" class="input-field">
                        <option value="">Semua Tipe</option>
                        <option value="TV" <?php echo ($type === 'TV') ? 'selected' : ''; ?>>TV</option>
                        <option value="Movie" <?php echo ($type === 'Movie') ? 'selected' : ''; ?>>Movie</option>
                        <option value="OVA" <?php echo ($type === 'OVA') ? 'selected' : ''; ?>>OVA</option>
                        <option value="ONA" <?php echo ($type === 'ONA') ? 'selected' : ''; ?>>ONA</option>
                        <option value="Special" <?php echo ($type === 'Special') ? 'selected' : ''; ?>>Special</option>
                    </select>
                </div>
                
                <!-- Genre Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Genre</label>
                    <select name="genre" class="input-field">
                        <option value="">Semua Genre</option>
                        <?php foreach ($allGenres as $g): ?>
                            <option value="<?php echo $g['slug']; ?>" <?php echo ($genre === $g['slug']) ? 'selected' : ''; ?>>
                                <?php echo $g['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Year Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                    <select name="year" class="input-field">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>>
                                <?php echo $y; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Sort Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select name="sort" class="input-field">
                        <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="popular" <?php echo ($sort === 'popular') ? 'selected' : ''; ?>>Populer</option>
                        <option value="rating" <?php echo ($sort === 'rating') ? 'selected' : ''; ?>>Rating Tertinggi</option>
                        <option value="title" <?php echo ($sort === 'title') ? 'selected' : ''; ?>>Judul (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <!-- Clear Filters -->
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Menampilkan <?php echo ($result['page'] - 1) * $result['perPage'] + 1; ?> - 
                    <?php echo min($result['page'] * $result['perPage'], $result['total']); ?> 
                    dari <?php echo $result['total']; ?> anime
                </div>
                <?php if (!empty($search) || !empty($status) || !empty($type) || !empty($genre) || !empty($year) || $sort !== 'newest'): ?>
                    <a href="anime-list.php" class="text-sm text-red-600 hover:text-red-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Hapus Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<!-- Anime Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <?php if (empty($result['data'])): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada anime ditemukan</h3>
                <p class="mt-1 text-gray-500">Coba ubah filter atau kata kunci pencarian</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
                <?php foreach ($result['data'] as $anime): ?>
                    <div class="group bg-white rounded-xl  overflow-hidden  transition-all duration-300">
                        <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="block">
                            <div class="relative aspect-2-3 bg-gray-200 overflow-hidden">
                                <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                                     alt="<?php echo $anime['title']; ?>"
                                     class="img-fill">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                                <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-xs">
                                    <div class="flex items-center text-white/90">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <?php echo number_format($anime['rating'], 1); ?>
                                    </div>
                                    <span class="px-2 py-1 rounded bg-blue-600/80 text-white">
                                        <?php echo $anime['episode_count']; ?> Episode
                                    </span>
                                </div>
                            </div>
                            <h3 class="mt-3 px-1 text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                                <?php echo $anime['title']; ?>
                                <span class="block h-px bg-gradient-to-r from-primary-500/0 via-primary-500/60 to-primary-500/0 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left mt-1"></span>
                            </h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($result['totalPages'] > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="relative z-0 inline-flex rounded-md  -space-x-px" aria-label="Pagination">
                        <?php if ($result['hasPrevPage']): ?>
                            <a href="?page=<?php echo $result['page'] - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&genre=<?php echo $genre; ?>&year=<?php echo $year; ?>&sort=<?php echo $sort; ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $startPage = max(1, $result['page'] - 2);
                        $endPage = min($result['totalPages'], $result['page'] + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&genre=<?php echo $genre; ?>&year=<?php echo $year; ?>&sort=<?php echo $sort; ?>" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $result['page'] ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($result['hasNextPage']): ?>
                            <a href="?page=<?php echo $result['page'] + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&type=<?php echo $type; ?>&genre=<?php echo $genre; ?>&year=<?php echo $year; ?>&sort=<?php echo $sort; ?>" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'templates/footer.php'; ?>
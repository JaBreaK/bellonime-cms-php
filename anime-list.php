<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 24;
$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$status = $_GET['status'] ?? '';
$type = $_GET['type'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

$db = Database::getInstance()->getConnection();

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "a.title LIKE :search";
    $params[':search'] = "%$search%";
}

if ($status) {
    $where[] = "a.status = :status";
    $params[':status'] = $status;
}

if ($type) {
    $where[] = "a.type = :type";
    $params[':type'] = $type;
}

if ($genre) {
    $where[] = "a.id IN (SELECT anime_id FROM anime_genre ag JOIN genres g ON ag.genre_id = g.id WHERE g.slug = :genre)";
    $params[':genre'] = $genre;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$orderBy = match($sort) {
    'popular' => 'a.views DESC',
    'rating' => 'a.rating DESC',
    'title' => 'a.title ASC',
    default => 'a.created_at DESC'
};

// Get total
$countSql = "SELECT COUNT(*) FROM animes a $whereClause";
$stmt = $db->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get animes
$offset = ($page - 1) * $perPage;
$sql = "SELECT a.*, (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count 
        FROM animes a $whereClause ORDER BY $orderBy LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$animes = $stmt->fetchAll();

// Get all genres for filter
$allGenres = getAllGenres();

$pageTitle = 'Browse Anime';
?>
<?php include 'templates/header.php'; ?>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold mb-6">Browse Anime</h1>
        
        <!-- Filters -->
        <div class="bg-dark-700 rounded-xl p-4 mb-8">
            <form method="GET" class="flex flex-wrap gap-4">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search..." class="input-field flex-1 min-w-[200px]">
                
                <select name="genre" class="input-field w-40">
                    <option value="">All Genres</option>
                    <?php foreach ($allGenres as $g): ?>
                        <option value="<?= $g['slug'] ?>" <?= $genre == $g['slug'] ? 'selected' : '' ?>><?= $g['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="status" class="input-field w-36">
                    <option value="">All Status</option>
                    <option value="Ongoing" <?= $status == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                    <option value="Completed" <?= $status == 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                
                <select name="type" class="input-field w-32">
                    <option value="">All Types</option>
                    <option value="TV" <?= $type == 'TV' ? 'selected' : '' ?>>TV</option>
                    <option value="Movie" <?= $type == 'Movie' ? 'selected' : '' ?>>Movie</option>
                    <option value="OVA" <?= $type == 'OVA' ? 'selected' : '' ?>>OVA</option>
                    <option value="ONA" <?= $type == 'ONA' ? 'selected' : '' ?>>ONA</option>
                </select>
                
                <select name="sort" class="input-field w-36">
                    <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
                    <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>Popular</option>
                    <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>Rating</option>
                    <option value="title" <?= $sort == 'title' ? 'selected' : '' ?>>A-Z</option>
                </select>
                
                <button type="submit" class="btn-primary">Filter</button>
            </form>
        </div>
        
        <!-- Results -->
        <?php if (empty($animes)): ?>
            <div class="text-center py-16 text-gray-400">
                <p class="text-lg">No anime found</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
                <?php foreach ($animes as $anime): ?>
                    <a href="detail.php?slug=<?= $anime['slug'] ?>" class="anime-card group">
                        <div class="aspect-poster relative">
                            <img src="<?= getImageUrl($anime['poster'] ?? '') ?>" alt="<?= htmlspecialchars($anime['title']) ?>" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                            <div class="absolute top-2 right-2">
                                <span class="badge badge-secondary text-xs"><?= $anime['episode_count'] ?> EP</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-3">
                                <h3 class="text-sm font-medium line-clamp-2"><?= htmlspecialchars($anime['title']) ?></h3>
                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-400">
                                    <span><?= $anime['type'] ?></span>
                                    <span>•</span>
                                    <span class="flex items-center">
                                        <svg class="w-3 h-3 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <?= number_format($anime['rating'] ?? 0, 1) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="flex justify-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="btn-secondary">Prev</a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="<?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="btn-secondary">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'templates/footer.php'; ?>
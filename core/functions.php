<?php
require_once 'connection.php';

// Anime functions
function getAllAnimes($limit = null, $offset = 0, $status = null, $featured = false) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count,
            COALESCE(GROUP_CONCAT(g.name SEPARATOR ', '), '') as genres
            FROM animes a
            LEFT JOIN anime_genre ag ON a.id = ag.anime_id
            LEFT JOIN genres g ON ag.genre_id = g.id";
    
    $conditions = [];
    $params = [];
    
    if ($status) {
        $conditions[] = "a.status = :status";
        $params[':status'] = $status;
    }
    
    if ($featured) {
        $conditions[] = "a.featured = 1";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

function getAnimeById($id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count
            FROM animes a 
            WHERE a.id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

function getAnimeBySlug($slug) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count
            FROM animes a 
            WHERE a.slug = :slug";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    
    return $stmt->fetch();
}

function getAnimeGenres($animeId) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT g.* FROM genres g
            JOIN anime_genre ag ON g.id = ag.genre_id
            WHERE ag.anime_id = :animeId
            ORDER BY g.name";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':animeId' => $animeId]);
    
    return $stmt->fetchAll();
}

function getAnimesByGenre($genreId, $page = 1, $perPage = 12) {
    $sql = "SELECT a.*, COUNT(DISTINCT e.id) as episode_count
            FROM animes a
            INNER JOIN anime_genre ag ON a.id = ag.anime_id
            LEFT JOIN episodes e ON a.id = e.anime_id
            WHERE ag.genre_id = :genre_id
            GROUP BY a.id
            ORDER BY a.created_at DESC";
    
    return paginate($sql, $page, $perPage, [':genre_id' => $genreId]);
}

function getPopularAnimes($limit = 10) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*, COUNT(DISTINCT e.id) as episode_count
            FROM animes a
            LEFT JOIN episodes e ON a.id = e.anime_id
            WHERE a.status != 'upcoming'
            GROUP BY a.id
            ORDER BY a.views DESC, a.created_at DESC
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function getFeaturedAnimes($limit = 6) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*, COUNT(DISTINCT e.id) as episode_count
            FROM animes a
            LEFT JOIN episodes e ON a.id = e.anime_id
            WHERE a.featured = 1
            GROUP BY a.id
            ORDER BY a.created_at DESC
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function searchAnimes($query, $page = 1, $perPage = 12) {
    $db = Database::getInstance()->getConnection();
    
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count,
            COALESCE(GROUP_CONCAT(g.name SEPARATOR ', '), '') as genres
            FROM animes a
            LEFT JOIN anime_genre ag ON a.id = ag.anime_id
            LEFT JOIN genres g ON ag.genre_id = g.id
            WHERE a.title LIKE :query_like OR a.synopsis LIKE :query_like
            GROUP BY a.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':query_like' => "%$query%",
        ':limit' => $perPage,
        ':offset' => $offset
    ]);
    
    return $stmt->fetchAll();
}

// Episode functions
function getAllEpisodes($animeId = null) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title 
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id";
    
    $params = [];
    
    if ($animeId) {
        $sql .= " WHERE e.anime_id = :animeId";
        $params[':animeId'] = $animeId;
    }
    
    $sql .= " ORDER BY e.anime_id, e.episode_number";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

function getEpisodeById($id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.poster, a.slug as anime_slug
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            WHERE e.id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

function getEpisodeBySlug($slug) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.poster, a.slug as anime_slug
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            WHERE e.slug = :slug";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    
    return $stmt->fetch();
}

function getEpisodesByAnimeId($animeId) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM episodes 
            WHERE anime_id = :anime_id 
            ORDER BY episode_number ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':anime_id' => $animeId]);
    
    return $stmt->fetchAll();
}

function getNextEpisode($animeId, $currentEpisode) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM episodes 
            WHERE anime_id = :animeId AND episode_number > :currentEpisode
            ORDER BY episode_number ASC 
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':animeId' => $animeId,
        ':currentEpisode' => $currentEpisode
    ]);
    
    return $stmt->fetch();
}

function getPreviousEpisode($animeId, $currentEpisode) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM episodes 
            WHERE anime_id = :animeId AND episode_number < :currentEpisode
            ORDER BY episode_number DESC 
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':animeId' => $animeId,
        ':currentEpisode' => $currentEpisode
    ]);
    
    return $stmt->fetch();
}

function getLatestEpisodes($limit = 10) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.poster, a.slug as anime_slug
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            ORDER BY e.created_at DESC
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':limit' => (int)$limit]);
    
    return $stmt->fetchAll();
}

// Genre functions
function getAllGenres() {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT g.*, 
            (SELECT COUNT(*) FROM anime_genre WHERE genre_id = g.id) as anime_count
            FROM genres g 
            ORDER BY g.name";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

function getGenreBySlug($slug) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM genres WHERE slug = :slug";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    
    return $stmt->fetch();
}

function getGenreById($id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM genres WHERE id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

// Admin functions
function getAdminById($id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM admins WHERE id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

function getAdminByUsername($username) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM admins WHERE username = :username";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':username' => $username]);
    
    return $stmt->fetch();
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Statistics functions
function getDashboardStats() {
    $db = Database::getInstance()->getConnection();
    
    $stats = [];
    
    // Total anime
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM animes");
    $stmt->execute();
    $stats['total_animes'] = $stmt->fetch()['total'];
    
    // Total episodes
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM episodes");
    $stmt->execute();
    $stats['total_episodes'] = $stmt->fetch()['total'];
    
    // Total genres
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM genres");
    $stmt->execute();
    $stats['total_genres'] = $stmt->fetch()['total'];
    
    // Ongoing anime
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM animes WHERE status = 'Ongoing'");
    $stmt->execute();
    $stats['ongoing_animes'] = $stmt->fetch()['total'];
    
    // Total views
    $stmt = $db->prepare("SELECT SUM(views) as total FROM animes");
    $stmt->execute();
    $stats['total_views'] = $stmt->fetch()['total'] ?? 0;
    
    return $stats;
}

/* ===================== Local File Upload Helpers ===================== */

/**
 * Upload image file (poster/background) to local storage
 */
function uploadImageFile($fileEntry, $uploadDir = 'uploads/posters') {
    if (!isset($fileEntry['error']) || $fileEntry['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $tmpName = $fileEntry['tmp_name'];
    $originalName = $fileEntry['name'];
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($tmpName);
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }
    
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '_' . time() . '.' . $extension;
    
    $targetDir = __DIR__ . '/../' . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $targetPath = $targetDir . '/' . $filename;
    if (move_uploaded_file($tmpName, $targetPath)) {
        return $uploadDir . '/' . $filename;
    }
    
    return false;
}

/**
 * Upload video file to local storage
 */
function uploadVideoFile($fileEntry, $uploadDir = 'uploads/videos') {
    if (!isset($fileEntry['error']) || $fileEntry['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $tmpName = $fileEntry['tmp_name'];
    $originalName = $fileEntry['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['mp4', 'webm', 'mkv', 'mov'];
    if (!in_array($extension, $allowedExtensions)) {
        return false;
    }
    
    $filename = uniqid('video_') . '_' . time() . '.' . $extension;
    
    $targetDir = __DIR__ . '/../' . $uploadDir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $targetPath = $targetDir . '/' . $filename;
    if (move_uploaded_file($tmpName, $targetPath)) {
        return $uploadDir . '/' . $filename;
    }
    
    return false;
}

/**
 * Get full URL for local video file
 */
function getLocalVideoUrl($filePath) {
    if (empty($filePath)) {
        return '';
    }
    
    // If it's already a full URL, return as is
    if (preg_match('/^https?:\/\//i', $filePath)) {
        return $filePath;
    }
    
    // Build full URL from server root
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Always use root path
    return $protocol . '://' . $host . '/' . ltrim($filePath, '/');
}

/**
 * Delete local file from server
 */
function deleteLocalFile($filePath) {
    if (empty($filePath)) {
        return false;
    }
    
    // Don't delete external URLs
    if (preg_match('/^https?:\/\//i', $filePath)) {
        return false;
    }
    
    $fullPath = __DIR__ . '/../' . ltrim($filePath, '/');
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}

/**
 * Get image URL - supports both local files and external URLs
 */
function getImageUrl($value) {
    $value = trim($value ?? '');
    if ($value === '') {
        return 'https://via.placeholder.com/300x450?text=No+Image';
    }
    
    // Check if it's a local file path (not a URL)
    if (!preg_match('/^https?:\/\//i', $value)) {
        // It's a local path - build full URL from server root
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Always use root path (don't use script directory)
        return $protocol . '://' . $host . '/' . ltrim($value, '/');
    }
    
    // It's an external URL - return as is
    return $value;
}

/**
 * Normalize embed input - extract iframe src if full iframe tag is pasted
 */
function normalizeEmbedInput($input) {
    $input = trim($input ?? '');
    if ($input === '') {
        return '';
    }
    
    // If user pasted full iframe tag, extract src
    if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/', $input, $matches)) {
        return $matches[1];
    }
    
    // Otherwise return as is (should be a URL)
    return $input;
}

/**
 * Check if embed URL is from allowed domain
 */
function isAllowedEmbedDomain($url) {
    $url = trim($url ?? '');
    if ($url === '') {
        return false;
    }
    
    $allowedDomains = [
        'youtube.com',
        'youtu.be',
        'dailymotion.com',
        'vimeo.com',
        'streamtape.com',
        'doodstream.com',
        'mixdrop.co',
        'upstream.to'
    ];
    
    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'] ?? '';
    
    foreach ($allowedDomains as $domain) {
        if (stripos($host, $domain) !== false) {
            return true;
        }
    }
    
    return false;
}

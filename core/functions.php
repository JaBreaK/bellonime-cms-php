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

function searchAnimes($query, $page = 1, $perPage = 12) {
    $db = Database::getInstance()->getConnection();
    
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count,
            COALESCE(GROUP_CONCAT(g.name SEPARATOR ', '), '') as genres,
            MATCH(a.title, a.synopsis) AGAINST(:query IN NATURAL LANGUAGE MODE) as relevance
            FROM animes a
            LEFT JOIN anime_genre ag ON a.id = ag.anime_id
            LEFT JOIN genres g ON ag.genre_id = g.id
            WHERE a.title LIKE :query_like OR a.synopsis LIKE :query_like
            GROUP BY a.id
            ORDER BY relevance DESC, a.created_at DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':query' => $query,
        ':query_like' => "%$query%",
        ':limit' => (int)$perPage,
        ':offset' => (int)$offset
    ]);
    
    $results = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM animes a
                 WHERE a.title LIKE :query OR a.synopsis LIKE :query";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute([':query' => "%$query%"]);
    $total = $countStmt->fetch()['total'];
    
    return [
        'data' => $results,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ];
}

function getAnimesByGenre($genreSlug, $page = 1, $perPage = 12) {
    $db = Database::getInstance()->getConnection();
    
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT a.*,
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count,
            COALESCE(GROUP_CONCAT(g.name SEPARATOR ', '), '') as genres
            FROM animes a
            JOIN anime_genre ag ON a.id = ag.anime_id
            JOIN genres g ON ag.genre_id = g.id
            WHERE g.slug = :genreSlug
            GROUP BY a.id
            ORDER BY a.created_at DESC
            LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':genreSlug' => $genreSlug,
        ':limit' => (int)$perPage,
        ':offset' => (int)$offset
    ]);
    
    $results = $stmt->fetchAll();
    
    // Get total count
    $countSql = "SELECT COUNT(DISTINCT a.id) as total FROM animes a
                 JOIN anime_genre ag ON a.id = ag.anime_id
                 JOIN genres g ON ag.genre_id = g.id
                 WHERE g.slug = :genreSlug";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute([':genreSlug' => $genreSlug]);
    $total = $countStmt->fetch()['total'];
    
    return [
        'data' => $results,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage)
    ];
}

function getPopularAnimes($limit = 10) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT a.*, 
            (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count
            FROM animes a 
            WHERE a.status = 'Ongoing' OR a.status = 'Complete'
            ORDER BY a.views DESC, a.rating DESC 
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':limit' => (int)$limit]);
    
    return $stmt->fetchAll();
}
/* Embed helpers: accept any http(s) URL or extract src from iframe */
function normalizeEmbedInput($input) {
    $input = trim($input ?? '');
    if ($input === '') {
        return '';
    }

    // If HTML iframe is provided, extract src attribute
    if (!filter_var($input, FILTER_VALIDATE_URL)) {
        if (preg_match('/src=["\']([^"\']+)["\']/i', $input, $m)) {
            $input = trim($m[1]);
        } else {
            // Not a valid URL nor iframe code
            return '';
        }
    }

    // Accept any http(s) URL
    $scheme = parse_url($input, PHP_URL_SCHEME);
    if (!in_array(strtolower($scheme), ['http', 'https'])) {
        return '';
    }

    return $input;
}

function isAllowedEmbedDomain($url) {
    // Allow any http(s) URL
    $scheme = parse_url($url, PHP_URL_SCHEME);
    return in_array(strtolower($scheme), ['http', 'https']);
}

function getLatestEpisodes($limit = 20) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.slug as anime_slug, a.poster
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            ORDER BY e.created_at DESC
            LIMIT :limit";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':limit' => (int)$limit]);
    
    return $stmt->fetchAll();
}

// Episode functions
function getEpisodesByAnimeId($animeId) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT * FROM episodes 
            WHERE anime_id = :animeId 
            ORDER BY episode_number ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':animeId' => $animeId]);
    
    return $stmt->fetchAll();
}

function getEpisodeById($id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.slug as anime_slug
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            WHERE e.id = :id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    return $stmt->fetch();
}

function getEpisodeBySlug($slug) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT e.*, a.title as anime_title, a.slug as anime_slug
            FROM episodes e
            JOIN animes a ON e.anime_id = a.id
            WHERE e.slug = :slug";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':slug' => $slug]);
    
    return $stmt->fetch();
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
/* Helper: resolve image URL from stored value (external only, no local folder refs) */
if (!function_exists('getImageUrl')) {
    function getImageUrl($value) {
        $value = trim($value ?? '');
        if ($value === '') {
            // External placeholder to avoid any local project image usage
            return 'https://via.placeholder.com/300x450?text=No+Image';
        }
        if (preg_match('/^https?:\\/\\//i', $value)) {
            return $value;
        }
        // Non-URL values are treated as missing; return external placeholder
        return 'https://via.placeholder.com/300x450?text=No+Image';
    }
}
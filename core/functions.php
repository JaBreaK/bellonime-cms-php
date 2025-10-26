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
 // Ensure episodes table has per-quality stream and download URL columns
if (!function_exists('ensureEpisodeQualityColumns')) {
    function ensureEpisodeQualityColumns() {
        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = 'episodes'");
            $stmt->execute([':schema' => DB_NAME]);
            $rows = $stmt->fetchAll();
            $existing = [];
            foreach ($rows as $r) {
                $existing[$r['COLUMN_NAME']] = true;
            }

            $columns = [
                // Per-quality EMBED URLs
                'embed_480_url' => 'VARCHAR(500)',
                'embed_720_url' => 'VARCHAR(500)',
                'embed_1080_url' => 'VARCHAR(500)',
                // Per-quality DOWNLOAD URLs
                'dl_480_url' => 'VARCHAR(500)',
                'dl_720_url' => 'VARCHAR(500)',
                'dl_1080_url' => 'VARCHAR(500)',
            ];

            foreach ($columns as $col => $type) {
                if (!isset($existing[$col])) {
                    // Add column if missing
                    $db->exec("ALTER TABLE episodes ADD COLUMN {$col} {$type} NULL");
                }
            }
        } catch (Throwable $e) {
            // Ignore errors silently to not break request flow
        }
    }
}
/* ===================== HXFile integration helpers (coba.php style) ===================== */
/* API key resolution: prefer env HXFILE_API_KEY, else constant HXFILE_API_KEY, else fallback from coba.php */
if (!function_exists('getHxfileApiKey')) {
    function getHxfileApiKey() {
        $env = getenv('HXFILE_API_KEY');
        if ($env && is_string($env) && trim($env) !== '') {
            return trim($env);
        }
        if (defined('HXFILE_API_KEY')) {
            $val = constant('HXFILE_API_KEY');
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }
        }
        // Fallback: value seen in coba.php (replace if needed)
        return '7658f4c4b8et4zqdlpko';
    }
}

/* Build embed and download URLs from filecode */
if (!function_exists('hxfileBuildEmbedUrl')) {
    function hxfileBuildEmbedUrl($filecode) {
        $filecode = trim($filecode ?? '');
        if ($filecode === '') return '';
        return 'https://xshotcok.com/embed-' . $filecode . '.html';
    }
}
if (!function_exists('hxfileBuildDownloadUrl')) {
    function hxfileBuildDownloadUrl($filecode) {
        $filecode = trim($filecode ?? '');
        if ($filecode === '') return '';
        // Public page URL (no premium direct link usage)
        return 'https://hxfile.co/' . $filecode;
    }
}

/* Step 1: Discover upload server (mirrors coba.php GET) */
if (!function_exists('hxfileGetUploadServer')) {
    function hxfileGetUploadServer() {
        $apiKey = getHxfileApiKey();
        $url = 'http://hxfile.co/api/upload/server?' . http_build_query(['key' => $apiKey]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            throw new Exception('HXFile: Error fetching upload server: ' . $err);
        }
        $data = json_decode($response, true);
        if (!$data || !isset($data['status']) || (int)$data['status'] !== 200) {
            throw new Exception('HXFile: Invalid server response: ' . $response);
        }

        $sessId = $data['sess_id'] ?? ($data['sid'] ?? null);
        $serverUrl = isset($data['result']) ? rtrim($data['result'], '/') : null;

        if (!$sessId || !$serverUrl) {
            throw new Exception('HXFile: Missing sess_id or server URL: ' . json_encode($data));
        }

        // Ensure scheme present
        if (!preg_match('~^https?://~i', $serverUrl)) {
            $serverUrl = 'http://' . ltrim($serverUrl, '/');
        }

        return ['sess_id' => $sessId, 'server_url' => $serverUrl];
    }
}

/* Extract filecode from any kind of HXFile response (JSON or HTML) */
if (!function_exists('hxfileExtractFileCode')) {
    function hxfileExtractFileCode($raw) {
        $raw = (string)$raw;

        // Try JSON decode first
        $json = json_decode($raw, true);
        if (is_array($json)) {
            // Common shape: [ { file_code: "..." } ]
            if (isset($json[0]) && is_array($json[0]) && isset($json[0]['file_code'])) {
                return (string)$json[0]['file_code'];
            }
            // Variant shapes
            if (isset($json['file_code'])) {
                return (string)$json['file_code'];
            }
            if (isset($json['result'])) {
                // result may contain file_code, or a URL with the code
                if (is_array($json['result']) && isset($json['result'][0]['file_code'])) {
                    return (string)$json['result'][0]['file_code'];
                }
                if (is_string($json['result'])) {
                    // Attempt to extract code from URL string
                    if (preg_match('~hxfile\.co/([A-Za-z0-9]+)~', $json['result'], $m)) {
                        return $m[1];
                    }
                }
            }
            // Deep scan for any key like file_code
            $stack = [$json];
            while ($stack) {
                $node = array_pop($stack);
                if (is_array($node)) {
                    foreach ($node as $k => $v) {
                        if (is_array($v)) {
                            $stack[] = $v;
                        } elseif (is_string($v)) {
                            if (strcasecmp((string)$k, 'file_code') === 0) {
                                return $v;
                            }
                            if (preg_match('~hxfile\.co/([A-Za-z0-9]+)~', $v, $m)) {
                                return $m[1];
                            }
                        }
                    }
                }
            }
        }

        // Raw regexes against HTML / text
        // 1) JSON-like "file_code":"CODE"
        if (preg_match('~"file[_ ]?code"\s*:\s*"([A-Za-z0-9]+)"~i', $raw, $m)) {
            return $m[1];
        }
        // 2) hxfile.co/CODE
        if (preg_match('~hxfile\.co/([A-Za-z0-9]+)~i', $raw, $m)) {
            return $m[1];
        }
        // 3) embed-CODE.html (sometimes other domains mirror embed)
        if (preg_match('~embed-([A-Za-z0-9]+)\.html~i', $raw, $m)) {
            return $m[1];
        }
        // 4) generic alnum token length 12-20 surrounded by quotes (last resort)
        if (preg_match('~["\']([A-Za-z0-9]{10,20})["\']~', $raw, $m)) {
            return $m[1];
        }
        // 5) entire response is just the code
        $trim = trim($raw);
        if ($trim !== '' && preg_match('~^[A-Za-z0-9]{10,20}$~', $trim)) {
            return $trim;
        }
        // 6) standalone alnum token length 10-20
        if (preg_match('~\b([A-Za-z0-9]{10,20})\b~', $raw, $m)) {
            return $m[1];
        }

        return '';
    }
}

/* Helper: query HXFile recent files and try to find file_code by original filename */
if (!function_exists('hxfileFindFileCodeByName')) {
    function hxfileFindFileCodeByName($originalName, $perPage = 100) {
        $apiKey = getHxfileApiKey();
        $base = trim(pathinfo((string)$originalName, PATHINFO_FILENAME));
        if ($base === '') {
            return '';
        }

        $url = 'http://hxfile.co/api/file/list?' . http_build_query([
            'key' => $apiKey,
            'per_page' => max(10, min(200, (int)$perPage)),
            'page' => 1
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return '';
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return '';
        }

        // Try to resolve files array from several possible shapes
        $files = [];
        if (isset($data['result']) && is_array($data['result'])) {
            $files = $data['result'];
        } elseif (isset($data['files']) && is_array($data['files'])) {
            $files = $data['files'];
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $files = $data['data'];
        } elseif (isset($data[0]) && is_array($data[0])) {
            $files = $data;
        }

        $baseLower = strtolower($base);
        foreach ($files as $item) {
            if (!is_array($item)) continue;

            $name = '';
            foreach (['file_name', 'name', 'filename', 'title'] as $nk) {
                if (isset($item[$nk]) && is_string($item[$nk]) && trim($item[$nk]) !== '') {
                    $name = (string)$item[$nk];
                    break;
                }
            }
            if ($name === '') continue;

            if (strpos(strtolower($name), $baseLower) !== false) {
                foreach (['file_code', 'filecode', 'fileCode', 'code'] as $ck) {
                    if (isset($item[$ck]) && is_string($item[$ck]) && trim($item[$ck]) !== '') {
                        return trim((string)$item[$ck]);
                    }
                }
            }
        }

        return '';
    }
}

/* Step 2: Upload local file (mirrors coba.php multipart POST) */
if (!function_exists('hxfileUploadLocalFile')) {
    function hxfileUploadLocalFile($filePath, $originalName = '', $mime = '') {
        if (!is_file($filePath)) {
            throw new Exception('HXFile: File not found: ' . $filePath);
        }

        // Determine effective original filename (use provided name if available to preserve extension)
        $effectiveName = '';
        if (is_string($originalName) && trim($originalName) !== '') {
            $effectiveName = trim($originalName);
        } else {
            $effectiveName = basename($filePath);
        }

        // Determine MIME type: prefer provided $mime, else finfo, else by extension, else octet-stream
        $effectiveMime = '';
        if (is_string($mime) && trim($mime) !== '') {
            $effectiveMime = trim($mime);
        } else {
            $detected = '';
            if (function_exists('finfo_open')) {
                $fi = @finfo_open(FILEINFO_MIME_TYPE);
                if ($fi) {
                    $detected = @finfo_file($fi, $filePath) ?: '';
                    @finfo_close($fi);
                }
            }
            if (!$detected) {
                $ext = strtolower(pathinfo($effectiveName, PATHINFO_EXTENSION));
                $map = [
                    'mp4'  => 'video/mp4',
                    'm4v'  => 'video/x-m4v',
                    'mkv'  => 'video/x-matroska',
                    'webm' => 'video/webm',
                    'avi'  => 'video/x-msvideo',
                    'mov'  => 'video/quicktime',
                    'mpeg' => 'video/mpeg',
                    'mpg'  => 'video/mpeg',
                    '3gp'  => 'video/3gpp',
                    'ts'   => 'video/mp2t'
                ];
                $detected = $map[$ext] ?? '';
            }
            $effectiveMime = $detected ?: 'application/octet-stream';
        }

        // Get server info
        $server = hxfileGetUploadServer();
        $sessId = $server['sess_id'];
        $serverUrl = $server['server_url'];

        // Prepare CURLFile with correct filename and mime so HXFile uses the real extension
        $cfile = new CURLFile($filePath, $effectiveMime, $effectiveName);
        $postFields = [
            'sess_id' => $sessId,
            'file'    => $cfile,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $serverUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 0, // allow large uploads
            CURLOPT_VERBOSE => false,
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            throw new Exception('HXFile: Error uploading file: ' . $err);
        }

        // Primary extraction from immediate response
        $code = hxfileExtractFileCode($response);

        // Fallback: if still empty but we know the original filename, query recent list
        if (!$code && is_string($effectiveName) && trim($effectiveName) !== '') {
            // small delay to allow indexing, if needed
            // usleep(300000); // 300ms (optional)
            $code = hxfileFindFileCodeByName($effectiveName, 100);
        }

        $embed = $code ? hxfileBuildEmbedUrl($code) : '';
        $download = $code ? hxfileBuildDownloadUrl($code) : '';

        return [
            'filecode' => $code,
            'embed_url' => $embed,
            'download_url' => $download,
            'raw_response' => $response,
            'http_code' => $httpCode,
        ];
    }
}

/* Convenience: upload from $_FILES entry */
if (!function_exists('hxfileUploadLocalFromFilesArray')) {
    function hxfileUploadLocalFromFilesArray(array $fileEntry) {
        if (!isset($fileEntry['error']) || $fileEntry['error'] !== UPLOAD_ERR_OK) {
            $err = $fileEntry['error'] ?? -1;
            throw new Exception('HXFile: Invalid upload entry, error=' . $err);
        }
        $tmp = $fileEntry['tmp_name'] ?? '';
        if (!$tmp || !is_uploaded_file($tmp)) {
            throw new Exception('HXFile: Temporary upload file missing');
        }
        $orig = $fileEntry['name'] ?? '';
        $type = $fileEntry['type'] ?? ''; // browser-provided MIME
        return hxfileUploadLocalFile($tmp, $orig, $type);
    }
}
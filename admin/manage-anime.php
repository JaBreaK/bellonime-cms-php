<?php
require_once 'includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

$pageTitle = 'Manajemen Anime';

// Handle form actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        handleCreateAnime();
        break;
    case 'edit':
        if ($id) {
            handleEditAnime($id);
        } else {
            setFlashMessage('error', 'ID anime tidak valid');
            redirect(ADMIN_URL . 'manage-anime.php');
        }
        break;
    case 'delete':
        if ($id) {
            handleDeleteAnime($id);
        } else {
            setFlashMessage('error', 'ID anime tidak valid');
            redirect(ADMIN_URL . 'manage-anime.php');
        }
        break;
    case 'toggle-featured':
        if ($id) {
            handleToggleFeatured($id);
        } else {
            setFlashMessage('error', 'ID anime tidak valid');
            redirect(ADMIN_URL . 'manage-anime.php');
        }
        break;
    default:
        showAnimeList();
        break;
}

function handleCreateAnime() {
    global $pageTitle;
    $pageTitle = 'Tambah Anime';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize($_POST['title']);
        $slug = createSlug($title);
        $synopsis = sanitize($_POST['synopsis']);
        $type = $_POST['type'];
        $status = $_POST['status'];
        $studio = sanitize($_POST['studio']);
        $totalEpisodes = (int)$_POST['total_episodes'];
        $duration = (int)$_POST['duration'];
        $rating = (float)$_POST['rating'];
        $year = (int)$_POST['year'];
        $season = $_POST['season'] ?? null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $genres = $_POST['genres'] ?? [];
        
        // Validate input
        if (empty($title)) {
            setFlashMessage('error', 'Judul anime harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM animes WHERE slug = :slug");
            $stmt->execute([':slug' => $slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            // Handle poster URL only (no local uploads)
            $posterUrl = trim($_POST['poster_url'] ?? '');
            $poster = filter_var($posterUrl, FILTER_VALIDATE_URL) ? $posterUrl : '';
            
            // Handle background URL only (no local uploads)
            $backgroundUrl = trim($_POST['background_url'] ?? '');
            $background = filter_var($backgroundUrl, FILTER_VALIDATE_URL) ? $backgroundUrl : '';
            
            // Insert anime
            $sql = "INSERT INTO animes (title, slug, synopsis, poster, background, type, status, studio, total_episodes, duration, rating, year, season, featured) 
                    VALUES (:title, :slug, :synopsis, :poster, :background, :type, :status, :studio, :total_episodes, :duration, :rating, :year, :season, :featured)";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':title' => $title,
                ':slug' => $slug,
                ':synopsis' => $synopsis,
                ':poster' => $poster,
                ':background' => $background,
                ':type' => $type,
                ':status' => $status,
                ':studio' => $studio,
                ':total_episodes' => $totalEpisodes,
                ':duration' => $duration,
                ':rating' => $rating,
                ':year' => $year,
                ':season' => $season,
                ':featured' => $featured
            ];
            
            if ($stmt->execute($params)) {
                $animeId = $db->lastInsertId();
                
                // Insert genres
                if (!empty($genres)) {
                    foreach ($genres as $genreId) {
                        $stmt = $db->prepare("INSERT INTO anime_genre (anime_id, genre_id) VALUES (:anime_id, :genre_id)");
                        $stmt->execute([':anime_id' => $animeId, ':genre_id' => $genreId]);
                    }
                }
                
                setFlashMessage('success', 'Anime berhasil ditambahkan');
                redirect(ADMIN_URL . 'manage-anime.php');
            } else {
                setFlashMessage('error', 'Gagal menambahkan anime');
            }
        }
    }
    
    include 'includes/header.php';
    include 'anime-form.php';
    include 'includes/footer.php';
}

function handleEditAnime($id) {
    global $pageTitle;
    $pageTitle = 'Edit Anime';
    
    $anime = getAnimeById($id);
    if (!$anime) {
        setFlashMessage('error', 'Anime tidak ditemukan');
        redirect(ADMIN_URL . 'manage-anime.php');
    }
    
    $animeGenres = getAnimeGenres($id);
    $selectedGenres = array_column($animeGenres, 'id');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize($_POST['title']);
        $slug = createSlug($title);
        $synopsis = sanitize($_POST['synopsis']);
        $type = $_POST['type'];
        $status = $_POST['status'];
        $studio = sanitize($_POST['studio']);
        $totalEpisodes = (int)$_POST['total_episodes'];
        $duration = (int)$_POST['duration'];
        $rating = (float)$_POST['rating'];
        $year = (int)$_POST['year'];
        $season = $_POST['season'] ?? null;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $genres = $_POST['genres'] ?? [];
        
        // Validate input
        if (empty($title)) {
            setFlashMessage('error', 'Judul anime harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if slug already exists (excluding current anime)
            $stmt = $db->prepare("SELECT id FROM animes WHERE slug = :slug AND id != :id");
            $stmt->execute([':slug' => $slug, ':id' => $id]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            // Handle poster URL only (no local uploads)
            $posterUrl = trim($_POST['poster_url'] ?? '');
            $poster = ($posterUrl && filter_var($posterUrl, FILTER_VALIDATE_URL)) ? $posterUrl : $anime['poster'];
            
            // Handle background URL only (no local uploads)
            $backgroundUrl = trim($_POST['background_url'] ?? '');
            $background = ($backgroundUrl && filter_var($backgroundUrl, FILTER_VALIDATE_URL)) ? $backgroundUrl : $anime['background'];
            
            // Update anime
            $sql = "UPDATE animes SET 
                    title = :title, 
                    slug = :slug, 
                    synopsis = :synopsis, 
                    poster = :poster, 
                    background = :background, 
                    type = :type, 
                    status = :status, 
                    studio = :studio, 
                    total_episodes = :total_episodes, 
                    duration = :duration, 
                    rating = :rating, 
                    year = :year, 
                    season = :season, 
                    featured = :featured,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':title' => $title,
                ':slug' => $slug,
                ':synopsis' => $synopsis,
                ':poster' => $poster,
                ':background' => $background,
                ':type' => $type,
                ':status' => $status,
                ':studio' => $studio,
                ':total_episodes' => $totalEpisodes,
                ':duration' => $duration,
                ':rating' => $rating,
                ':year' => $year,
                ':season' => $season,
                ':featured' => $featured,
                ':id' => $id
            ];
            
            if ($stmt->execute($params)) {
                // Update genres
                // Delete existing genres
                $stmt = $db->prepare("DELETE FROM anime_genre WHERE anime_id = :anime_id");
                $stmt->execute([':anime_id' => $id]);
                
                // Insert new genres
                if (!empty($genres)) {
                    foreach ($genres as $genreId) {
                        $stmt = $db->prepare("INSERT INTO anime_genre (anime_id, genre_id) VALUES (:anime_id, :genre_id)");
                        $stmt->execute([':anime_id' => $id, ':genre_id' => $genreId]);
                    }
                }
                
                setFlashMessage('success', 'Anime berhasil diperbarui');
                redirect(ADMIN_URL . 'manage-anime.php');
            } else {
                setFlashMessage('error', 'Gagal memperbarui anime');
            }
        }
    }
    
    include 'includes/header.php';
    include 'anime-form.php';
    include 'includes/footer.php';
}

function handleDeleteAnime($id) {
    $anime = getAnimeById($id);
    if (!$anime) {
        setFlashMessage('error', 'Anime tidak ditemukan');
        redirect(ADMIN_URL . 'manage-anime.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = Database::getInstance()->getConnection();
        
        try {
            // Delete related records
            $stmt = $db->prepare("DELETE FROM anime_genre WHERE anime_id = :anime_id");
            $stmt->execute([':anime_id' => $id]);
            
            $stmt = $db->prepare("DELETE FROM episodes WHERE anime_id = :anime_id");
            $stmt->execute([':anime_id' => $id]);
            
            // Delete anime
            $stmt = $db->prepare("DELETE FROM animes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            // No local files to delete; images are external URLs now
            
            setFlashMessage('success', 'Anime berhasil dihapus');
        } catch (Exception $e) {
            setFlashMessage('error', 'Gagal menghapus anime: ' . $e->getMessage());
        }
    }
    
    redirect(ADMIN_URL . 'manage-anime.php');
}

function handleToggleFeatured($id) {
    $anime = getAnimeById($id);
    if (!$anime) {
        setFlashMessage('error', 'Anime tidak ditemukan');
        redirect(ADMIN_URL . 'manage-anime.php');
    }
    
    $db = Database::getInstance()->getConnection();
    $newFeatured = $anime['featured'] ? 0 : 1;
    
    $stmt = $db->prepare("UPDATE animes SET featured = :featured WHERE id = :id");
    $stmt->execute([':featured' => $newFeatured, ':id' => $id]);
    
    setFlashMessage('success', 'Status featured berhasil diperbarui');
    redirect(ADMIN_URL . 'manage-anime.php');
}

function showAnimeList() {
    $page = (int)($_GET['page'] ?? 1);
    $perPage = 10;
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $db = Database::getInstance()->getConnection();
    
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
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";
    
    // Get paginated results
    $result = paginate($sql, $page, $perPage, $params);
    
    include 'includes/header.php';
    include 'anime-list.php';
    include 'includes/footer.php';
}
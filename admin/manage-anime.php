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
        $year = (int)$_POST['year'];
        $totalEpisodes = (int)$_POST['total_episodes'];
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
            
            // Handle poster upload
            $posterFile = null;
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $posterFile = uploadImageFile($_FILES['poster'], 'uploads/posters');
            }
            
            // Handle background upload
            $backgroundFile = null;
            if (isset($_FILES['background']) && $_FILES['background']['error'] === UPLOAD_ERR_OK) {
                $backgroundFile = uploadImageFile($_FILES['background'], 'uploads/posters');
            }
            
            // Insert anime
            $sql = "INSERT INTO animes (title, slug, synopsis, type, status, year, total_episodes, poster, background, featured) 
                    VALUES (:title, :slug, :synopsis, :type, :status, :year, :total_episodes, :poster, :background, :featured)";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':title' => $title,
                ':slug' => $slug,
                ':synopsis' => $synopsis,
                ':type' => $type,
                ':status' => $status,
                ':year' => $year,
                ':total_episodes' => $totalEpisodes,
                ':poster' => $posterFile,
                ':background' => $backgroundFile,
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
        $year = (int)$_POST['year'];
        $totalEpisodes = (int)$_POST['total_episodes'];
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
            
            // Handle poster upload
            $posterFile = $anime['poster'] ?? null;
            if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                $newPoster = uploadImageFile($_FILES['poster'], 'uploads/posters');
                if ($newPoster) {
                    if ($posterFile) {
                        deleteLocalFile($posterFile);
                    }
                    $posterFile = $newPoster;
                }
            }
            
            // Handle background upload
            $backgroundFile = $anime['background'] ?? null;
            if (isset($_FILES['background']) && $_FILES['background']['error'] === UPLOAD_ERR_OK) {
                $newBackground = uploadImageFile($_FILES['background'], 'uploads/posters');
                if ($newBackground) {
                    if ($backgroundFile) {
                        deleteLocalFile($backgroundFile);
                    }
                    $backgroundFile = $newBackground;
                }
            }
            
            // Update anime
            $sql = "UPDATE animes SET 
                    title = :title,
                    slug = :slug,
                    synopsis = :synopsis,
                    type = :type,
                    status = :status,
                    year = :year,
                    total_episodes = :total_episodes,
                    poster = :poster,
                    background = :background,
                    featured = :featured,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':title' => $title,
                ':slug' => $slug,
                ':synopsis' => $synopsis,
                ':type' => $type,
                ':status' => $status,
                ':year' => $year,
                ':total_episodes' => $totalEpisodes,
                ':poster' => $posterFile,
                ':background' => $backgroundFile,
                ':featured' => $featured,
                ':id' => $id
            ];
            
            if ($stmt->execute($params)) {
                // Update genres
                $stmt = $db->prepare("DELETE FROM anime_genre WHERE anime_id = :anime_id");
                $stmt->execute([':anime_id' => $id]);
                
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
            // Delete local files if exist
            if (!empty($anime['poster'])) {
                deleteLocalFile($anime['poster']);
            }
            if (!empty($anime['background'])) {
                deleteLocalFile($anime['background']);
            }
            
            // Delete related records
            $stmt = $db->prepare("DELETE FROM anime_genre WHERE anime_id = :anime_id");
            $stmt->execute([':anime_id' => $id]);
            
            $stmt = $db->prepare("DELETE FROM episodes WHERE anime_id = :anime_id");
            $stmt->execute([':anime_id' => $id]);
            
            // Delete anime
            $stmt = $db->prepare("DELETE FROM animes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
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
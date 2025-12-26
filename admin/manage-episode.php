<?php
require_once 'includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

$pageTitle = 'Manajemen Episode';

// Handle form actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;
$animeId = $_GET['anime_id'] ?? null;

switch ($action) {
    case 'create':
        handleCreateEpisode();
        break;
    case 'edit':
        if ($id) {
            handleEditEpisode($id);
        } else {
            setFlashMessage('error', 'ID episode tidak valid');
            redirect(ADMIN_URL . 'manage-episode.php');
        }
        break;
    case 'delete':
        if ($id) {
            handleDeleteEpisode($id);
        } else {
            setFlashMessage('error', 'ID episode tidak valid');
            redirect(ADMIN_URL . 'manage-episode.php');
        }
        break;
    default:
        showEpisodeList();
        break;
}

function handleCreateEpisode() {
    global $pageTitle;
    $pageTitle = 'Tambah Episode';
    
    $animeId = $_GET['anime_id'] ?? null;
    $anime = null;
    if ($animeId) {
        $anime = getAnimeById($animeId);
        if (!$anime) {
            setFlashMessage('error', 'Anime tidak ditemukan');
            redirect(ADMIN_URL . 'manage-episode.php');
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $animeId = (int)$_POST['anime_id'];
        $episodeNumber = (int)$_POST['episode_number'];
        $title = sanitize($_POST['title']);
        $slug = createSlug($title) . '-ep-' . $episodeNumber;
        $duration = (int)$_POST['duration'];
        
        // Handle video file upload
        $videoFile = null;
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $videoFile = uploadVideoFile($_FILES['video_file'], 'uploads/videos');
        }
        
        // Validate input
        if (empty($title)) {
            setFlashMessage('error', 'Judul episode harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if slug already exists
            $stmt = $db->prepare("SELECT id FROM episodes WHERE slug = :slug");
            $stmt->execute([':slug' => $slug]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
            
            // Insert episode
            $sql = "INSERT INTO episodes (anime_id, episode_number, title, slug, video, duration) 
                    VALUES (:anime_id, :episode_number, :title, :slug, :video, :duration)";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':anime_id' => $animeId,
                ':episode_number' => $episodeNumber,
                ':title' => $title,
                ':slug' => $slug,
                ':video' => $videoFile,
                ':duration' => $duration
            ];
            
            if ($stmt->execute($params)) {
                setFlashMessage('success', 'Episode berhasil ditambahkan');
                redirect(ADMIN_URL . 'manage-episode.php?anime_id=' . $animeId);
            } else {
                setFlashMessage('error', 'Gagal menambahkan episode');
            }
        }
    }
    
    include 'includes/header.php';
    include 'episode-form.php';
    include 'includes/footer.php';
}

function handleEditEpisode($id) {
    global $pageTitle;
    $pageTitle = 'Edit Episode';
    
    $episode = getEpisodeById($id);
    if (!$episode) {
        setFlashMessage('error', 'Episode tidak ditemukan');
        redirect(ADMIN_URL . 'manage-episode.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $animeId = (int)$_POST['anime_id'];
        $episodeNumber = (int)$_POST['episode_number'];
        $title = sanitize($_POST['title']);
        $slug = createSlug($title) . '-ep-' . $episodeNumber;
        $duration = (int)$_POST['duration'];
        
        // Handle video file upload
        $videoFile = $episode['video'] ?? null;
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $newVideoFile = uploadVideoFile($_FILES['video_file'], 'uploads/videos');
            if ($newVideoFile) {
                if ($videoFile) {
                    deleteLocalFile($videoFile);
                }
                $videoFile = $newVideoFile;
            }
        }
        
        // Validate input
        if (empty($title)) {
            setFlashMessage('error', 'Judul episode harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Update episode
            $sql = "UPDATE episodes SET 
                    anime_id = :anime_id,
                    episode_number = :episode_number,
                    title = :title,
                    slug = :slug,
                    video = :video,
                    duration = :duration,
                    updated_at = NOW()
                    WHERE id = :id";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':anime_id' => $animeId,
                ':episode_number' => $episodeNumber,
                ':title' => $title,
                ':slug' => $slug,
                ':video' => $videoFile,
                ':duration' => $duration,
                ':id' => $id
            ];
            
            if ($stmt->execute($params)) {
                setFlashMessage('success', 'Episode berhasil diperbarui');
                redirect(ADMIN_URL . 'manage-episode.php?anime_id=' . $animeId);
            } else {
                setFlashMessage('error', 'Gagal memperbarui episode');
            }
        }
    }
    
    include 'includes/header.php';
    include 'episode-form.php';
    include 'includes/footer.php';
}

function handleDeleteEpisode($id) {
    $episode = getEpisodeById($id);
    if (!$episode) {
        setFlashMessage('error', 'Episode tidak ditemukan');
        redirect(ADMIN_URL . 'manage-episode.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = Database::getInstance()->getConnection();
        
        try {
            // Delete local video file if exists
            if (!empty($episode['video'])) {
                deleteLocalFile($episode['video']);
            }
            
            // Delete episode from database
            $stmt = $db->prepare("DELETE FROM episodes WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            setFlashMessage('success', 'Episode berhasil dihapus');
        } catch (Exception $e) {
            setFlashMessage('error', 'Gagal menghapus episode: ' . $e->getMessage());
        }
    }
    
    redirect(ADMIN_URL . 'manage-episode.php?anime_id=' . $episode['anime_id']);
}

function showEpisodeList() {
    $page = (int)($_GET['page'] ?? 1);
    $perPage = 20;
    $search = $_GET['search'] ?? '';
    $animeId = $_GET['anime_id'] ?? null;
    
    $db = Database::getInstance()->getConnection();
    
    // Build query
    $sql = "SELECT e.*, a.title as anime_title, a.poster
            FROM episodes e
            INNER JOIN animes a ON e.anime_id = a.id";
    
    $conditions = [];
    $params = [];
    
    if (!empty($search)) {
        $conditions[] = "(e.title LIKE :search OR a.title LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if ($animeId) {
        $conditions[] = "e.anime_id = :anime_id";
        $params[':anime_id'] = $animeId;
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " ORDER BY e.anime_id, e.episode_number ASC";
    
    // Get paginated results
    $result = paginate($sql, $page, $perPage, $params);
    
    // Get all animes for filter
    $animes = getAllAnimes();
    
    include 'includes/header.php';
    include 'episode-list.php';
    include 'includes/footer.php';
}
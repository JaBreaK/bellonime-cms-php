<?php
require_once 'includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

// Ensure DB has quality columns for episodes
if (function_exists('ensureEpisodeQualityColumns')) {
    ensureEpisodeQualityColumns();
}

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
        // Build title from anime title if empty
        $animeData = getAnimeById($animeId);
        $baseTitle = $animeData ? $animeData['title'] : '';
        $titleInput = sanitize($_POST['title']);
        $title = !empty($titleInput) ? $titleInput : ($baseTitle ? ($baseTitle . ' - Episode ' . $episodeNumber) : '');
        // Slug always based on anime title + episode number
        $slug = createSlug($baseTitle ?: $title) . '-ep-' . $episodeNumber;
        // Per-quality EMBED URLs (store iframe src URLs only)
        $embed480 = normalizeEmbedInput($_POST['embed_480_url'] ?? '');
        $embed720 = normalizeEmbedInput($_POST['embed_720_url'] ?? '');
        $embed1080 = normalizeEmbedInput($_POST['embed_1080_url'] ?? '');
        // Per-quality download URLs
        $dl480 = sanitize($_POST['dl_480_url'] ?? '');
        $dl720 = sanitize($_POST['dl_720_url'] ?? '');
        $dl1080 = sanitize($_POST['dl_1080_url'] ?? '');
        $duration = (int)$_POST['duration'];
        
        // HXFile local upload per kualitas (opsional): auto-fill embed & download if provided
        try {
            $map = [
                '480' => 'hxfile_file_480',
                '720' => 'hxfile_file_720',
                '1080' => 'hxfile_file_1080',
            ];
            // Check hidden flags and filecodes set via AJAX (to avoid re-upload)
            $flags = [
                '480' => !empty($_POST['hxfile_uploaded_480']),
                '720' => !empty($_POST['hxfile_uploaded_720']),
                '1080' => !empty($_POST['hxfile_uploaded_1080']),
            ];
            $codes = [
                '480' => trim((string)($_POST['hxfile_filecode_480'] ?? '')),
                '720' => trim((string)($_POST['hxfile_filecode_720'] ?? '')),
                '1080' => trim((string)($_POST['hxfile_filecode_1080'] ?? '')),
            ];
            // Current values from form POST
            $currentEmbeds = [
                '480' => $embed480,
                '720' => $embed720,
                '1080' => $embed1080,
            ];
            $currentDls = [
                '480' => $dl480,
                '720' => $dl720,
                '1080' => $dl1080,
            ];

            foreach ($map as $q => $field) {
                // If we have a filecode from AJAX but links are empty, construct and fill them now
                if (!empty($codes[$q]) && (empty($currentEmbeds[$q]) || empty($currentDls[$q]))) {
                    $embedUrl = hxfileBuildEmbedUrl($codes[$q]);
                    $dlUrl = hxfileBuildDownloadUrl($codes[$q]);
                    if ($q === '480') { $embed480 = $embedUrl; $dl480 = $dlUrl; }
                    elseif ($q === '720') { $embed720 = $embedUrl; $dl720 = $dlUrl; }
                    elseif ($q === '1080') { $embed1080 = $embedUrl; $dl1080 = $dlUrl; }
                }

                // Skip server-side re-upload if links already populated or AJAX flag present
                if (!empty($currentEmbeds[$q]) || !empty($currentDls[$q]) || !empty($flags[$q]) || !empty($codes[$q])) {
                    continue;
                }

                // Otherwise, if a file is posted on server-side, upload it (non-AJAX fallback)
                if (isset($_FILES[$field]) && is_array($_FILES[$field]) && (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK)) {
                    $res = hxfileUploadLocalFromFilesArray($_FILES[$field]);
                    $code = $res['filecode'] ?? '';
                    if ($code) {
                        $embedUrl = $res['embed_url'] ?? '';
                        $dlUrl = $res['download_url'] ?? '';
                        if ($q === '480') {
                            $embed480 = $embedUrl; $dl480 = $dlUrl;
                        } elseif ($q === '720') {
                            $embed720 = $embedUrl; $dl720 = $dlUrl;
                        } elseif ($q === '1080') {
                            $embed1080 = $embedUrl; $dl1080 = $dlUrl;
                        }
                        setFlashMessage('success', "HXFile {$q}p upload sukses: {$code}");
                    } else {
                        $snippet = substr(strip_tags($res['raw_response'] ?? ''), 0, 200);
                        setFlashMessage('error', "HXFile {$q}p upload file gagal: Filecode not found in upload response" . ($snippet ? " (snippet: {$snippet}...)" : ""));
                    }
                }
            }
        } catch (Throwable $e) {
            setFlashMessage('error', 'HXFile upload error: ' . $e->getMessage());
        }

        // Bind posted/HXFile values back to $episode for form re-render when validation fails
        $episode = [
            'anime_id' => $animeId,
            'episode_number' => $episodeNumber,
            'title' => $title,
            'duration' => $duration,
            'embed_480_url' => $embed480,
            'embed_720_url' => $embed720,
            'embed_1080_url' => $embed1080,
            'dl_480_url' => $dl480,
            'dl_720_url' => $dl720,
            'dl_1080_url' => $dl1080,
        ];

        // Validate input
        if (empty($animeId) || empty($title) || empty($episodeNumber)) {
            setFlashMessage('error', 'Anime, judul, dan nomor episode harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if episode already exists
            $stmt = $db->prepare("SELECT id FROM episodes WHERE anime_id = :anime_id AND episode_number = :episode_number");
            $stmt->execute([':anime_id' => $animeId, ':episode_number' => $episodeNumber]);
            if ($stmt->fetch()) {
                setFlashMessage('error', 'Episode dengan nomor tersebut sudah ada');
            } else {
                // Insert episode with per-quality embed and download URLs
                $sql = "INSERT INTO episodes (
                            anime_id, episode_number, title, slug,
                            embed_480_url, embed_720_url, embed_1080_url,
                            dl_480_url, dl_720_url, dl_1080_url,
                            duration
                        ) VALUES (
                            :anime_id, :episode_number, :title, :slug,
                            :embed_480_url, :embed_720_url, :embed_1080_url,
                            :dl_480_url, :dl_720_url, :dl_1080_url,
                            :duration
                        )";
                
                $stmt = $db->prepare($sql);
                $params = [
                    ':anime_id' => $animeId,
                    ':episode_number' => $episodeNumber,
                    ':title' => $title,
                    ':slug' => $slug,
                    ':embed_480_url' => $embed480,
                    ':embed_720_url' => $embed720,
                    ':embed_1080_url' => $embed1080,
                    ':dl_480_url' => $dl480,
                    ':dl_720_url' => $dl720,
                    ':dl_1080_url' => $dl1080,
                    ':duration' => $duration
                ];
                
                if ($stmt->execute($params)) {
                    setFlashMessage('success', 'Episode berhasil ditambahkan');
                    
                    // Redirect to specific anime episodes or back to list
                    if (isset($_POST['add_more'])) {
                        redirect(ADMIN_URL . 'manage-episode.php?action=create&anime_id=' . $animeId);
                    } else {
                        redirect(ADMIN_URL . 'manage-episode.php?anime_id=' . $animeId);
                    }
                } else {
                    setFlashMessage('error', 'Gagal menambahkan episode');
                }
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
        // Build title from anime title if empty (keeps edit flexible)
        $animeData = getAnimeById($animeId);
        $baseTitle = $animeData ? $animeData['title'] : '';
        $titleInput = sanitize($_POST['title']);
        $title = !empty($titleInput) ? $titleInput : ($baseTitle ? ($baseTitle . ' - Episode ' . $episodeNumber) : '');
        // Slug always based on anime title + episode number
        $slug = createSlug($baseTitle ?: $title) . '-ep-' . $episodeNumber;
        // Per-quality EMBED URLs (store iframe src URLs only)
        $embed480 = normalizeEmbedInput($_POST['embed_480_url'] ?? '');
        $embed720 = normalizeEmbedInput($_POST['embed_720_url'] ?? '');
        $embed1080 = normalizeEmbedInput($_POST['embed_1080_url'] ?? '');
        // Per-quality download URLs
        $dl480 = sanitize($_POST['dl_480_url'] ?? '');
        $dl720 = sanitize($_POST['dl_720_url'] ?? '');
        $dl1080 = sanitize($_POST['dl_1080_url'] ?? '');
        $duration = (int)$_POST['duration'];
        
        // HXFile local upload per kualitas (opsional): auto-fill embed & download if provided
        try {
            $map = [
                '480' => 'hxfile_file_480',
                '720' => 'hxfile_file_720',
                '1080' => 'hxfile_file_1080',
            ];
            // Check hidden flags and filecodes set via AJAX (to avoid re-upload)
            $flags = [
                '480' => !empty($_POST['hxfile_uploaded_480']),
                '720' => !empty($_POST['hxfile_uploaded_720']),
                '1080' => !empty($_POST['hxfile_uploaded_1080']),
            ];
            $codes = [
                '480' => trim((string)($_POST['hxfile_filecode_480'] ?? '')),
                '720' => trim((string)($_POST['hxfile_filecode_720'] ?? '')),
                '1080' => trim((string)($_POST['hxfile_filecode_1080'] ?? '')),
            ];
            // Current values from form POST
            $currentEmbeds = [
                '480' => $embed480,
                '720' => $embed720,
                '1080' => $embed1080,
            ];
            $currentDls = [
                '480' => $dl480,
                '720' => $dl720,
                '1080' => $dl1080,
            ];

            foreach ($map as $q => $field) {
                // If we have a filecode from AJAX but links are empty, construct and fill them now
                if (!empty($codes[$q]) && (empty($currentEmbeds[$q]) || empty($currentDls[$q]))) {
                    $embedUrl = hxfileBuildEmbedUrl($codes[$q]);
                    $dlUrl = hxfileBuildDownloadUrl($codes[$q]);
                    if ($q === '480') { $embed480 = $embedUrl; $dl480 = $dlUrl; }
                    elseif ($q === '720') { $embed720 = $embedUrl; $dl720 = $dlUrl; }
                    elseif ($q === '1080') { $embed1080 = $embedUrl; $dl1080 = $dlUrl; }
                }

                // Skip server-side re-upload if links already populated or AJAX flag present
                if (!empty($currentEmbeds[$q]) || !empty($currentDls[$q]) || !empty($flags[$q]) || !empty($codes[$q])) {
                    continue;
                }

                // Otherwise, if a file is posted on server-side, upload it (non-AJAX fallback)
                if (isset($_FILES[$field]) && is_array($_FILES[$field]) && (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK)) {
                    $res = hxfileUploadLocalFromFilesArray($_FILES[$field]);
                    $code = $res['filecode'] ?? '';
                    if ($code) {
                        $embedUrl = $res['embed_url'] ?? '';
                        $dlUrl = $res['download_url'] ?? '';
                        if ($q === '480') {
                            $embed480 = $embedUrl; $dl480 = $dlUrl;
                        } elseif ($q === '720') {
                            $embed720 = $embedUrl; $dl720 = $dlUrl;
                        } elseif ($q === '1080') {
                            $embed1080 = $embedUrl; $dl1080 = $dlUrl;
                        }
                        setFlashMessage('success', "HXFile {$q}p upload sukses: {$code}");
                    } else {
                        $snippet = substr(strip_tags($res['raw_response'] ?? ''), 0, 200);
                        setFlashMessage('error', "HXFile {$q}p upload file gagal: Filecode not found in upload response" . ($snippet ? " (snippet: {$snippet}...)" : ""));
                    }
                }
            }
        } catch (Throwable $e) {
            setFlashMessage('error', 'HXFile upload error: ' . $e->getMessage());
        }

        // Bind posted/HXFile values back to $episode for form re-render when validation fails
        $episode['anime_id'] = $animeId;
        $episode['episode_number'] = $episodeNumber;
        $episode['title'] = $title;
        $episode['duration'] = $duration;
        $episode['embed_480_url'] = $embed480;
        $episode['embed_720_url'] = $embed720;
        $episode['embed_1080_url'] = $embed1080;
        $episode['dl_480_url'] = $dl480;
        $episode['dl_720_url'] = $dl720;
        $episode['dl_1080_url'] = $dl1080;

        // Validate input
        if (empty($title) || empty($episodeNumber)) {
            setFlashMessage('error', 'Judul dan nomor episode harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if episode number already exists (excluding current episode)
            $stmt = $db->prepare("SELECT id FROM episodes WHERE anime_id = :anime_id AND episode_number = :episode_number AND id != :id");
            $stmt->execute([':anime_id' => $animeId, ':episode_number' => $episodeNumber, ':id' => $id]);
            if ($stmt->fetch()) {
                setFlashMessage('error', 'Episode dengan nomor tersebut sudah ada');
            } else {
                // Update episode with per-quality embed and download URLs
                $sql = "UPDATE episodes SET
                        anime_id = :anime_id,
                        episode_number = :episode_number,
                        title = :title,
                        slug = :slug,
                        embed_480_url = :embed_480_url,
                        embed_720_url = :embed_720_url,
                        embed_1080_url = :embed_1080_url,
                        dl_480_url = :dl_480_url,
                        dl_720_url = :dl_720_url,
                        dl_1080_url = :dl_1080_url,
                        duration = :duration,
                        updated_at = NOW()
                        WHERE id = :id";
                
                $stmt = $db->prepare($sql);
                $params = [
                    ':anime_id' => $animeId,
                    ':episode_number' => $episodeNumber,
                    ':title' => $title,
                    ':slug' => $slug,
                    ':embed_480_url' => $embed480,
                    ':embed_720_url' => $embed720,
                    ':embed_1080_url' => $embed1080,
                    ':dl_480_url' => $dl480,
                    ':dl_720_url' => $dl720,
                    ':dl_1080_url' => $dl1080,
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
            // Delete episode
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
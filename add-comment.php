<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$episodeId = isset($_POST['episode_id']) ? (int)$_POST['episode_id'] : 0;
$animeId = isset($_POST['anime_id']) ? (int)$_POST['anime_id'] : 0;
$content = trim($_POST['content'] ?? '');

if ($episodeId <= 0 || $animeId <= 0) {
    setFlashMessage('error', 'Data episode tidak valid');
    redirect('index.php');
}

if ($content === '') {
    setFlashMessage('error', 'Komentar tidak boleh kosong');
    redirect('nonton.php?id=' . $episodeId);
}

// Validasi episode
$episode = getEpisodeById($episodeId);
if (!$episode || (int)$episode['anime_id'] !== $animeId) {
    setFlashMessage('error', 'Episode tidak ditemukan');
    redirect('index.php');
}

$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("INSERT INTO comments (user_id, anime_id, episode_id, content, status, created_at, updated_at) VALUES (NULL, :anime_id, :episode_id, :content, 'approved', NOW(), NOW())");
    $stmt->execute([
        ':anime_id' => $animeId,
        ':episode_id' => $episodeId,
        ':content' => $content,
    ]);
    setFlashMessage('success', 'Komentar berhasil ditambahkan');
} catch (Exception $e) {
    setFlashMessage('error', 'Gagal menambahkan komentar');
}

redirect('nonton.php?id=' . $episodeId);
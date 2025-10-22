<?php
require_once 'includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

$animeId = (int)$_GET['anime_id'];

if ($animeId) {
    $db = Database::getInstance()->getConnection();
    
    // Get highest episode number for this anime
    $stmt = $db->prepare("SELECT MAX(episode_number) as max_episode FROM episodes WHERE anime_id = :anime_id");
    $stmt->execute([':anime_id' => $animeId]);
    $result = $stmt->fetch();
    
    $nextEpisode = ($result['max_episode'] ?? 0) + 1;
    
    echo json_encode(['nextEpisode' => $nextEpisode]);
} else {
    echo json_encode(['error' => 'Invalid anime ID']);
}
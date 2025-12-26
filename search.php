<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

// Search animes
$db = Database::getInstance()->getConnection();

$sql = "SELECT a.id, a.title, a.slug, a.poster, a.type, a.status, a.year,
        (SELECT COUNT(*) FROM episodes WHERE anime_id = a.id) as episode_count
        FROM animes a
        WHERE a.title LIKE :query_like OR a.synopsis LIKE :query_like
        ORDER BY a.views DESC, a.created_at DESC
        LIMIT 10";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':query' => $query,
    ':query_like' => "%$query%"
]);

$results = $stmt->fetchAll();

// Format results
$formattedResults = [];
foreach ($results as $result) {
    $formattedResults[] = [
        'id' => $result['id'],
        'title' => $result['title'],
        'slug' => $result['slug'],
        'poster' => getImageUrl($result['poster'] ?? ''),
        'type' => $result['type'],
        'status' => $result['status'],
        'year' => $result['year'],
        'episode_count' => $result['episode_count'],
        'url' => BASE_URL . 'detail.php?slug=' . $result['slug']
    ];
}

echo json_encode($formattedResults);
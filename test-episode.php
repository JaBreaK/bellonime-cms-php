<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

echo "<h1>Test Episode Management</h1>";

// Test getAllAnimes function
echo "<h2>Test getAllAnimes()</h2>";
$animes = getAllAnimes();
if ($animes) {
    echo "<p>Found " . count($animes) . " anime(s):</p>";
    echo "<ul>";
    foreach ($animes as $anime) {
        echo "<li>ID: " . $anime['id'] . ", Title: " . $anime['title'] . ", Episodes: " . $anime['episode_count'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No anime found</p>";
}

// Test getEpisodesByAnimeId function
echo "<h2>Test getEpisodesByAnimeId()</h2>";
if (!empty($animes)) {
    $firstAnimeId = $animes[0]['id'];
    $episodes = getEpisodesByAnimeId($firstAnimeId);
    if ($episodes) {
        echo "<p>Found " . count($episodes) . " episode(s) for anime ID " . $firstAnimeId . ":</p>";
        echo "<ul>";
        foreach ($episodes as $episode) {
            echo "<li>Episode " . $episode['episode_number'] . ": " . $episode['title'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No episodes found for anime ID " . $firstAnimeId . "</p>";
    }
}

// Add a test episode
echo "<h2>Add Test Episode</h2>";
if (!empty($animes)) {
    $firstAnime = $animes[0];
    $animeId = $firstAnime['id'];
    $animeTitle = $firstAnime['title'];
    
    echo "<p>Adding a test episode for anime: " . $animeTitle . " (ID: " . $animeId . ")</p>";
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Check if episode already exists
        $stmt = $db->prepare("SELECT id FROM episodes WHERE anime_id = :anime_id AND episode_number = :episode_number");
        $stmt->execute([':anime_id' => $animeId, ':episode_number' => 1]);
        
        if (!$stmt->fetch()) {
            // Insert test episode
            $sql = "INSERT INTO episodes (anime_id, episode_number, title, slug, video_url, duration) 
                    VALUES (:anime_id, :episode_number, :title, :slug, :video_url, :duration)";
            
            $stmt = $db->prepare($sql);
            $params = [
                ':anime_id' => $animeId,
                ':episode_number' => 1,
                ':title' => 'Episode 1 - ' . $animeTitle,
                ':slug' => createSlug($animeTitle) . '-ep-1',
                ':video_url' => 'https://example.com/video.mp4',
                ':duration' => 24
            ];
            
            if ($stmt->execute($params)) {
                echo "<p style='color: green;'>Test episode added successfully!</p>";
            } else {
                echo "<p style='color: red;'>Failed to add test episode</p>";
            }
        } else {
            echo "<p style='color: orange;'>Episode 1 already exists for this anime</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
    
    // Verify the episode was added
    echo "<h2>Verify Episode Added</h2>";
    $episodes = getEpisodesByAnimeId($animeId);
    if ($episodes) {
        echo "<p>Found " . count($episodes) . " episode(s) for anime ID " . $animeId . ":</p>";
        echo "<ul>";
        foreach ($episodes as $episode) {
            echo "<li>Episode " . $episode['episode_number'] . ": " . $episode['title'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No episodes found for anime ID " . $animeId . "</p>";
    }
}

// Test direct database query
echo "<h2>Direct Database Query</h2>";
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT COUNT(*) as total FROM episodes");
    $result = $stmt->fetch();
    echo "<p>Total episodes in database: " . $result['total'] . "</p>";
    
    if ($result['total'] > 0) {
        $stmt = $db->query("SELECT e.*, a.title as anime_title FROM episodes e JOIN animes a ON e.anime_id = a.id ORDER BY e.anime_id, e.episode_number");
        $episodes = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Anime</th><th>Episode</th><th>Title</th></tr>";
        
        foreach ($episodes as $episode) {
            echo "<tr>";
            echo "<td>" . $episode['anime_title'] . "</td>";
            echo "<td>" . $episode['episode_number'] . "</td>";
            echo "<td>" . $episode['title'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Solution</h2>";
echo "<p>If the test episode was added successfully, you should now be able to see it in the <a href='admin/manage-episode.php'>Manage Episode</a> page.</p>";
echo "<p>If not, there might be an issue with the database connection or the episode listing logic.</p>";
?>
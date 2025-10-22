<?php
require_once 'core/connection.php';

// Insert sample genres
$genres = [
    ['Action', 'action', 'Series with high energy, intense physical conflict, and thrilling stunts.'],
    ['Adventure', 'adventure', 'Journey-based narratives with exploration and discovery.'],
    ['Comedy', 'comedy', 'Light-hearted series designed to make you laugh with humor and witty dialogue.'],
    ['Drama', 'drama', 'Emotionally charged stories focusing on character development and realistic situations.'],
    ['Fantasy', 'fantasy', 'Magical worlds, supernatural elements, and mythical creatures.'],
    ['Horror', 'horror', 'Spine-chilling tales designed to frighten and unsettle the audience.'],
    ['Romance', 'romance', 'Love stories focusing on romantic relationships and emotional connections.'],
    ['Sci-Fi', 'sci-fi', 'Futuristic settings, advanced technology, and space exploration.'],
    ['Slice of Life', 'slice-of-life', 'Everyday experiences and realistic portrayals of ordinary life.'],
    ['Sports', 'sports', 'Competitive athletic activities and the passion for games.'],
    ['Thriller', 'thriller', 'Suspenseful narratives that keep you on the edge of your seat.'],
    ['Historical', 'historical', 'Stories set in the past with accurate historical settings and events.'],
    ['Mecha', 'mecha', 'Series featuring giant robots and mechanical suits.'],
    ['Psychological', 'psychological', 'Mind-bending narratives that explore the depths of human psychology.'],
    ['Superhero', 'superhero', 'Stories about individuals with extraordinary powers fighting evil.']
];

foreach ($genres as $genre) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO genres (name, slug, description) VALUES (?, ?, ?)");
    $stmt->execute([$genre[0], $genre[1], $genre[2]]);
}

// Get genre IDs for anime assignment
$genreStmt = $pdo->query("SELECT id, slug FROM genres");
$genreMap = [];
while ($row = $genreStmt->fetch()) {
    $genreMap[$row['slug']] = $row['id'];
}

// Insert sample animes
$animes = [
    [
        'title' => 'Naruto Shippuden',
        'slug' => 'naruto-shippuden',
        'synopsis' => 'Naruto Uzumaki returns to Konoha after two years of training with Jiraiya. He reunites with his friends and mentors to face the Akatsuki, a dangerous organization seeking to capture all Tailed Beasts.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Studio Pierrot',
        'total_episodes' => 500,
        'duration' => 24,
        'rating' => 8.5,
        'year' => 2007,
        'season' => 'Fall',
        'featured' => 1,
        'genres' => ['action', 'drama', 'fantasy']
    ],
    [
        'title' => 'Attack on Titan',
        'slug' => 'attack-on-titan',
        'synopsis' => 'Humanity lives within cities protected by enormous walls. When a colossal titan breaches the wall, Eren Yeager and his friends join the military to fight against the man-eating titans.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Wit Studio',
        'total_episodes' => 87,
        'duration' => 24,
        'rating' => 9.0,
        'year' => 2013,
        'season' => 'Spring',
        'featured' => 1,
        'genres' => ['action', 'drama', 'fantasy', 'horror']
    ],
    [
        'title' => 'One Piece',
        'slug' => 'one-piece',
        'synopsis' => 'Monkey D. Luffy and his crew of Straw Hat Pirates search for the ultimate treasure, One Piece, to become the next Pirate King.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'Toei Animation',
        'total_episodes' => 1000,
        'duration' => 24,
        'rating' => 8.8,
        'year' => 1999,
        'season' => 'Fall',
        'featured' => 1,
        'genres' => ['action', 'adventure', 'comedy', 'drama']
    ],
    [
        'title' => 'Death Note',
        'slug' => 'death-note',
        'synopsis' => 'Light Yagami discovers a notebook that can kill anyone whose name is written in it. He decides to create a perfect world free of crime, but a brilliant detective known as L pursues him.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Madhouse',
        'total_episodes' => 37,
        'duration' => 24,
        'rating' => 9.0,
        'year' => 2006,
        'season' => 'Fall',
        'featured' => 1,
        'genres' => ['thriller', 'drama', 'psychological']
    ],
    [
        'title' => 'Your Name',
        'slug' => 'your-name',
        'synopsis' => 'Two high school students, Mitsuha and Taki, are complete strangers living separate lives. One day, they suddenly switch bodies and must navigate each other\'s lives.',
        'type' => 'Movie',
        'status' => 'Complete',
        'studio' => 'CoMix Wave Films',
        'total_episodes' => 1,
        'duration' => 106,
        'rating' => 8.4,
        'year' => 2016,
        'season' => 'Summer',
        'featured' => 1,
        'genres' => ['romance', 'drama', 'fantasy']
    ],
    [
        'title' => 'Demon Slayer',
        'slug' => 'demon-slayer',
        'synopsis' => 'After his family is slaughtered and his sister is turned into a demon, Tanjiro Kamado becomes a demon slayer to find a cure and avenge his family.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'Ufotable',
        'total_episodes' => 55,
        'duration' => 24,
        'rating' => 8.7,
        'year' => 2019,
        'season' => 'Spring',
        'featured' => 1,
        'genres' => ['action', 'drama', 'fantasy']
    ],
    [
        'title' => 'My Hero Academia',
        'slug' => 'my-hero-academia',
        'synopsis' => 'In a world where most humans have developed superpowers, Izuku Midoriya dreams of becoming a hero despite being born without powers.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'Bones',
        'total_episodes' => 138,
        'duration' => 24,
        'rating' => 8.5,
        'year' => 2016,
        'season' => 'Spring',
        'featured' => 0,
        'genres' => ['action', 'comedy', 'drama']
    ],
    [
        'title' => 'Spirited Away',
        'slug' => 'spirited-away',
        'synopsis' => 'Chihiro Ogino wanders into a world ruled by gods, witches, and spirits where humans are transformed into beasts.',
        'type' => 'Movie',
        'status' => 'Complete',
        'studio' => 'Studio Ghibli',
        'total_episodes' => 1,
        'duration' => 125,
        'rating' => 8.6,
        'year' => 2001,
        'season' => 'Summer',
        'featured' => 1,
        'genres' => ['fantasy', 'adventure', 'drama']
    ],
    [
        'title' => 'Tokyo Ghoul',
        'slug' => 'tokyo-ghoul',
        'synopsis' => 'Ken Kaneki is a normal college student until he meets a woman who reveals herself as a ghoul and turns his life upside down.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Studio Pierrot',
        'total_episodes' => 48,
        'duration' => 24,
        'rating' => 8.0,
        'year' => 2014,
        'season' => 'Summer',
        'featured' => 0,
        'genres' => ['action', 'horror', 'drama']
    ],
    [
        'title' => 'Dragon Ball Z',
        'slug' => 'dragon-ball-z',
        'synopsis' => 'Goku and his friends defend Earth against various villains ranging from intergalactic space fighters and conquerors to magical androids.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Toei Animation',
        'total_episodes' => 291,
        'duration' => 24,
        'rating' => 8.8,
        'year' => 1989,
        'season' => 'Spring',
        'featured' => 1,
        'genres' => ['action', 'adventure', 'comedy']
    ],
    [
        'title' => 'Steins;Gate',
        'slug' => 'steins-gate',
        'synopsis' => 'Self-proclaimed mad scientist Okabe Rintarou accidentally creates a time machine and must fix the consequences of changing the past.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'White Fox',
        'total_episodes' => 24,
        'duration' => 24,
        'rating' => 8.8,
        'year' => 2011,
        'season' => 'Spring',
        'featured' => 0,
        'genres' => ['sci-fi', 'thriller', 'drama']
    ],
    [
        'title' => 'Fullmetal Alchemist: Brotherhood',
        'slug' => 'fullmetal-alchemist-brotherhood',
        'synopsis' => 'Two brothers seek the Philosopher\'s Stone to restore their bodies after an attempt to revive their mother goes horribly wrong.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Bones',
        'total_episodes' => 64,
        'duration' => 24,
        'rating' => 9.0,
        'year' => 2009,
        'season' => 'Spring',
        'featured' => 1,
        'genres' => ['action', 'adventure', 'drama', 'fantasy']
    ],
    [
        'title' => 'Hunter x Hunter',
        'slug' => 'hunter-x-hunter',
        'synopsis' => 'Gon Freecss wants to become a Hunter like his father to find him. Along the way, he makes friends and faces dangerous challenges.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Madhouse',
        'total_episodes' => 148,
        'duration' => 24,
        'rating' => 9.0,
        'year' => 2011,
        'season' => 'Fall',
        'featured' => 0,
        'genres' => ['action', 'adventure', 'fantasy']
    ],
    [
        'title' => 'Attack on Titan: The Final Season',
        'slug' => 'attack-on-titan-final-season',
        'synopsis' => 'The final chapter of the battle between humanity and the titans as the truth about the world is revealed.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'MAPPA',
        'total_episodes' => 87,
        'duration' => 24,
        'rating' => 9.1,
        'year' => 2020,
        'season' => 'Winter',
        'featured' => 1,
        'genres' => ['action', 'drama', 'fantasy']
    ],
    [
        'title' => 'Jujutsu Kaisen',
        'slug' => 'jujutsu-kaisen',
        'synopsis' => 'Yuji Itadori joins a secret organization of Jujutsu Sorcerers to eliminate a cursed spirit after becoming its host.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'MAPPA',
        'total_episodes' => 47,
        'duration' => 24,
        'rating' => 8.5,
        'year' => 2020,
        'season' => 'Fall',
        'featured' => 1,
        'genres' => ['action', 'horror', 'fantasy']
    ],
    [
        'title' => 'Vinland Saga',
        'slug' => 'vinland-saga',
        'synopsis' => 'Thorfinn seeks revenge against the man who killed his father while becoming embroiled in the Viking invasion of England.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'Wit Studio',
        'total_episodes' => 48,
        'duration' => 24,
        'rating' => 8.8,
        'year' => 2019,
        'season' => 'Summer',
        'featured' => 0,
        'genres' => ['action', 'drama', 'historical']
    ],
    [
        'title' => 'Re:Zero',
        'slug' => 're-zero',
        'synopsis' => 'Subaru Natsuki is transported to another world where he gains the ability to return from death, but each return brings more suffering.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'White Fox',
        'total_episodes' => 50,
        'duration' => 24,
        'rating' => 8.4,
        'year' => 2016,
        'season' => 'Spring',
        'featured' => 0,
        'genres' => ['fantasy', 'thriller', 'drama']
    ],
    [
        'title' => 'One Punch Man',
        'slug' => 'one-punch-man',
        'synopsis' => 'Saitama is a hero who can defeat any enemy with a single punch, but he\'s bored with his overwhelming power.',
        'type' => 'TV',
        'status' => 'Ongoing',
        'studio' => 'Madhouse',
        'total_episodes' => 24,
        'duration' => 24,
        'rating' => 8.5,
        'year' => 2015,
        'season' => 'Fall',
        'featured' => 0,
        'genres' => ['action', 'comedy', 'superhero']
    ],
    [
        'title' => 'Cowboy Bebop',
        'slug' => 'cowboy-bebop',
        'synopsis' => 'Spike Spiegel and his crew of bounty hunters travel through space in their ship, the Bebop, hunting criminals.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Sunrise',
        'total_episodes' => 26,
        'duration' => 24,
        'rating' => 8.9,
        'year' => 1998,
        'season' => 'Spring',
        'featured' => 0,
        'genres' => ['sci-fi', 'action', 'drama']
    ],
    [
        'title' => 'Neon Genesis Evangelion',
        'slug' => 'neon-genesis-evangelion',
        'synopsis' => 'Teenage pilots control giant mechas called Evangelions to fight mysterious beings known as Angels.',
        'type' => 'TV',
        'status' => 'Complete',
        'studio' => 'Gainax',
        'total_episodes' => 26,
        'duration' => 24,
        'rating' => 8.3,
        'year' => 1995,
        'season' => 'Fall',
        'featured' => 0,
        'genres' => ['sci-fi', 'drama', 'mecha']
    ],
    [
        'title' => 'Princess Mononoke',
        'slug' => 'princess-mononoke',
        'synopsis' => 'A young warrior becomes involved in the struggle between forest gods and humans who consume its resources.',
        'type' => 'Movie',
        'status' => 'Complete',
        'studio' => 'Studio Ghibli',
        'total_episodes' => 1,
        'duration' => 134,
        'rating' => 8.4,
        'year' => 1997,
        'season' => 'Summer',
        'featured' => 0,
        'genres' => ['fantasy', 'adventure', 'drama']
    ]
];

foreach ($animes as $animeData) {
    // Insert anime
    $stmt = $pdo->prepare("INSERT IGNORE INTO animes (title, slug, synopsis, type, status, studio, total_episodes, duration, rating, year, season, featured, views, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $animeData['title'],
        $animeData['slug'],
        $animeData['synopsis'],
        $animeData['type'],
        $animeData['status'],
        $animeData['studio'],
        $animeData['total_episodes'],
        $animeData['duration'],
        $animeData['rating'],
        $animeData['year'],
        $animeData['season'],
        $animeData['featured'],
        rand(1000, 50000) // Random views
    ]);
    
    // Get anime ID
    $animeId = $pdo->lastInsertId();
    
    // Insert genre relationships
    foreach ($animeData['genres'] as $genreSlug) {
        if (isset($genreMap[$genreSlug])) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO anime_genre (anime_id, genre_id) VALUES (?, ?)");
            $stmt->execute([$animeId, $genreMap[$genreSlug]]);
        }
    }
    
    // Insert sample episodes (only for TV series)
    if ($animeData['type'] === 'TV') {
        $episodeCount = min($animeData['total_episodes'], 12); // Only add first 12 episodes
        
        for ($i = 1; $i <= $episodeCount; $i++) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO episodes (anime_id, episode_number, title, slug, video_url, duration, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $episodeTitle = "Episode " . $i;
            $episodeSlug = $animeData['slug'] . "-episode-" . $i;
            $videoUrl = "https://example.com/video/" . $episodeSlug . ".mp4";
            
            $stmt->execute([
                $animeId,
                $i,
                $episodeTitle,
                $episodeSlug,
                $videoUrl,
                $animeData['duration']
            ]);
        }
    }
}

echo "Sample data inserted successfully!";
echo "<br><a href='admin/login.php'>Go to Admin Panel</a>";
?>
-- Database: bellonime
CREATE DATABASE IF NOT EXISTS bellonime CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bellonime;

-- Table: genres (Genre Anime)
CREATE TABLE genres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: animes (Data Anime)
CREATE TABLE animes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    synopsis TEXT,
    poster VARCHAR(255),
    background VARCHAR(255),
    type ENUM('TV', 'Movie', 'OVA', 'ONA', 'Special') DEFAULT 'TV',
    status ENUM('Ongoing', 'Complete', 'Upcoming') DEFAULT 'Ongoing',
    studio VARCHAR(100),
    total_episodes INT DEFAULT 0,
    duration INT DEFAULT 0, -- dalam menit
    rating DECIMAL(3,2) DEFAULT 0.00,
    year INT,
    season ENUM('Spring', 'Summer', 'Fall', 'Winter') NULL,
    views INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: anime_genre (Relasi Anime dan Genre - Many to Many)
CREATE TABLE anime_genre (
    anime_id INT,
    genre_id INT,
    PRIMARY KEY (anime_id, genre_id),
    FOREIGN KEY (anime_id) REFERENCES animes(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

-- Table: episodes (Episode Anime)
CREATE TABLE episodes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    anime_id INT NOT NULL,
    episode_number INT NOT NULL,
    title VARCHAR(255),
    slug VARCHAR(255) NOT NULL,
    video_url VARCHAR(500),
    video_embed TEXT,
    duration INT DEFAULT 0, -- dalam menit
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (anime_id) REFERENCES animes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_episode (anime_id, episode_number)
);

-- Table: admins (Admin Panel)
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: users (Users - Optional)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255),
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: watchlist (Watchlist User - Optional)
CREATE TABLE watchlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    anime_id INT NOT NULL,
    status ENUM('watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch') DEFAULT 'plan_to_watch',
    episodes_watched INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (anime_id) REFERENCES animes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_watchlist (user_id, anime_id)
);

-- Table: comments (Comments - Optional)
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    anime_id INT NOT NULL,
    episode_id INT NULL,
    content TEXT NOT NULL,
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (anime_id) REFERENCES animes(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE SET NULL
);

-- Insert default data
-- Insert default genres
INSERT INTO genres (name, slug, description) VALUES
('Action', 'action', 'Anime dengan adegan pertarungan dan aksi yang intens'),
('Adventure', 'adventure', 'Anime dengan petualangan ke tempat baru'),
('Comedy', 'comedy', 'Anime yang lucu dan menghibur'),
('Drama', 'drama', 'Anime dengan cerita yang emosional'),
('Fantasy', 'fantasy', 'Anime dengan elemen magis dan dunia fantasi'),
('Horror', 'horror', 'Anime yang menakutkan'),
('Mystery', 'mystery', 'Anime dengan teka-teki yang harus dipecahkan'),
('Romance', 'romance', 'Anime dengan cerita cinta'),
('Sci-Fi', 'sci-fi', 'Anime dengan tema sains dan teknologi'),
('Slice of Life', 'slice-of-life', 'Anime dengan kehidupan sehari-hari'),
('Sports', 'sports', 'Anime tentang olahraga'),
('Supernatural', 'supernatural', 'Anime dengan kekuatan supernatural'),
('Thriller', 'thriller', 'Anime yang tegang dan mendebarkan'),
('Isekai', 'isekai', 'Anime dengan tema dunia lain');

-- Insert default admin (password: admin123)
INSERT INTO admins (username, email, password, full_name, role) VALUES
('admin', 'admin@bellonime.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin');

-- Create indexes for better performance
CREATE INDEX idx_animes_title ON animes(title);
CREATE INDEX idx_animes_slug ON animes(slug);
CREATE INDEX idx_animes_status ON animes(status);
CREATE INDEX idx_animes_featured ON animes(featured);
CREATE INDEX idx_episodes_anime_id ON episodes(anime_id);
CREATE INDEX idx_episodes_slug ON episodes(slug);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_comments_anime_id ON comments(anime_id);
CREATE INDEX idx_comments_episode_id ON comments(episode_id);
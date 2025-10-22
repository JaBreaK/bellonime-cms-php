<?php
require_once 'includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

$pageTitle = 'Manajemen Genre';

// Handle form actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        handleCreateGenre();
        break;
    case 'edit':
        if ($id) {
            handleEditGenre($id);
        } else {
            setFlashMessage('error', 'ID genre tidak valid');
            redirect(ADMIN_URL . 'manage-genre.php');
        }
        break;
    case 'delete':
        if ($id) {
            handleDeleteGenre($id);
        } else {
            setFlashMessage('error', 'ID genre tidak valid');
            redirect(ADMIN_URL . 'manage-genre.php');
        }
        break;
    default:
        showGenreList();
        break;
}

function handleCreateGenre() {
    global $pageTitle;
    $pageTitle = 'Tambah Genre';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitize($_POST['name']);
        $slug = createSlug($name);
        $description = sanitize($_POST['description']);
        
        // Validate input
        if (empty($name)) {
            setFlashMessage('error', 'Nama genre harus diisi');
        } else {
            $db = Database::getInstance()->getConnection();
            
            // Check if genre already exists
            $stmt = $db->prepare("SELECT id FROM genres WHERE name = :name OR slug = :slug");
            $stmt->execute([':name' => $name, ':slug' => $slug]);
            if ($stmt->fetch()) {
                setFlashMessage('error', 'Genre dengan nama tersebut sudah ada');
            } else {
                // Insert genre
                $sql = "INSERT INTO genres (name, slug, description) VALUES (:name, :slug, :description)";
                
                $stmt = $db->prepare($sql);
                $params = [
                    ':name' => $name,
                    ':slug' => $slug,
                    ':description' => $description
                ];
                
                if ($stmt->execute($params)) {
                    setFlashMessage('success', 'Genre berhasil ditambahkan');
                    redirect(ADMIN_URL . 'manage-genre.php');
                } else {
                    setFlashMessage('error', 'Gagal menambahkan genre');
                }
            }
        }
    }
    
    include 'includes/header.php';
    include 'genre-form.php';
    include 'includes/footer.php';
}

function handleEditGenre($id) {
    global $pageTitle;
    $pageTitle = 'Edit Genre';
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM genres WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $genre = $stmt->fetch();
    
    if (!$genre) {
        setFlashMessage('error', 'Genre tidak ditemukan');
        redirect(ADMIN_URL . 'manage-genre.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = sanitize($_POST['name']);
        $slug = createSlug($name);
        $description = sanitize($_POST['description']);
        
        // Validate input
        if (empty($name)) {
            setFlashMessage('error', 'Nama genre harus diisi');
        } else {
            // Check if genre already exists (excluding current genre)
            $stmt = $db->prepare("SELECT id FROM genres WHERE (name = :name OR slug = :slug) AND id != :id");
            $stmt->execute([':name' => $name, ':slug' => $slug, ':id' => $id]);
            if ($stmt->fetch()) {
                setFlashMessage('error', 'Genre dengan nama tersebut sudah ada');
            } else {
                // Update genre
                $sql = "UPDATE genres SET name = :name, slug = :slug, description = :description WHERE id = :id";
                
                $stmt = $db->prepare($sql);
                $params = [
                    ':name' => $name,
                    ':slug' => $slug,
                    ':description' => $description,
                    ':id' => $id
                ];
                
                if ($stmt->execute($params)) {
                    setFlashMessage('success', 'Genre berhasil diperbarui');
                    redirect(ADMIN_URL . 'manage-genre.php');
                } else {
                    setFlashMessage('error', 'Gagal memperbarui genre');
                }
            }
        }
    }
    
    include 'includes/header.php';
    include 'genre-form.php';
    include 'includes/footer.php';
}

function handleDeleteGenre($id) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM genres WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $genre = $stmt->fetch();
    
    if (!$genre) {
        setFlashMessage('error', 'Genre tidak ditemukan');
        redirect(ADMIN_URL . 'manage-genre.php');
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Check if genre is being used by any anime
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM anime_genre WHERE genre_id = :genre_id");
            $stmt->execute([':genre_id' => $id]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                setFlashMessage('error', 'Genre tidak dapat dihapus karena masih digunakan oleh ' . $count . ' anime');
            } else {
                // Delete genre
                $stmt = $db->prepare("DELETE FROM genres WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                setFlashMessage('success', 'Genre berhasil dihapus');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'Gagal menghapus genre: ' . $e->getMessage());
        }
    }
    
    redirect(ADMIN_URL . 'manage-genre.php');
}

function showGenreList() {
    $page = (int)($_GET['page'] ?? 1);
    $perPage = 20;
    $search = $_GET['search'] ?? '';
    
    $db = Database::getInstance()->getConnection();
    
    // Build query
    $sql = "SELECT g.*, 
            (SELECT COUNT(*) FROM anime_genre WHERE genre_id = g.id) as anime_count
            FROM genres g";
    
    $conditions = [];
    $params = [];
    
    if (!empty($search)) {
        $conditions[] = "(g.name LIKE :search OR g.description LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $sql .= " ORDER BY g.name ASC";
    
    // Get paginated results
    $result = paginate($sql, $page, $perPage, $params);
    
    include 'includes/header.php';
    include 'genre-list.php';
    include 'includes/footer.php';
}
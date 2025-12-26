<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'bellonime');
define('DB_USER', 'root');
define('DB_PASS', '');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Base URL configuration
define('BASE_URL', '/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('ASSETS_URL', BASE_URL . 'assets/');

// File upload configuration removed; images are external URLs only

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session
session_start();

// Database connection class
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning of the instance
    private function __clone() {}
    
    // Prevent unserialization of the instance
    public function __wakeup() {}
}

// Helper functions
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeFilename($filename) {
    // Remove any character that is not alphanumeric, space, hyphen, or underscore
    $filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $filename);
    // Replace spaces with hyphens
    $filename = preg_replace('/\s+/', '-', $filename);
    // Convert to lowercase
    return strtolower($filename);
}

function createSlug($text) {
    // Convert to lowercase
    $text = strtolower($text);
    // Replace spaces with hyphens
    $text = preg_replace('/\s+/', '-', $text);
    // Remove any character that is not alphanumeric, hyphen, or underscore
    $text = preg_replace('/[^a-z0-9\-_]/', '', $text);
    // Remove multiple hyphens
    $text = preg_replace('/-+/', '-', $text);
    // Remove hyphens at the beginning and end
    return trim($text, '-');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login to access this page';
        redirect(ADMIN_URL . 'login.php');
    }
}

/* uploadFile removed: project uses external image URLs only */

function paginate($query, $page = 1, $perPage = 10, $params = []) {
    $page = max(1, (int)$page);
    $offset = ($page - 1) * $perPage;
    
    $db = Database::getInstance()->getConnection();
    
    // Get total records
    $countQuery = "SELECT COUNT(*) as total FROM ($query) as count_table";
    $stmt = $db->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get paginated results
    $limitQuery = $query . " LIMIT $perPage OFFSET $offset";
    $stmt = $db->prepare($limitQuery);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    return [
        'data' => $results,
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => ceil($total / $perPage),
        'hasNextPage' => $page < ceil($total / $perPage),
        'hasPrevPage' => $page > 1
    ];
}

// Flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}

function hasFlashMessage($type) {
    return isset($_SESSION['flash'][$type]);
}
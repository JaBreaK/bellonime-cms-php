<?php
// Database Setup Helper
session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? 'bellonime';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    
    try {
        // Test connection
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Select the database
        $pdo->exec("USE `$db_name`");
        
        // Read SQL file
        $sql = file_get_contents('database.sql');
        
        // Remove comments and split statements
        $sql = preg_replace('/--.*$/m', '', $sql);
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        // Execute statements
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Create admin user
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'admin', NOW())");
        $stmt->execute(['admin', 'admin@bellonime.com', $password]);
        
        // Insert sample data
        include 'seed-data.php';
        
        $success = "Database berhasil diinstall! Admin user: admin / admin123. Sample data telah ditambahkan.";
        
        // Update connection.php with new credentials
        $connection_content = "<?php\n";
        $connection_content .= "// Database Configuration\n";
        $connection_content .= "define('DB_HOST', '$db_host');\n";
        $connection_content .= "define('DB_NAME', '$db_name');\n";
        $connection_content .= "define('DB_USER', '$db_user');\n";
        $connection_content .= "define('DB_PASS', '$db_pass');\n\n";
        
        $connection_content .= "// Base URL\n";
        $connection_content .= "\$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';\n";
        $connection_content .= "\$host = \$_SERVER['HTTP_HOST'];\n";
        $connection_content .= "\$path = rtrim(dirname(\$_SERVER['PHP_SELF']), '/\\');\n";
        $connection_content .= "define('BASE_URL', \$protocol . '://' . \$host . \$path . '/');\n";
        $connection_content .= "define('ADMIN_URL', BASE_URL . 'admin/');\n";
        $connection_content .= "define('ASSETS_URL', BASE_URL . 'assets/');\n\n";
        
        $connection_content .= "// Database Connection\n";
        $connection_content .= "try {\n";
        $connection_content .= "    \$pdo = new PDO(\"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8mb4\", DB_USER, DB_PASS);\n";
        $connection_content .= "    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
        $connection_content .= "} catch (PDOException \$e) {\n";
        $connection_content .= "    die(\"Connection failed: \" . \$e->getMessage());\n";
        $connection_content .= "}\n\n";
        
        $connection_content .= "// Start session\n";
        $connection_content .= "if (session_status() === PHP_SESSION_NONE) {\n";
        $connection_content .= "    session_start();\n";
        $connection_content .= "}\n\n";
        
        $connection_content .= "// Helper functions\n";
        $connection_content .= "function redirect(\$url) {\n";
        $connection_content .= "    header(\"Location: \$url\");\n";
        $connection_content .= "    exit();\n";
        $connection_content .= "}\n\n";
        
        $connection_content .= "function setFlashMessage(\$type, \$message) {\n";
        $connection_content .= "    \$_SESSION['flash'][\$type] = \$message;\n";
        $connection_content .= "}\n\n";
        
        $connection_content .= "function getFlashMessage(\$type) {\n";
        $connection_content .= "    if (isset(\$_SESSION['flash'][\$type])) {\n";
        $connection_content .= "        \$message = \$_SESSION['flash'][\$type];\n";
        $connection_content .= "        unset(\$_SESSION['flash'][\$type]);\n";
        $connection_content .= "        return \$message;\n";
        $connection_content .= "    }\n";
        $connection_content .= "    return '';\n";
        $connection_content .= "}\n";
        
        file_put_contents('core/connection.php', $connection_content);
        
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bellonime - Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Bellonime Setup</h1>
            <p class="text-gray-600 mt-2">Install database dan konfigurasi awal</p>
        </div>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $success ?>
            </div>
            <div class="text-center">
                <a href="admin/login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Go to Admin Panel
                </a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="db_host">
                        Database Host
                    </label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="db_name">
                        Database Name
                    </label>
                    <input type="text" id="db_name" name="db_name" value="bellonime" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="db_user">
                        Database User
                    </label>
                    <input type="text" id="db_user" name="db_user" value="root" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="db_pass">
                        Database Password
                    </label>
                    <input type="password" id="db_pass" name="db_pass"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                    Install Database
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
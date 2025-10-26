<?php
// Admin-side HXFile lookup endpoint
// Client can call this AFTER a direct-to-HXFile upload to resolve filecode by original filename
// Request: POST { name: "original-file-name.mp4" }
// Response: { success: true, filecode, embed_url, download_url } or { success: false, error }

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        respond(['success' => false, 'error' => 'Method not allowed'], 405);
    }

    $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
    if ($name === '') {
        respond(['success' => false, 'error' => 'Missing parameter: name'], 400);
    }

    // Try multiple attempts allowing HXFile to index the new upload
    $attempts = 5;
    $delayMs  = 400; // 0.4s between attempts
    $code     = '';

    for ($i = 0; $i < $attempts; $i++) {
        $code = hxfileFindFileCodeByName($name, 100);
        if ($code !== '') {
            break;
        }
        usleep($delayMs * 1000);
    }

    if ($code === '') {
        respond(['success' => false, 'error' => 'Filecode not found by filename'], 200);
    }

    $embed = hxfileBuildEmbedUrl($code);
    $download = hxfileBuildDownloadUrl($code);

    respond([
        'success' => true,
        'filecode' => $code,
        'embed_url' => $embed,
        'download_url' => $download,
    ], 200);

} catch (Throwable $e) {
    respond(['success' => false, 'error' => 'Lookup error', 'details' => $e->getMessage()], 500);
}
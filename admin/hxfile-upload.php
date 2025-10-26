<?php
// Admin-side HXFile AJAX upload endpoint
// Usage: POST multipart/form-data with field "file" (video), optional "quality" (480|720|1080)
// Returns JSON: { success: true, filecode, embed_url, download_url } or { success: false, error, details }

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../core/functions.php';

// Increase limits for large uploads (adjust as needed)
@ini_set('upload_max_filesize','512M');
@ini_set('post_max_size','512M');
@ini_set('memory_limit','1024M');
@ini_set('max_execution_time','600');
@ini_set('max_input_time','600');

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

    // Basic checks
    if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
        respond(['success' => false, 'error' => 'No file uploaded'], 400);
    }

    $quality = isset($_POST['quality']) ? trim((string)$_POST['quality']) : '';
    if ($quality !== '' && !in_array($quality, ['480','720','1080'], true)) {
        // Non-fatal; only for UI hints
        $quality = '';
    }

    // Perform upload using the same flow as coba.php
    $result = hxfileUploadLocalFromFilesArray($_FILES['file']);

    $code = $result['filecode'] ?? '';
    if ($code === '') {
        // Return snippet for debugging
        $snippet = substr(strip_tags((string)($result['raw_response'] ?? '')), 0, 400);
        respond([
            'success' => false,
            'error' => 'Filecode not found in upload response',
            'details' => [
                'http_code' => $result['http_code'] ?? null,
                'raw_snippet' => $snippet,
            ],
            'quality' => $quality,
        ], 200);
    }

    respond([
        'success' => true,
        'filecode' => $code,
        'embed_url' => $result['embed_url'] ?? ('https://xshotcok.com/embed-' . $code . '.html'),
        'download_url' => $result['download_url'] ?? ('https://hxfile.co/' . $code),
        'quality' => $quality,
    ], 200);

} catch (Throwable $e) {
    respond([
        'success' => false,
        'error' => 'HXFile upload error',
        'details' => $e->getMessage(),
    ], 500);
}
<?php
// Return HXFile upload server ticket without exposing API key to the client.
// Response JSON: { success, server_url, sess_id }
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
    $info = hxfileGetUploadServer();
    if (!is_array($info) || empty($info['server_url']) || empty($info['sess_id'])) {
        respond(['success' => false, 'error' => 'Failed to get upload server'], 200);
    }
    respond([
        'success' => true,
        'server_url' => $info['server_url'],
        'sess_id' => $info['sess_id'],
    ], 200);
} catch (Throwable $e) {
    respond(['success' => false, 'error' => 'Ticket error', 'details' => $e->getMessage()], 500);
}
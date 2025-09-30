<?php
// Simple 500 error diagnostic
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    echo json_encode([
        'status' => 'ok',
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s'),
        'memory_limit' => ini_get('memory_limit'),
        'upload_max' => ini_get('upload_max_filesize'),
        'post_max' => ini_get('post_max_size'),
        'file_uploads' => ini_get('file_uploads') ? 'enabled' : 'disabled',
        'test' => 'Simple diagnostic successful'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
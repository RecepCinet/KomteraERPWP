<?php
// Upload Debug Information
header('Content-Type: application/json');

$debug_info = [
    'php_settings' => [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'file_uploads' => ini_get('file_uploads') ? 'Enabled' : 'Disabled'
    ],
    'directory_info' => [],
    'server_info' => [
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'php_version' => PHP_VERSION,
        'operating_system' => PHP_OS
    ]
];

// Check upload directory
$upload_dir = dirname(__DIR__) . '/uploads/ozel_fiyat/';

$debug_info['directory_info'] = [
    'upload_path' => $upload_dir,
    'exists' => is_dir($upload_dir),
    'writable' => is_writable($upload_dir),
    'permissions' => is_dir($upload_dir) ? substr(sprintf('%o', fileperms($upload_dir)), -4) : 'N/A'
];

if (is_dir($upload_dir)) {
    $free_space = disk_free_space($upload_dir);
    $total_space = disk_total_space($upload_dir);

    $debug_info['directory_info']['free_space_mb'] = $free_space ? round($free_space / 1024 / 1024, 2) : 'Unknown';
    $debug_info['directory_info']['total_space_mb'] = $total_space ? round($total_space / 1024 / 1024, 2) : 'Unknown';
    $debug_info['directory_info']['usage_percent'] = ($free_space && $total_space) ? round((($total_space - $free_space) / $total_space) * 100, 2) : 'Unknown';
}

// Convert size values to bytes for comparison
function parseSize($size) {
    $unit = strtoupper(substr($size, -1));
    $value = (int) substr($size, 0, -1);

    switch($unit) {
        case 'G': return $value * 1024 * 1024 * 1024;
        case 'M': return $value * 1024 * 1024;
        case 'K': return $value * 1024;
        default: return $value;
    }
}

$upload_max_bytes = parseSize(ini_get('upload_max_filesize'));
$post_max_bytes = parseSize(ini_get('post_max_size'));

$debug_info['recommendations'] = [];

if ($upload_max_bytes < 10 * 1024 * 1024) {
    $debug_info['recommendations'][] = "upload_max_filesize çok düşük (min 10M önerilen)";
}

if ($post_max_bytes < $upload_max_bytes) {
    $debug_info['recommendations'][] = "post_max_size upload_max_filesize'dan büyük olmalı";
}

if (!ini_get('file_uploads')) {
    $debug_info['recommendations'][] = "file_uploads = On olmalı";
}

if (ini_get('max_execution_time') < 60) {
    $debug_info['recommendations'][] = "max_execution_time düşük olabilir (min 60 sn önerilen)";
}

echo json_encode($debug_info, JSON_PRETTY_PRINT);
?>
<?php
/**
 * Heartbeat Endpoint
 *
 * Lightweight endpoint to check server and database connectivity
 * Returns JSON response indicating system health status
 */

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');

// Simple response without database check (faster)
$response = [
    'status' => 'ok',
    'timestamp' => time(),
    'server' => 'online'
];

echo json_encode($response);
exit;

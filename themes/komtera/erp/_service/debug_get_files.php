<?php
// Minimal debug version of get_ozel_fiyat_files.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

try {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    // Check basic parameters first
    if (!isset($_GET['teklif_no'])) {
        throw new Exception('Missing teklif_no parameter');
    }

    $teklif_no = $_GET['teklif_no'];

    // Check paths - try multiple possible WordPress locations
    $possible_wp_paths = [
        dirname(__DIR__, 5) . '/wp-load.php',  // /home/utopia/htdocs/erptest.komtera.com/wp-load.php
        dirname(__DIR__, 4) . '/wp-load.php',  // /home/utopia/htdocs/erptest.komtera.com/wp-content/wp-load.php (current incorrect path)
        dirname(__DIR__, 3) . '/wp-load.php',  // Other possible location
    ];

    $wp_load_path = null;
    foreach ($possible_wp_paths as $path) {
        if (file_exists($path)) {
            $wp_load_path = $path;
            break;
        }
    }
    $conn_path = dirname(__DIR__) . '/_conn.php';

    $debug_info = [
        'status' => 'starting',
        'teklif_no' => $teklif_no,
        'possible_wp_paths' => $possible_wp_paths,
        'wp_load_path' => $wp_load_path,
        'wp_load_exists' => $wp_load_path ? file_exists($wp_load_path) : false,
        'conn_path' => $conn_path,
        'conn_exists' => file_exists($conn_path),
        'php_version' => PHP_VERSION,
        'current_dir' => __DIR__,
        'server_time' => date('Y-m-d H:i:s')
    ];

    // Try WordPress loading
    if (!$wp_load_path) {
        throw new Exception("WordPress wp-load.php not found in any of these locations: " . implode(', ', $possible_wp_paths));
    }

    $debug_info['wp_load_attempt'] = 'starting';
    require_once($wp_load_path);
    $debug_info['wp_load_attempt'] = 'success';

    // Try database connection
    if (!file_exists($conn_path)) {
        throw new Exception("Database connection file not found at: $conn_path");
    }

    $debug_info['conn_attempt'] = 'starting';
    include $conn_path;

    if (!isset($conn)) {
        throw new Exception("Database connection variable \$conn not set");
    }

    $debug_info['conn_attempt'] = 'success';
    $debug_info['conn_type'] = get_class($conn);

    // Simple response for now
    echo json_encode([
        'success' => true,
        'message' => 'Debug test successful',
        'debug' => $debug_info,
        'files' => [],
        'count' => 0
    ]);

} catch (Exception $e) {
    // Clear any output buffer
    ob_clean();

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $debug_info ?? [],
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    // Clear any output buffer
    ob_clean();

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage(),
        'debug' => $debug_info ?? [],
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Throwable $e) {
    // Clear any output buffer
    ob_clean();

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'debug' => $debug_info ?? [],
        'type' => get_class($e)
    ]);
} finally {
    // End output buffering
    ob_end_flush();
}
?>
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// WordPress context - with error handling
// Try multiple possible WordPress locations
$possible_wp_paths = [
    dirname(__DIR__, 5) . '/wp-load.php',  // Correct path: /home/utopia/htdocs/erptest.komtera.com/wp-load.php
    dirname(__DIR__, 4) . '/wp-load.php',  // Incorrect path we were using
    dirname(__DIR__, 3) . '/wp-load.php',  // Other possible location
];

$wp_load_path = null;
foreach ($possible_wp_paths as $path) {
    if (file_exists($path)) {
        $wp_load_path = $path;
        break;
    }
}
if (!$wp_load_path) {
    throw new Exception("WordPress wp-load.php not found in any of these locations: " . implode(', ', $possible_wp_paths));
}

try {
    require_once($wp_load_path);
} catch (Exception $e) {
    throw new Exception("WordPress loading failed: " . $e->getMessage());
}

// Database connection - with error handling
$conn_path = dirname(__DIR__) . '/_conn.php';
if (!file_exists($conn_path)) {
    throw new Exception("Database connection file not found at: $conn_path");
}

try {
    include $conn_path;
    if (!isset($conn)) {
        throw new Exception("Database connection variable \$conn not set after including connection file");
    }
} catch (Exception $e) {
    throw new Exception("Database connection failed: " . $e->getMessage());
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST method allowed');
    }

    parse_str(file_get_contents('php://input'), $_POST);

    if (!isset($_POST['file_id']) || !isset($_POST['teklif_no'])) {
        throw new Exception('Missing file_id or teklif_no parameter');
    }

    $file_id = intval($_POST['file_id']);
    $teklif_no = sanitize_text_field($_POST['teklif_no']);

    // Get file info before deletion
    $sql = "SELECT FILE_PATH, FILE_NAME FROM aa_erp_kt_teklif_dosyalar
            WHERE id = ? AND TEKLIF_NO = ? AND (SIL IS NULL OR SIL = 0)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$file_id, $teklif_no]);
    $file_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file_info) {
        throw new Exception('Dosya bulunamadı');
    }

    // Mark as deleted in database (soft delete)
    $delete_sql = "UPDATE aa_erp_kt_teklif_dosyalar
                   SET SIL = 1, DELETED_DATE = GETDATE(), DELETED_BY = ?
                   WHERE id = ? AND TEKLIF_NO = ?";

    $delete_stmt = $conn->prepare($delete_sql);
    $result = $delete_stmt->execute([
        wp_get_current_user()->user_login ?? 'system',
        $file_id,
        $teklif_no
    ]);

    if (!$result) {
        throw new Exception('Veritabanı silme hatası');
    }

    // Try to delete physical file (optional - might want to keep for recovery)
    $physical_file_path = dirname(__DIR__) . '/uploads/ozel_fiyat/' . $file_info['FILE_PATH'];
    if (file_exists($physical_file_path)) {
        @unlink($physical_file_path); // @ to suppress errors if file deletion fails
    }

    echo json_encode([
        'success' => true,
        'message' => 'Dosya başarıyla silindi'
    ]);

} catch (Exception $e) {
    http_response_code(400);

    // Enhanced error logging
    $error_details = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
        'post_params' => $_POST ?? [],
        'wp_load_path' => $wp_load_path ?? 'not set',
        'conn_path' => $conn_path ?? 'not set'
    ];

    error_log('Delete File Error Details: ' . json_encode($error_details));

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'debug' => $error_details
    ]);
} catch (Error $e) {
    http_response_code(500);

    // Enhanced fatal error logging
    $fatal_details = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'php_version' => PHP_VERSION,
        'memory_usage' => memory_get_usage(true),
        'memory_limit' => ini_get('memory_limit')
    ];

    error_log('Delete File Fatal Error Details: ' . json_encode($fatal_details));

    echo json_encode([
        'success' => false,
        'error' => 'Fatal server error: ' . $e->getMessage(),
        'debug' => $fatal_details
    ]);
} catch (Throwable $e) {
    // Catch any other throwable errors
    http_response_code(500);

    $throwable_details = [
        'type' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];

    error_log('Delete File Throwable Error: ' . json_encode($throwable_details));

    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'debug' => $throwable_details
    ]);
}
?>
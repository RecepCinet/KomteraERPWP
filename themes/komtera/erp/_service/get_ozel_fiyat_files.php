<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

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
    if (!isset($_GET['teklif_no'])) {
        throw new Exception('Missing teklif_no parameter');
    }

    $teklif_no = sanitize_text_field($_GET['teklif_no']);

    // Create table if not exists
    $create_table_sql = "
        IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='" . getTableName('aa_erp_kt_teklif_dosyalar') . "' AND xtype='U')
        CREATE TABLE " . getTableName('aa_erp_kt_teklif_dosyalar') . " (
            id INT IDENTITY(1,1) PRIMARY KEY,
            TEKLIF_NO NVARCHAR(50) NOT NULL,
            ORIGINAL_NAME NVARCHAR(255) NOT NULL,
            FILE_NAME NVARCHAR(255) NOT NULL,
            FILE_PATH NVARCHAR(500) NOT NULL,
            FILE_SIZE BIGINT NOT NULL,
            FILE_TYPE NVARCHAR(100) NOT NULL,
            UPLOAD_DATE DATETIME NOT NULL DEFAULT GETDATE(),
            UPLOADED_BY NVARCHAR(100),
            SIL BIT DEFAULT 0
        )";

    $conn->exec($create_table_sql);

    // Get files for this teklif
    $sql = "SELECT
                id,
                ORIGINAL_NAME as original_name,
                FILE_NAME as file_name,
                FILE_PATH as file_path,
                FILE_SIZE as file_size,
                FILE_TYPE as file_type,
                UPLOAD_DATE as upload_date,
                UPLOADED_BY as uploaded_by
            FROM " . getTableName('aa_erp_kt_teklif_dosyalar') . "
            WHERE TEKLIF_NO = ? AND (SIL IS NULL OR SIL = 0)
            ORDER BY UPLOAD_DATE DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$teklif_no]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process files data
    $processed_files = [];
    foreach ($files as $file) {
        $extension = pathinfo($file['original_name'], PATHINFO_EXTENSION);

        // Format upload date
        $upload_date = '';
        if ($file['upload_date']) {
            try {
                $date = new DateTime($file['upload_date']);
                $upload_date = $date->format('d.m.Y H:i');
            } catch (Exception $e) {
                $upload_date = $file['upload_date'];
            }
        }

        $processed_files[] = [
            'id' => $file['id'],
            'original_name' => $file['original_name'],
            'file_name' => $file['file_name'],
            'file_path' => $file['file_path'],
            'file_size' => intval($file['file_size']),
            'file_type' => $file['file_type'],
            'extension' => $extension,
            'upload_date' => $upload_date,
            'uploaded_by' => $file['uploaded_by']
        ];
    }

    echo json_encode([
        'success' => true,
        'files' => $processed_files,
        'count' => count($processed_files)
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
        'get_params' => $_GET ?? [],
        'wp_load_path' => $wp_load_path ?? 'not set',
        'conn_path' => $conn_path ?? 'not set'
    ];

    error_log('Get Files Error Details: ' . json_encode($error_details));

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'files' => [],
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

    error_log('Get Files Fatal Error Details: ' . json_encode($fatal_details));

    echo json_encode([
        'success' => false,
        'error' => 'Fatal server error: ' . $e->getMessage(),
        'files' => [],
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

    error_log('Get Files Throwable Error: ' . json_encode($throwable_details));

    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'files' => [],
        'debug' => $throwable_details
    ]);
}
?>
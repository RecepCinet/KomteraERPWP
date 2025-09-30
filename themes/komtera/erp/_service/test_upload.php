<?php
// Basic test to identify 500 error source
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== UPLOAD TEST START ===\n";

try {
    echo "1. PHP Version: " . PHP_VERSION . "\n";

    echo "2. Testing WordPress load...\n";
    if (file_exists(dirname(__DIR__, 4) . '/wp-load.php')) {
        require_once(dirname(__DIR__, 4) . '/wp-load.php');
        echo "   ✓ WordPress loaded\n";
    } else {
        echo "   ✗ WordPress wp-load.php not found\n";
        exit;
    }

    echo "3. Testing database connection...\n";
    $conn_file = dirname(__DIR__) . '/_conn.php';
    if (file_exists($conn_file)) {
        include $conn_file;
        echo "   ✓ Connection file exists\n";

        if (isset($conn)) {
            echo "   ✓ Database connection established\n";
        } else {
            echo "   ✗ Database connection variable not set\n";
        }
    } else {
        echo "   ✗ Connection file not found: $conn_file\n";
    }

    echo "4. Testing upload directory...\n";
    $upload_dir = dirname(__DIR__) . '/uploads/ozel_fiyat/';
    echo "   Upload dir: $upload_dir\n";
    echo "   Exists: " . (is_dir($upload_dir) ? 'YES' : 'NO') . "\n";
    echo "   Writable: " . (is_writable($upload_dir) ? 'YES' : 'NO') . "\n";

    echo "5. Testing file upload settings...\n";
    echo "   file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "\n";
    echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "   post_max_size: " . ini_get('post_max_size') . "\n";
    echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";

    echo "6. Testing $_FILES...\n";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "   POST method detected\n";
        echo "   FILES array: " . print_r($_FILES, true) . "\n";
        echo "   POST array: " . print_r($_POST, true) . "\n";
    } else {
        echo "   GET method - no file upload test\n";
    }

    echo "7. Testing table creation...\n";
    if (isset($conn)) {
        $create_sql = "
            IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='aa_erp_kt_teklif_dosyalar' AND xtype='U')
            CREATE TABLE aa_erp_kt_teklif_dosyalar (
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

        try {
            $conn->exec($create_sql);
            echo "   ✓ Table creation/check successful\n";
        } catch (Exception $e) {
            echo "   ✗ Table error: " . $e->getMessage() . "\n";
        }
    }

    echo "=== TEST COMPLETED SUCCESSFULLY ===\n";

} catch (Throwable $e) {
    echo "=== FATAL ERROR ===\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
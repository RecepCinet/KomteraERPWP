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

    // Check PHP upload settings first
    $upload_max = ini_get('upload_max_filesize');
    $post_max = ini_get('post_max_size');
    $memory_limit = ini_get('memory_limit');

    if (!isset($_FILES['file']) || !isset($_POST['teklif_no'])) {
        throw new Exception('Missing file or teklif_no parameter');
    }

    // Check for upload errors
    if (isset($_FILES['file']['error']) && $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => "Dosya çok büyük (PHP upload_max_filesize: {$upload_max})",
            UPLOAD_ERR_FORM_SIZE => "Dosya çok büyük (HTML form limiti)",
            UPLOAD_ERR_PARTIAL => "Dosya kısmen yüklendi. Tekrar deneyin.",
            UPLOAD_ERR_NO_FILE => "Dosya seçilmemiş",
            UPLOAD_ERR_NO_TMP_DIR => "Geçici klasör bulunamadı",
            UPLOAD_ERR_CANT_WRITE => "Dosya yazma hatası (disk izinleri)",
            UPLOAD_ERR_EXTENSION => "PHP uzantısı dosya yüklemeyi engelledi"
        ];

        $error_message = $upload_errors[$_FILES['file']['error']] ?? 'Bilinmeyen upload hatası: ' . $_FILES['file']['error'];
        throw new Exception($error_message);
    }

    $teklif_no = sanitize_text_field($_POST['teklif_no']);
    $file = $_FILES['file'];

    // Validate file
    $allowed_types = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
    $max_size = 10 * 1024 * 1024; // 10MB

    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Dosya türü desteklenmiyor. PDF, PNG, JPEG dosyaları yükleyebilirsiniz.');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('Dosya çok büyük. Maksimum 10MB yükleyebilirsiniz.');
    }

    // Create upload directory
    $upload_dir = dirname(__DIR__) . '/uploads/ozel_fiyat/';
    if (!is_dir($upload_dir)) {
        $created = wp_mkdir_p($upload_dir);
        if (!$created) {
            throw new Exception('Upload klasörü oluşturulamadı. Server izinlerini kontrol edin.');
        }
    }

    // Check directory permissions
    if (!is_writable($upload_dir)) {
        throw new Exception('Upload klasörü yazılamaz durumda. Klasör izinlerini kontrol edin (755 veya 775 olmalı).');
    }

    // Check available disk space
    $free_space = disk_free_space($upload_dir);
    $required_space = $file['size'] * 2; // Safety margin
    if ($free_space !== false && $free_space < $required_space) {
        throw new Exception('Disk alanı yetersiz. ' . round($free_space / 1024 / 1024, 2) . 'MB mevcut, ' . round($required_space / 1024 / 1024, 2) . 'MB gerekli.');
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $teklif_no . '_' . time() . '_' . wp_generate_password(8, false) . '.' . $extension;
    $file_path = $upload_dir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Dosya yükleme hatası');
    }

    // Save to database
    $sql = "INSERT INTO aa_erp_kt_teklif_dosyalar
            (TEKLIF_NO, ORIGINAL_NAME, FILE_NAME, FILE_PATH, FILE_SIZE, FILE_TYPE, UPLOAD_DATE, UPLOADED_BY)
            VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?)";

    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $teklif_no,
        $file['name'],
        $filename,
        $filename,
        $file['size'],
        $file['type'],
        wp_get_current_user()->user_login ?? 'system'
    ]);

    if (!$result) {
        // If database insert fails, remove uploaded file
        unlink($file_path);
        throw new Exception('Veritabanı kayıt hatası');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Dosya başarıyla yüklendi',
        'file_id' => $conn->lastInsertId(),
        'filename' => $filename
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
        'files_received' => isset($_FILES) ? array_keys($_FILES) : 'none',
        'post_received' => isset($_POST) ? array_keys($_POST) : 'none',
        'wp_load_path' => $wp_load_path ?? 'not set',
        'conn_path' => $conn_path ?? 'not set'
    ];

    error_log('Upload Error Details: ' . json_encode($error_details));

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

    error_log('Upload Fatal Error Details: ' . json_encode($fatal_details));

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

    error_log('Upload Throwable Error: ' . json_encode($throwable_details));

    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'debug' => $throwable_details
    ]);
}
?>
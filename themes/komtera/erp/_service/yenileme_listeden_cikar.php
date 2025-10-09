<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// WordPress integration
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'wp-load.php bulunamadı']);
    exit;
}

// Database connection
include __DIR__ . '/../_conn.php';
require_once __DIR__ . '/../../inc/table_helper.php';

// Check user authentication
$current_user_id = get_current_user_id();
if (!$current_user_id) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => __('Giriş yapmış kullanıcı bulunamadı.','komtera')]);
    exit;
}

// Get current user login
$current_user = wp_get_current_user();
$kim = $current_user->user_login;

// Get parameters
$teklif_no = isset($_GET['teklif_no']) ? $_GET['teklif_no'] : '';

if (empty($teklif_no)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => __('Teklif numarası eksik.','komtera')]);
    exit;
}

try {
    // Get table name
    $tableName = getTableName('aa_erp_kt_teklifler');

    // Update query
    $sql = "UPDATE {$tableName} SET yenileme_log = :kim WHERE TEKLIF_NO = :teklif_no";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute(['kim' => $kim, 'teklif_no' => $teklif_no])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => __('Başarıyla listeden çıkarıldı.','komtera'),
            'user' => $kim,
            'teklif_no' => $teklif_no
        ]);
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => __('Güncelleme başarısız.','komtera'),
            'error' => $stmt->errorInfo()
        ]);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => __('Veritabanı hatası.','komtera'),
        'error' => $e->getMessage()
    ]);
}
?>

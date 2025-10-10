<?php
/**
 * Teklif Unlock Service
 * Teklifin kilidini açar (KILIT = 0)
 */

// Error reporting
error_reporting(E_ALL);
ini_set("display_errors", false); // Production için false
ini_set("log_errors", true);

// Include database connection
require_once dirname(__DIR__) . '/_conn.php';
require_once dirname(__DIR__, 2) . '/inc/table_helper.php';

// Set JSON header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Get teklif_no from POST
$teklif_no = $_POST['teklif_no'] ?? '';

// Validate input
if (empty($teklif_no)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Teklif numarası belirtilmemiş.'
    ]);
    exit;
}

try {
    // Check if teklif exists
    $check_sql = "SELECT TOP 1 TEKLIF_NO, KILIT FROM " . getTableName('aa_erp_kt_teklifler') . "
                  WHERE TEKLIF_NO = :teklif_no AND (SIL IS NULL OR SIL <> '1')";

    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':teklif_no', $teklif_no);
    $check_stmt->execute();
    $teklif = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$teklif) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Teklif bulunamadı: ' . $teklif_no
        ]);
        exit;
    }

    // Check if already unlocked
    if ($teklif['KILIT'] == 0 || $teklif['KILIT'] === null) {
        echo json_encode([
            'success' => true,
            'message' => 'Teklif zaten açık durumda.',
            'already_unlocked' => true
        ]);
        exit;
    }

    // Unlock the teklif
    $update_sql = "UPDATE " . getTableName('aa_erp_kt_teklifler') . "
                   SET KILIT = 0, KILIT_TARIHI = NULL
                   WHERE TEKLIF_NO = :teklif_no";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':teklif_no', $teklif_no);
    $result = $update_stmt->execute();

    if ($result) {
        // Log the unlock action (optional - if you have a log table)
        try {
            $log_sql = "INSERT INTO " . getTableName('aa_erp_kt_log') . "
                        (tarih, modul, kullanici, yapilan_islem, detay, ip_adres)
                        VALUES (GETDATE(), :modul, :kullanici, :islem, :detay, :ip)";

            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->execute([
                'modul' => 'Teklifler',
                'kullanici' => $_SERVER['REMOTE_USER'] ?? 'system',
                'islem' => 'Teklif Kilidi Açıldı',
                'detay' => 'Teklif No: ' . $teklif_no,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
        } catch (Exception $log_error) {
            // Log error but don't fail the main operation
            error_log("Log error: " . $log_error->getMessage());
        }

        echo json_encode([
            'success' => true,
            'message' => 'Teklif kilidi başarıyla açıldı.',
            'teklif_no' => $teklif_no
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Teklif kilidi açılırken bir hata oluştu.'
        ]);
    }

} catch (PDOException $e) {
    error_log("Database error in teklif_unlock.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error in teklif_unlock.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
    ]);
}
?>

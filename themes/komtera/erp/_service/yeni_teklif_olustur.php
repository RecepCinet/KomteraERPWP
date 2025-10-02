<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'WordPress bulunamadı']);
    exit;
}

// Database connection
include dirname(__DIR__) . '/_conn.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['firsat_no']) || empty($_POST['firsat_no'])) {
        throw new Exception('Fırsat numarası belirtilmedi');
    }

    $firsat_no = $_POST['firsat_no'];

    // Fırsat bilgilerini getir
    $sql = "SELECT * FROM " . getTableName('aa_erp_kt_firsatlar') . " WHERE FIRSAT_NO = :firsat_no";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':firsat_no', $firsat_no);
    $stmt->execute();
    $firsat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firsat) {
        throw new Exception('Fırsat bulunamadı');
    }

    // Yeni teklif numarası oluştur (son numara + 11)
    $sql = "SELECT MAX(CAST(SUBSTRING(TEKLIF_NO, 2, LEN(TEKLIF_NO) - 1) AS INT)) as max_no
            FROM " . getTableName('aa_erp_kt_teklifler') . "
            WHERE TEKLIF_NO LIKE 'T%' AND ISNUMERIC(SUBSTRING(TEKLIF_NO, 2, LEN(TEKLIF_NO) - 1)) = 1";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_no = ($result['max_no'] ?? 1000000) + 11;
    $yeni_teklif_no = 'T' . $next_no;

    // Yeni teklif kaydı oluştur - Doğru kolon isimleriyle
    $insert_sql = "INSERT INTO " . getTableName('aa_erp_kt_teklifler') . " (
        TEKLIF_NO, X_FIRSAT_NO, YARATILIS_TARIHI, YARATILIS_SAATI, TEKLIF_TIPI
    ) VALUES (
        :teklif_no, :firsat_no, CAST(GETDATE() AS DATE), CAST(GETDATE() AS TIME), 1
    )";

    $stmt = $conn->prepare($insert_sql);
    $stmt->bindParam(':teklif_no', $yeni_teklif_no);
    $stmt->bindParam(':firsat_no', $firsat_no);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'teklif_no' => $yeni_teklif_no,
            'message' => 'Yeni teklif başarıyla oluşturuldu'
        ]);
    } else {
        throw new Exception('Teklif oluşturulurken hata oluştu');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'sql_error' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
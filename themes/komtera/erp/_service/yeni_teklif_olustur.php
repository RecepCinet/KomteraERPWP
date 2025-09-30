<?php
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

    // Yeni teklif numarası oluştur
    $sql = "SELECT MAX(CAST(SUBSTRING(TEKLIF_NO, 2, LEN(TEKLIF_NO) - 1) AS INT)) as max_no
            FROM " . getTableName('aa_erp_kt_teklifler') . "
            WHERE TEKLIF_NO LIKE 'T%' AND ISNUMERIC(SUBSTRING(TEKLIF_NO, 2, LEN(TEKLIF_NO) - 1)) = 1";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_no = ($result['max_no'] ?? 1000000) + 1;
    $yeni_teklif_no = 'T' . $next_no;

    // Yeni teklif kaydı oluştur
    $insert_sql = "INSERT INTO " . getTableName('aa_erp_kt_teklifler') . " (
        TEKLIF_NO, X_FIRSAT_NO, BAYI_ADI, BAYI_CHKODU, BAYI_YETKILI_ISIM,
        MUSTERI_ADI, MUSTERI_YETKILI_ISIM, MARKA, PROJE_ADI,
        TEKLIF_TARIHI, GECERLILIK_TARIHI, DURUM, KAYIT_TARIHI, KAYIDI_ACAN,
        MUSTERI_TEMSILCISI, OLASILIK, GELIS_KANALI
    ) VALUES (
        :teklif_no, :firsat_no, :bayi_adi, :bayi_chkodu, :bayi_yetkili_isim,
        :musteri_adi, :musteri_yetkili_isim, :marka, :proje_adi,
        GETDATE(), DATEADD(day, 30, GETDATE()), 'Taslak', GETDATE(), :kayidi_acan,
        :musteri_temsilcisi, :olasilik, :gelis_kanali
    )";

    $stmt = $conn->prepare($insert_sql);
    $stmt->bindParam(':teklif_no', $yeni_teklif_no);
    $stmt->bindParam(':firsat_no', $firsat_no);
    $stmt->bindParam(':bayi_adi', $firsat['BAYI_ADI']);
    $stmt->bindParam(':bayi_chkodu', $firsat['BAYI_CHKODU']);
    $stmt->bindParam(':bayi_yetkili_isim', $firsat['BAYI_YETKILI_ISIM']);
    $stmt->bindParam(':musteri_adi', $firsat['MUSTERI_ADI']);
    $stmt->bindParam(':musteri_yetkili_isim', $firsat['MUSTERI_YETKILI_ISIM']);
    $stmt->bindParam(':marka', $firsat['MARKA']);
    $stmt->bindParam(':proje_adi', $firsat['PROJE_ADI']);
    $stmt->bindParam(':kayidi_acan', $firsat['KAYIDI_ACAN']);
    $stmt->bindParam(':musteri_temsilcisi', $firsat['MUSTERI_TEMSILCISI']);
    $stmt->bindParam(':olasilik', $firsat['OLASILIK']);
    $stmt->bindParam(':gelis_kanali', $firsat['GELIS_KANALI']);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'teklif_no' => $yeni_teklif_no,
            'message' => 'Yeni teklif başarıyla oluşturuldu'
        ]);
    } else {
        throw new Exception('Teklif oluşturulurken hata oluştu');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
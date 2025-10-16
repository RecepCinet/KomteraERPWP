<?php
// Bayi Detay - Modal için

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

$ch_kodu = isset($_GET['ch_kodu']) ? $_GET['ch_kodu'] : '';

if (empty($ch_kodu)) {
    echo json_encode(['success' => false, 'error' => 'CH_KODU gerekli']);
    exit;
}

try {
    // Ana bayi bilgileri
    $sql = "SELECT
        b.CH_UNVANI,
        b.CH_KODU,
        b.ADRES1,
        b.ADRES2,
        b.SEHIR,
        b.VADE,
        b.VERGI_DAIRESI,
        b.VERGI_NO,
        k.dikkat_listesi,
        k.kara_liste
    FROM aaa_erp_kt_bayiler b
    LEFT JOIN atest_aa_erp_kt_bayiler_kara_liste k ON b.CH_KODU = k.ch_kodu
    WHERE b.CH_KODU = :ch_kodu";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['ch_kodu' => $ch_kodu]);
    $bayi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bayi) {
        echo json_encode(['success' => false, 'error' => 'Bayi bulunamadı']);
        exit;
    }

    $response = [
        'success' => true,
        'bayi' => $bayi
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

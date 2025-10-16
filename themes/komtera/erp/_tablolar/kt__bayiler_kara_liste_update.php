<?php
// Kara Liste / Dikkat Listesi Güncelleme

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

header('Content-Type: application/json');

$ch_kodu = isset($_POST['ch_kodu']) ? trim($_POST['ch_kodu']) : '';
$field = isset($_POST['field']) ? trim($_POST['field']) : '';
$value = isset($_POST['value']) ? intval($_POST['value']) : 0;

// Validasyon
if (empty($ch_kodu)) {
    echo json_encode(['success' => false, 'error' => 'CH_KODU gerekli']);
    exit;
}

if (!in_array($field, ['dikkat_listesi', 'kara_liste'])) {
    echo json_encode(['success' => false, 'error' => 'Geçersiz alan']);
    exit;
}

try {
    // Önce kayıt var mı kontrol et
    $checkSql = "SELECT COUNT(*) as count FROM atest_aa_erp_kt_bayiler_kara_liste WHERE ch_kodu = :ch_kodu";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->execute(['ch_kodu' => $ch_kodu]);
    $exists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;

    if ($exists) {
        // Güncelle
        $updateSql = "UPDATE atest_aa_erp_kt_bayiler_kara_liste
                      SET {$field} = :value
                      WHERE ch_kodu = :ch_kodu";
        $stmt = $conn->prepare($updateSql);
        $stmt->execute([
            'value' => $value,
            'ch_kodu' => $ch_kodu
        ]);
    } else {
        // Yeni kayıt ekle
        $insertSql = "INSERT INTO atest_aa_erp_kt_bayiler_kara_liste (ch_kodu, {$field})
                      VALUES (:ch_kodu, :value)";
        $stmt = $conn->prepare($insertSql);
        $stmt->execute([
            'ch_kodu' => $ch_kodu,
            'value' => $value
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Güncelleme başarılı']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>

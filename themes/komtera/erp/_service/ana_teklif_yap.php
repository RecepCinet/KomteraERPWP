<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../_conn.php';

header('Content-Type: application/json');

try {
    // Teklif numarasını al ve validate et
    $teklif_no = $_GET['teklif_no'] ?? '';

    if (empty($teklif_no)) {
        echo json_encode(['success' => false, 'error' => 'Teklif numarası belirtilmemiş']);
        exit;
    }

    // Önce bu teklifin hangi fırsata ait olduğunu bul
    $firsat_sql = "SELECT X_FIRSAT_NO FROM aa_erp_kt_teklifler WHERE TEKLIF_NO = :teklif_no";
    $firsat_stmt = $conn->prepare($firsat_sql);
    $firsat_stmt->bindParam(':teklif_no', $teklif_no);
    $firsat_stmt->execute();
    $firsat_result = $firsat_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$firsat_result) {
        echo json_encode(['success' => false, 'error' => 'Teklif bulunamadı']);
        exit;
    }

    $firsat_no = $firsat_result['X_FIRSAT_NO'];

    // Transaction başlat
    $conn->beginTransaction();

    try {
        // 1. Aynı fırsattaki tüm ana teklifleri sıfırla
        $reset_sql = "UPDATE aa_erp_kt_teklifler
                      SET TEKLIF_TIPI = 0
                      WHERE X_FIRSAT_NO = :firsat_no AND TEKLIF_TIPI = 1";
        $reset_stmt = $conn->prepare($reset_sql);
        $reset_stmt->bindParam(':firsat_no', $firsat_no);
        $reset_stmt->execute();

        // 2. Seçilen teklifi ana teklif yap
        $update_sql = "UPDATE aa_erp_kt_teklifler
                       SET TEKLIF_TIPI = 1
                       WHERE TEKLIF_NO = :teklif_no";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':teklif_no', $teklif_no);
        $update_stmt->execute();

        // Transaction commit
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => $teklif_no . ' numaralı teklif ana teklif olarak ayarlandı',
            'firsat_no' => $firsat_no
        ]);

    } catch (Exception $e) {
        // Transaction rollback
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
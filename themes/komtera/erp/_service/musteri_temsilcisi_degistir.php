<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';
    $bayiChKodu = isset($_POST['bayi_ch_kodu']) ? trim($_POST['bayi_ch_kodu']) : '';
    $eskiTemsilci = isset($_POST['eski_temsilci']) ? trim($_POST['eski_temsilci']) : '';
    $yeniTemsilci = isset($_POST['yeni_temsilci']) ? trim($_POST['yeni_temsilci']) : '';

    // Validasyon
    if (empty($marka)) {
        echo json_encode(['success' => false, 'message' => 'Marka zorunludur']);
        exit;
    }

    if (empty($eskiTemsilci)) {
        echo json_encode(['success' => false, 'message' => 'Eski müşteri temsilcisi zorunludur']);
        exit;
    }

    if (empty($yeniTemsilci)) {
        echo json_encode(['success' => false, 'message' => 'Yeni müşteri temsilcisi zorunludur']);
        exit;
    }

    if ($eskiTemsilci === $yeniTemsilci) {
        echo json_encode(['success' => false, 'message' => 'Eski ve yeni temsilci aynı olamaz']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_firsatlar');

    // SQL sorgusunu oluştur
    $sql = "UPDATE {$tableName} SET MUSTERI_TEMSILCISI = :yeni_temsilci WHERE MUSTERI_TEMSILCISI = :eski_temsilci AND MARKA = :marka";

    $params = [
        ':yeni_temsilci' => $yeniTemsilci,
        ':eski_temsilci' => $eskiTemsilci,
        ':marka' => $marka
    ];

    // Eğer bayi kodu belirtilmişse, onu da ekle
    if (!empty($bayiChKodu)) {
        $sql .= " AND CH_KODU = :bayi_ch_kodu";
        $params[':bayi_ch_kodu'] = $bayiChKodu;
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $affectedRows = $stmt->rowCount();

        if ($affectedRows > 0) {
            $message = "Müşteri temsilcisi başarıyla değiştirildi.";
            if (!empty($bayiChKodu)) {
                $message .= "\nMarka: {$marka}\nBayi: {$bayiChKodu}";
            } else {
                $message .= "\nMarka: {$marka} (Tüm bayiler)";
            }
            $message .= "\n{$eskiTemsilci} → {$yeniTemsilci}";

            echo json_encode([
                'success' => true,
                'message' => $message,
                'affected_rows' => $affectedRows
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Belirtilen kriterlere uygun kayıt bulunamadı'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Veritabanı hatası: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

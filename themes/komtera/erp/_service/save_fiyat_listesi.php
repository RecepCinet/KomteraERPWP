<?php
session_start();
include '../../_conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Sadece POST metodu kabul edilir']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['marka']) || !isset($data['records'])) {
    echo json_encode(['success' => false, 'message' => 'Marka ve kayıt bilgisi gerekli']);
    exit;
}

$marka = $data['marka'];
$records = $data['records'];

if (empty($records)) {
    echo json_encode(['success' => false, 'message' => 'Kayıt bulunamadı']);
    exit;
}

// Türkçe formatlanmış sayıları (1.234,56) float'a çevir
function parseturkish($value) {
    if (is_null($value) || $value === '') return 0;
    // String ise Türkçe formatı parse et: 1.234,56 -> 1234.56
    if (is_string($value)) {
        $value = str_replace('.', '', $value); // Binler ayracını kaldır
        $value = str_replace(',', '.', $value); // Virgülü noktaya çevir
    }
    return floatval($value);
}

try {
    $conn->beginTransaction();

    $insertedCount = 0;
    $updatedCount = 0;
    $deletedCount = 0;

    // Excel'den gelen tüm SKU'ları topla (boş olanları dahil etme)
    $excelSkus = array_map(function($record) {
        return trim($record['sku'] ?? $record['SKU'] ?? '');
    }, $records);
    $excelSkus = array_filter($excelSkus, function($sku) {
        return !empty($sku);
    }); // Boş olanları çıkar

    // Veritabanında bu marka için Excel'de olmayan SKU'ları sil
    if (!empty($excelSkus)) {
        $placeholders = str_repeat('?,', count($excelSkus) - 1) . '?';
        $deleteSql = "DELETE FROM aa_erp_kt_fiyat_listesi WHERE marka = ? AND sku NOT IN ($placeholders)";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteParams = array_merge([$marka], $excelSkus);
        $deleteStmt->execute($deleteParams);
        $deletedCount = $deleteStmt->rowCount();
    }

    foreach ($records as $record) {
        // SKU boş ise atla
        $sku = trim($record['sku'] ?? $record['SKU'] ?? '');
        if (empty($sku)) {
            continue;
        }

        // SKU ve marka ile kontrol et, var mı?
        $checkSql = "SELECT COUNT(*) FROM aa_erp_kt_fiyat_listesi WHERE sku = :sku AND marka = :marka";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([':sku' => $sku, ':marka' => $marka]);
        $exists = $checkStmt->fetchColumn() > 0;

        if ($exists) {
            // UPDATE
            $updateSql = "UPDATE aa_erp_kt_fiyat_listesi SET
                urunAciklama = :urunAciklama,
                tur = :tur,
                cozum = :cozum,
                lisansSuresi = :lisansSuresi,
                listeFiyati = :listeFiyati,
                listeFiyatiUpLift = :listeFiyatiUpLift,
                paraBirimi = :paraBirimi,
                wgCategory = :wgCategory,
                wgUpcCode = :wgUpcCode,
                a_iskonto4 = :a_iskonto4,
                a_iskonto3 = :a_iskonto3,
                a_iskonto2 = :a_iskonto2,
                a_iskonto1 = :a_iskonto1,
                s_iskonto4 = :s_iskonto4,
                s_iskonto3 = :s_iskonto3,
                s_iskonto2 = :s_iskonto2,
                s_iskonto1 = :s_iskonto1,
                a_iskonto4_r = :a_iskonto4_r,
                a_iskonto3_r = :a_iskonto3_r,
                a_iskonto2_r = :a_iskonto2_r,
                a_iskonto1_r = :a_iskonto1_r,
                s_iskonto4_r = :s_iskonto4_r,
                s_iskonto3_r = :s_iskonto3_r,
                s_iskonto2_r = :s_iskonto2_r,
                s_iskonto1_r = :s_iskonto1_r
                WHERE sku = :sku AND marka = :marka";

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([
                ':urunAciklama' => $record['urunAciklama'] ?? '',
                ':tur' => $record['tur'] ?? '',
                ':cozum' => $record['cozum'] ?? '',
                ':lisansSuresi' => $record['lisansSuresi'] ?? 0,
                ':listeFiyati' => parseturkish($record['listeFiyati'] ?? 0),
                ':listeFiyatiUpLift' => parseturkish($record['listeFiyatiUpLift'] ?? 0),
                ':paraBirimi' => $record['paraBirimi'] ?? '',
                ':wgCategory' => $record['wgCategory'] ?? '',
                ':wgUpcCode' => $record['wgUpcCode'] ?? '',
                ':a_iskonto4' => parseturkish($record['a_iskonto4'] ?? 0),
                ':a_iskonto3' => parseturkish($record['a_iskonto3'] ?? 0),
                ':a_iskonto2' => parseturkish($record['a_iskonto2'] ?? 0),
                ':a_iskonto1' => parseturkish($record['a_iskonto1'] ?? 0),
                ':s_iskonto4' => parseturkish($record['s_iskonto4'] ?? 0),
                ':s_iskonto3' => parseturkish($record['s_iskonto3'] ?? 0),
                ':s_iskonto2' => parseturkish($record['s_iskonto2'] ?? 0),
                ':s_iskonto1' => parseturkish($record['s_iskonto1'] ?? 0),
                ':a_iskonto4_r' => parseturkish($record['a_iskonto4_r'] ?? 0),
                ':a_iskonto3_r' => parseturkish($record['a_iskonto3_r'] ?? 0),
                ':a_iskonto2_r' => parseturkish($record['a_iskonto2_r'] ?? 0),
                ':a_iskonto1_r' => parseturkish($record['a_iskonto1_r'] ?? 0),
                ':s_iskonto4_r' => parseturkish($record['s_iskonto4_r'] ?? 0),
                ':s_iskonto3_r' => parseturkish($record['s_iskonto3_r'] ?? 0),
                ':s_iskonto2_r' => parseturkish($record['s_iskonto2_r'] ?? 0),
                ':s_iskonto1_r' => parseturkish($record['s_iskonto1_r'] ?? 0),
                ':sku' => $sku,
                ':marka' => $marka
            ]);
            $updatedCount++;
        } else {
            // INSERT
            $insertSql = "INSERT INTO aa_erp_kt_fiyat_listesi
                (sku, marka, urunAciklama, tur, cozum, lisansSuresi, listeFiyati, listeFiyatiUpLift, paraBirimi, wgCategory, wgUpcCode,
                 a_iskonto4, a_iskonto3, a_iskonto2, a_iskonto1,
                 s_iskonto4, s_iskonto3, s_iskonto2, s_iskonto1,
                 a_iskonto4_r, a_iskonto3_r, a_iskonto2_r, a_iskonto1_r,
                 s_iskonto4_r, s_iskonto3_r, s_iskonto2_r, s_iskonto1_r)
                VALUES
                (:sku, :marka, :urunAciklama, :tur, :cozum, :lisansSuresi, :listeFiyati, :listeFiyatiUpLift, :paraBirimi, :wgCategory, :wgUpcCode,
                 :a_iskonto4, :a_iskonto3, :a_iskonto2, :a_iskonto1,
                 :s_iskonto4, :s_iskonto3, :s_iskonto2, :s_iskonto1,
                 :a_iskonto4_r, :a_iskonto3_r, :a_iskonto2_r, :a_iskonto1_r,
                 :s_iskonto4_r, :s_iskonto3_r, :s_iskonto2_r, :s_iskonto1_r)";

            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->execute([
                ':sku' => $sku,
                ':marka' => $marka,
                ':urunAciklama' => $record['urunAciklama'] ?? '',
                ':tur' => $record['tur'] ?? '',
                ':cozum' => $record['cozum'] ?? '',
                ':lisansSuresi' => $record['lisansSuresi'] ?? 0,
                ':listeFiyati' => parseturkish($record['listeFiyati'] ?? 0),
                ':listeFiyatiUpLift' => parseturkish($record['listeFiyatiUpLift'] ?? 0),
                ':paraBirimi' => $record['paraBirimi'] ?? '',
                ':wgCategory' => $record['wgCategory'] ?? '',
                ':wgUpcCode' => $record['wgUpcCode'] ?? '',
                ':a_iskonto4' => parseturkish($record['a_iskonto4'] ?? 0),
                ':a_iskonto3' => parseturkish($record['a_iskonto3'] ?? 0),
                ':a_iskonto2' => parseturkish($record['a_iskonto2'] ?? 0),
                ':a_iskonto1' => parseturkish($record['a_iskonto1'] ?? 0),
                ':s_iskonto4' => parseturkish($record['s_iskonto4'] ?? 0),
                ':s_iskonto3' => parseturkish($record['s_iskonto3'] ?? 0),
                ':s_iskonto2' => parseturkish($record['s_iskonto2'] ?? 0),
                ':s_iskonto1' => parseturkish($record['s_iskonto1'] ?? 0),
                ':a_iskonto4_r' => parseturkish($record['a_iskonto4_r'] ?? 0),
                ':a_iskonto3_r' => parseturkish($record['a_iskonto3_r'] ?? 0),
                ':a_iskonto2_r' => parseturkish($record['a_iskonto2_r'] ?? 0),
                ':a_iskonto1_r' => parseturkish($record['a_iskonto1_r'] ?? 0),
                ':s_iskonto4_r' => parseturkish($record['s_iskonto4_r'] ?? 0),
                ':s_iskonto3_r' => parseturkish($record['s_iskonto3_r'] ?? 0),
                ':s_iskonto2_r' => parseturkish($record['s_iskonto2_r'] ?? 0),
                ':s_iskonto1_r' => parseturkish($record['s_iskonto1_r'] ?? 0)
            ]);
            $insertedCount++;
        }
    }

    $conn->commit();

    $message = [];
    if ($insertedCount > 0) $message[] = "$insertedCount yeni kayıt eklendi";
    if ($updatedCount > 0) $message[] = "$updatedCount kayıt güncellendi";
    if ($deletedCount > 0) $message[] = "$deletedCount kayıt silindi";

    echo json_encode([
        'success' => true,
        'message' => implode(', ', $message),
        'inserted' => $insertedCount,
        'updated' => $updatedCount,
        'deleted' => $deletedCount
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}

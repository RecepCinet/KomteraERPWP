<?php
session_start();
include '../../_conn.php';
require_once '../../inc/table_helper.php';

if (!isset($_GET['marka'])) {
    die('Marka parametresi gerekli');
}

$marka = $_GET['marka'];
$tableName = getTableName('aa_erp_kt_fiyat_listesi');

// ID hariç tüm kolonları çek - sayısal değerleri Türkçe formatla (virgüllü)
$sql = "SELECT
    sku,
    urunAciklama,
    marka,
    tur,
    cozum,
    lisansSuresi,
    paraBirimi,
    FORMAT(CAST(listeFiyati as float), 'N2', 'tr-TR') as listeFiyati,
    FORMAT(CAST(listeFiyatiUpLift as float), 'N2', 'tr-TR') as listeFiyatiUpLift,
    wgCategory,
    wgUpcCode,
    FORMAT(a_iskonto4, 'N2', 'tr-TR') as a_iskonto4,
    FORMAT(a_iskonto3, 'N2', 'tr-TR') as a_iskonto3,
    FORMAT(a_iskonto2, 'N2', 'tr-TR') as a_iskonto2,
    FORMAT(a_iskonto1, 'N2', 'tr-TR') as a_iskonto1,
    FORMAT(s_iskonto4, 'N2', 'tr-TR') as s_iskonto4,
    FORMAT(s_iskonto3, 'N2', 'tr-TR') as s_iskonto3,
    FORMAT(s_iskonto2, 'N2', 'tr-TR') as s_iskonto2,
    FORMAT(s_iskonto1, 'N2', 'tr-TR') as s_iskonto1,
    FORMAT(a_iskonto4_r, 'N2', 'tr-TR') as a_iskonto4_r,
    FORMAT(a_iskonto3_r, 'N2', 'tr-TR') as a_iskonto3_r,
    FORMAT(a_iskonto2_r, 'N2', 'tr-TR') as a_iskonto2_r,
    FORMAT(a_iskonto1_r, 'N2', 'tr-TR') as a_iskonto1_r,
    FORMAT(s_iskonto4_r, 'N2', 'tr-TR') as s_iskonto4_r,
    FORMAT(s_iskonto3_r, 'N2', 'tr-TR') as s_iskonto3_r,
    FORMAT(s_iskonto2_r, 'N2', 'tr-TR') as s_iskonto2_r,
    FORMAT(s_iskonto1_r, 'N2', 'tr-TR') as s_iskonto1_r
FROM {$tableName}
WHERE marka = :marka
ORDER BY sku";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([':marka' => $marka]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $data]);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
}

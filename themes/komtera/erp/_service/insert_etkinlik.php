<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';
    $baslik = isset($_POST['baslik']) ? trim($_POST['baslik']) : 'Yeni Etkinlik';
    $kodu = isset($_POST['kodu']) ? trim($_POST['kodu']) : '';
    $tarih_bas = isset($_POST['tarih_bas']) ? $_POST['tarih_bas'] : date('Y-m-d');
    $tarih_bit = isset($_POST['tarih_bit']) ? $_POST['tarih_bit'] : date('Y-m-d', strtotime('+30 days'));

    $tableName = getTableName('aa_erp_kt_etkinlikler');

    $sql = "INSERT INTO {$tableName} (marka, baslik, kodu, tarih_bas, tarih_bit)
            VALUES (:marka, :baslik, :kodu, :tarih_bas, :tarih_bit)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marka', $marka, PDO::PARAM_STR);
        $stmt->bindParam(':baslik', $baslik, PDO::PARAM_STR);
        $stmt->bindParam(':kodu', $kodu, PDO::PARAM_STR);
        $stmt->bindParam(':tarih_bas', $tarih_bas, PDO::PARAM_STR);
        $stmt->bindParam(':tarih_bit', $tarih_bit, PDO::PARAM_STR);
        $stmt->execute();

        $newId = $conn->lastInsertId();

        echo json_encode(['success' => true, 'message' => 'Yeni etkinlik oluşturuldu', 'id' => $newId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

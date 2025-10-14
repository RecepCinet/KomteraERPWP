<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $marka = isset($_POST['marka']) ? trim($_POST['marka']) : '';
    $baslik = isset($_POST['baslik']) ? trim($_POST['baslik']) : '';
    $kodu = isset($_POST['kodu']) ? trim($_POST['kodu']) : '';
    $tarih_bas = isset($_POST['tarih_bas']) ? $_POST['tarih_bas'] : '';
    $tarih_bit = isset($_POST['tarih_bit']) ? $_POST['tarih_bit'] : '';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
        exit;
    }

    if (empty($marka) || empty($baslik) || empty($kodu)) {
        echo json_encode(['success' => false, 'message' => 'Tüm alanlar zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_etkinlikler');

    $sql = "UPDATE {$tableName} SET
        marka = :marka,
        baslik = :baslik,
        kodu = :kodu,
        tarih_bas = :tarih_bas,
        tarih_bit = :tarih_bit
    WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':marka', $marka, PDO::PARAM_STR);
        $stmt->bindParam(':baslik', $baslik, PDO::PARAM_STR);
        $stmt->bindParam(':kodu', $kodu, PDO::PARAM_STR);
        $stmt->bindParam(':tarih_bas', $tarih_bas, PDO::PARAM_STR);
        $stmt->bindParam(':tarih_bit', $tarih_bit, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Etkinlik başarıyla güncellendi']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

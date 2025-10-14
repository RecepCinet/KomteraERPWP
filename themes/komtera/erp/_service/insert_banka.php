<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sira = isset($_POST['sira']) ? (int)$_POST['sira'] : 1;
    $banka = isset($_POST['banka']) ? trim($_POST['banka']) : '';
    $iban = isset($_POST['iban']) ? trim($_POST['iban']) : '';
    $kur = isset($_POST['kur']) ? trim($_POST['kur']) : 'TRY';

    $tableName = getTableName('aa_erp_kt_bankalar');

    $sql = "INSERT INTO {$tableName} (sira, banka, iban, kur) VALUES (:sira, :banka, :iban, :kur)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':sira', $sira, PDO::PARAM_INT);
        $stmt->bindParam(':banka', $banka, PDO::PARAM_STR);
        $stmt->bindParam(':iban', $iban, PDO::PARAM_STR);
        $stmt->bindParam(':kur', $kur, PDO::PARAM_STR);
        $stmt->execute();

        $newId = $conn->lastInsertId();

        echo json_encode(['success' => true, 'message' => 'Yeni banka kaydı oluşturuldu', 'id' => $newId]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

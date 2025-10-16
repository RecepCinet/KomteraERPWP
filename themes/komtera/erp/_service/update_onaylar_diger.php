<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $key_name = isset($_POST['key_name']) ? trim($_POST['key_name']) : '';
    $aciklama = isset($_POST['aciklama']) ? trim($_POST['aciklama']) : '';
    $kullanici = isset($_POST['kullanici']) ? trim($_POST['kullanici']) : '';

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
        exit;
    }

    if (empty($key_name)) {
        echo json_encode(['success' => false, 'message' => 'Key (Anahtar) zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_ayarlar_onaylar');

    $sql = "UPDATE {$tableName} SET
        kural = :key_name,
        aciklama = :aciklama,
        kim = :kullanici
    WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':key_name', $key_name, PDO::PARAM_STR);
        $stmt->bindParam(':aciklama', $aciklama, PDO::PARAM_STR);
        $stmt->bindParam(':kullanici', $kullanici, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Kayıt başarıyla güncellendi']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

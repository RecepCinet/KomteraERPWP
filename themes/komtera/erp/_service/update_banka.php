<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $kur = isset($_POST['kur']) ? trim($_POST['kur']) : '';
    $quick_update = isset($_POST['quick_update']) ? true : false;

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
        exit;
    }

    if (empty($kur)) {
        echo json_encode(['success' => false, 'message' => 'Para birimi zorunludur']);
        exit;
    }

    $tableName = getTableName('aa_erp_kt_bankalar');

    // Hızlı güncelleme (sadece para birimi)
    if ($quick_update) {
        $sql = "UPDATE {$tableName} SET kur = :kur WHERE id = :id";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':kur', $kur, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Para birimi başarıyla güncellendi']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    } else {
        // Tam güncelleme (tüm alanlar)
        $sira = isset($_POST['sira']) ? (int)$_POST['sira'] : 0;
        $banka = isset($_POST['banka']) ? trim($_POST['banka']) : '';
        $iban = isset($_POST['iban']) ? trim($_POST['iban']) : '';

        if ($sira <= 0 || empty($banka) || empty($iban)) {
            echo json_encode(['success' => false, 'message' => 'Tüm alanlar zorunludur']);
            exit;
        }

        $sql = "UPDATE {$tableName} SET
            sira = :sira,
            banka = :banka,
            iban = :iban,
            kur = :kur
        WHERE id = :id";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':sira', $sira, PDO::PARAM_INT);
            $stmt->bindParam(':banka', $banka, PDO::PARAM_STR);
            $stmt->bindParam(':iban', $iban, PDO::PARAM_STR);
            $stmt->bindParam(':kur', $kur, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Banka kaydı başarıyla güncellendi']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu']);
}
?>

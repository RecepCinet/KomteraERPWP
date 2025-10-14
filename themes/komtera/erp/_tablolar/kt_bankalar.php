<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

// Tek bir banka kaydı getir (ID parametresi varsa)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM " . getTableName('aa_erp_kt_bankalar') . " WHERE id = :id";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
} else {
    // Tüm banka kayıtlarını getir
    $sql = "SELECT * FROM " . getTableName('aa_erp_kt_bankalar') . " ORDER BY kur, sira";

    try {
        $stmt = $conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
        exit;
    }
}

$response = [
    'data' => $data
];

if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . json_encode($response) . ')';
} else {
    echo json_encode($response);
}
?>

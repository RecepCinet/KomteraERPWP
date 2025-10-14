<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$sql = "SELECT id, marka, q1, q2, q3, q4
        FROM " . getTableName('aa_erp_kt_mt_hedefler') . "
        ORDER BY marka";

try {
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
    exit;
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

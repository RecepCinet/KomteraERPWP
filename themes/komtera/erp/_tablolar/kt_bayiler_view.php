<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$sql = "SELECT
            CH_KODU,
            CH_UNVANI
        FROM " . getTableName('aaa_erp_kt_bayiler') . "
        WHERE CH_KODU IS NOT NULL
        ORDER BY CH_UNVANI";

try {
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'success' => true,
        'data' => $data
    ];
} catch (PDOException $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ];
}

header('Content-Type: application/json');

if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . json_encode($response) . ')';
} else {
    echo json_encode($response);
}
?>

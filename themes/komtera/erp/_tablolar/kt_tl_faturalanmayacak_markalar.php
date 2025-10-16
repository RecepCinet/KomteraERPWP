<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$sql = "SELECT
            id,
            marka
        FROM " . getTableName('aa_erp_kt_tl_fatura_marka') . "
        ORDER BY marka";

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

<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$sql = "SELECT
    bs.MARKA,
    bs.CH_KODU,
    b.CH_UNVANI,
    bs.seviye,
    CASE
        WHEN bs.seviye = '1' THEN 'AUTHORIZED'
        WHEN bs.seviye = '2' THEN 'SILVER'
        WHEN bs.seviye = '3' THEN 'GOLD'
        WHEN bs.seviye = '4' THEN 'PLATINUM'
        ELSE ''
    END AS SMETIN
FROM " . getTableName('aa_erp_kt_bayiler_markaseviyeleri') . " bs
LEFT JOIN " . getTableName('aaa_erp_kt_bayiler') . " b ON b.CH_KODU = bs.CH_KODU
ORDER BY bs.MARKA, b.CH_UNVANI";

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

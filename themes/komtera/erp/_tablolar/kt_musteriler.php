<?PHP

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';
require_once '../../inc/table_helper.php';

$tableName = getTableName('aa_erp_kt_musteriler');

// Local mode - tüm veriyi çek
$sql = "SELECT
    id,
    musteri,
    adres,
    sehir,
    posta_kodu
FROM $tableName
WHERE musteri IS NOT NULL AND LEN(musteri) > 1
ORDER BY musteri";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Local mode response
$response = ['data' => $data];

header('Content-Type: application/json');

if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . json_encode($response) . ')';
} else {
    echo json_encode($response);
}
?>

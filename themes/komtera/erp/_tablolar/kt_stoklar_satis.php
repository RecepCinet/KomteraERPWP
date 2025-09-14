<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../../_conn.php';

$sql = "Select (select count(SERIAL_NO) from aaa_erp_kt_serial_no sn where sn.SKU=ss.SKU) as SN,* from aaa_erp_kt_stoklar_satis ss order by id DESC ";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

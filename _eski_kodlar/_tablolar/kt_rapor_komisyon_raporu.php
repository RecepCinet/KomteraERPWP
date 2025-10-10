<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);
include '../_conn.php';
$sql = "select * from aaaa_erp_kt_komisyon_raporu";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>
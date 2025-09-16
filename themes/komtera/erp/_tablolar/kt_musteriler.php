<?PHP

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

$sql = "Select * from aa_erp_kt_musteriler";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

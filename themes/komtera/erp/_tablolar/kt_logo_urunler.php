<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../../_conn.php';

$siparis_no=$_GET['siparis_no'];

$sql = "Select * from ARYD_FIS_AKTARIM WHERE [NO]='$siparis_no'";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

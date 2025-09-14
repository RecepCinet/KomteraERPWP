<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../../_conn.php';

$sql = "select *,
CASE
WHEN e.tarih_bit < GETDATE() THEN 'Bitti'
ELSE 'Devam'
END AS BITTI
from aa_erp_kt_etkinlikler e";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

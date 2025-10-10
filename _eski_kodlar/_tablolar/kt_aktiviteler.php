<?PHP

session_start();
$user=($_SESSION['user']);

error_reporting(E_ALL);
ini_set('display_erros', true);
include '../_conn.php';
$date1= $_GET['date1'];
$date2= $_GET['date2'];
$cryp= $_GET['cryp'];
session_start();
$user=$_SESSION['user'];
$bu=$user['bu'];

$dates="TARIH>='$date1' AND TARIH<='$date2'";
$sql = "select 
CASE
WHEN BAYI_MEVCUT is not null THEN BAYI_MEVCUT 
WHEN BAYI_YENI is not null THEN BAYI_YENI 
END AS BAYI,
*
from aa_erp_kt_aktiviteler
where  ($dates)";  // bu like '$bu%' AND    bu iptal edildi!
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);
include '../_conn.php';
$sql = "select bs.MARKA,bs.CH_KODU,b.CH_UNVANI,bs.seviye,CASE
WHEN bs.seviye = '1' THEN 'AUTHORIZED'
WHEN bs.seviye = '2' THEN 'SILVER'
WHEN bs.seviye = '3' THEN 'GOLD'
WHEN bs.seviye = '4' THEN 'PLATINUM'
ELSE ''
END AS SMETIN
from aa_erp_kt_bayiler_markaseviyeleri bs LEFT JOIN aaa_erp_kt_bayiler b ON b.CH_KODU = bs.CH_KODU
";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

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
$dates="p.CD>='$date1' AND p.CD<='$date2'";
$sql = "select p.id,p.FIRSAT_NO,p.TUR,
CASE
WHEN f.DURUM=0 THEN 'Açık'
WHEN f.DURUM=-1 THEN 'Kaybedildi'
WHEN f.DURUM=0 THEN 'Kazanıldı'
END AS DURUM,
p.CD,p.CT,f.MARKA,f.BAYI_ADI,f.MUSTERI_ADI,
(select sum(SURE) from aa_erp_kt_poc_emek pe where x_poc_id=p.id and NEREDE='Yerinde') as Yerinde,
(select sum(SURE) from aa_erp_kt_poc_emek pe where x_poc_id=p.id and NEREDE='Uzaktan') as Uzaktan,
(select sum(SURE) from aa_erp_kt_poc_emek pe where x_poc_id=p.id) as Toplam
from aa_erp_kt_poc p LEFT JOIN aa_erp_kt_firsatlar f ON p.FIRSAT_NO = f.FIRSAT_NO
WHERE ($dates)"; // where  ($dates)";  // bu like '$bu%' AND    bu iptal edildi!
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

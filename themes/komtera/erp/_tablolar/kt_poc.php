<?PHP

session_start();
$user=($_SESSION['user']);

error_reporting(E_ALL);
ini_set('display_erros', true);
include '../../_conn.php';


$sql = "select p.id,p.FIRSAT_NO,p.TUR,
CASE
WHEN f.DURUM=0 THEN 'Açık'
WHEN f.DURUM=-1 THEN 'Kaybedildi'
WHEN f.DURUM=0 THEN 'Kazanıldı'
END AS DURUM,
p.CD,p.CT,f.MARKA,f.BAYI_ADI,f.MUSTERI_ADI,
(select sum(SURE) from " . getTableName('aa_erp_kt_poc_emek') . " pe where x_poc_id=p.id and NEREDE='Yerinde') as Yerinde,
(select sum(SURE) from " . getTableName('aa_erp_kt_poc_emek') . " pe where x_poc_id=p.id and NEREDE='Uzaktan') as Uzaktan,
(select sum(SURE) from " . getTableName('aa_erp_kt_poc_emek') . " pe where x_poc_id=p.id) as Toplam
from " . getTableName('aa_erp_kt_poc') . " p LEFT JOIN " . getTableName('aa_erp_kt_firsatlar') . " f ON p.FIRSAT_NO = f.FIRSAT_NO
"; // where  ($dates)";  // bu like '$bu%' AND    bu iptal edildi!
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

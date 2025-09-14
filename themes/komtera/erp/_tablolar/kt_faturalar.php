<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../../_conn.php';

$sql = "select id,_teklif_no,siparisNo,b.CH_UNVANI as bbayi,unvan,[_faturami],[_status_i] ,[_status_f],r_FisNo,
r_LogoId ,r_result ,r_response,f.irsaliyeTarihi , f.faturaTarihi ,projeKodu,dovizTuru ,dovizKuru ,ambarKodu
from aa_erp_kt_fatura_i f
left join aaa_erp_kt_bayiler b ON f.cariKod=b.CH_KODU
order by id desc
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
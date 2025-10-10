<?PHP
// Deneme!

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../_conn.php';

$sql = "select b.CH_UNVANI,
b.CH_KODU,
b.VADE,
k.dikkat_listesi,
k.kara_liste,
(select top 1 seviye from aa_erp_kt_bayiler_markaseviyeleri s where s.MARKA='SOPHOS' AND s.CH_KODU=b.CH_KODU) AS SOPHOS,
(select top 1 seviye from aa_erp_kt_bayiler_markaseviyeleri s where s.MARKA='WATCHGUARD' AND s.CH_KODU=b.CH_KODU) AS WATCHGUARD
from aaa_erp_kt_bayiler b LEFT JOIN aa_erp_kt_bayiler_kara_liste k
ON b.CH_KODU =k.ch_kodu
WHERE b.CH_KODU like '120%'
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

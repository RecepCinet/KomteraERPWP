<?PHP

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

$sqlf="select CONCAT('''',REPLACE(lisans_tarih_markalar, CHAR(13), '',''),'''') as D from " . getTableName('aa_erp_kt_pref') . "";
//

$stmt = $conn->query($sqlf);
$filt = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['D'];


$sql="SELECT f.id,
f.FIRSAT_NO,
t.TEKLIF_NO,
f.MUSTERI_TEMSILCISI,
f.MARKA,
f.BAYI_ADI,
f.BAYI_YETKILI_ISIM,
f.MUSTERI_ADI
FROM " . getTableName('aa_erp_kt_teklifler') . " t LEFT OUTER JOIN LKS.dbo." . getTableName('aa_erp_kt_firsatlar') . " f
ON t.X_FIRSAT_NO = f.FIRSAT_NO
WHERE f.SIL='0'
AND t.TEKLIF_TIPI = '1'
AND f.FIRSAT_NO NOT IN (select FIRSAT_NO from " . getTableName('aa_erp_kt_firsatlar') . " f where f.FIRSAT_ANA is null AND f.BAGLI_FIRSAT_NO is not NULL)
AND t.TEKLIF_NO IN (SELECT
  LEFT(su.X_SIPARIS_NO, CHARINDEX('-', su.X_SIPARIS_NO) - 1) AS SiparisNo
FROM
  " . getTableName('aa_erp_kt_siparisler_urunler') . " su
WHERE
  su.YENILEMETARIHI is null
  AND su.id > 32450
GROUP BY
  LEFT(su.X_SIPARIS_NO, CHARINDEX('-', su.X_SIPARIS_NO) - 1)
)";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>
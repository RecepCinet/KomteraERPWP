<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);
session_start();
$siparis_no=$_GET['siparis_no'];
include '../../_conn.php';
$sql = "select
su.id,
su.X_SIPARIS_NO,
su.SIRA,
su.SKU,
su.ACIKLAMA,
su.ADET,
su.BIRIM_FIYAT,
su.TIP,
su.SURE,
(su.ADET*su.BIRIM_FIYAT) AS TOPLAM,
su.LISANS,
ss.GERCEK_STOK,
(select top 1 SONUC from " . getTableName('ARYD_FIS_AKTARIM') . " WHERE [NO]=su.X_SIPARIS_NO) AS LSONUC,
(select top 1 MESAJ from " . getTableName('ARYD_FIS_AKTARIM') . " WHERE [NO]=su.X_SIPARIS_NO) AS LMESAJ,
SEC,
SEC_ADET
from " . getTableName('aa_erp_kt_siparisler_urunler') . " su
LEFT JOIN " . getTableName('aaa_erp_kt_stoklar_satis') . " ss ON su.SKU = ss.SKU
where X_SIPARIS_NO='$siparis_no'";  
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

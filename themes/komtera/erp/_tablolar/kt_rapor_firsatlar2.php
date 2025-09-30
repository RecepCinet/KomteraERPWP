<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../../_conn.php';

$sql = "SELECT f.id,
f.MARKA_MANAGER,
f.YETKILI_ISIM,
f.FIRSAT_NO,
stuff((
select ','+convert(varchar(10),tu.SKU)
from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu LEFT JOIN " . getTableName('aa_erp_kt_teklifler') . " t ON tu.X_TEKLIF_NO = t.TEKLIF_NO where t.TEKLIF_TIPI = 1 AND f.FIRSAT_NO = t.X_FIRSAT_NO
for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as skular,
stuff((
        select ','+convert(varchar(10),TEKLIF_NO)
        from " . getTableName('aa_erp_kt_teklifler') . " where X_FIRSAT_NO=f.FIRSAT_NO order by TEKLIF_TIPI desc
        for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as Teklifler,
stuff((
        select ','+convert(varchar(10),SIPARIS_NO)
	from " . getTableName('aa_erp_kt_siparisler') . " where X_FIRSAT_NO=f.FIRSAT_NO
	for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as Siparisler,
(select SUM(tu.ADET*tu.B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu INNER JOIN " . getTableName('aa_erp_kt_teklifler') . " te ON tu.X_TEKLIF_NO = te.TEKLIF_NO where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) as TUTAR,
f.PARA_BIRIMI,
CASE
WHEN PARA_BIRIMI = 'USD' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu INNER JOIN " . getTableName('aa_erp_kt_teklifler') . " te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)
WHEN PARA_BIRIMI = 'TRY' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu INNER JOIN " . getTableName('aa_erp_kt_teklifler') . " te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)/CONVERT(money," . $_SESSION['USD'] . ")
WHEN PARA_BIRIMI = 'EUR' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu INNER JOIN " . getTableName('aa_erp_kt_teklifler') . " te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)*CONVERT(money," . $_SESSION['EUR'] . "/" . $_SESSION['USD'] . ")
ELSE 0
END AS DLR_TUTAR,
f.REGISTER,
f.BASLANGIC_TARIHI,
f.BITIS_TARIHI,
f.REVIZE_TARIHI,
CASE
WHEN DURUM = '-1' THEN 'Kaybedildi'
WHEN DURUM = '1' THEN 'Kazanıldı'
WHEN DURUM = '0' THEN 'Açık'
ELSE ''
END AS DURUM,
f.KAYIDI_ACAN,
f.MUSTERI_TEMSILCISI,
f.MARKA,
f.ETKINLIK,
f.BAYI_ADI,
f.BAYI_YETKILI_ISIM,
f.MUSTERI_ADI,
f.OLASILIK,
f.KAYBEDILME_NEDENI,
f.KAYBEDILME_NEDENI_DIGER,
(select top 1 NOTLAR from " . getTableName('aa_erp_kt_teklifler') . " where X_FIRSAT_NO=f.FIRSAT_NO and TEKLIF_TIPI=1) as TNOTLAR
FROM LKS.dbo." . getTableName('aa_erp_kt_firsatlar') . " f WHERE SIL='0' AND f.MUSTERI_TEMSILCISI='" . $_SESSION['user']['kullanici'] . "'
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


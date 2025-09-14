<?PHP
session_start();

include '../../_conn.php';
$date1= $_GET['date1'];
$date2= $_GET['date2'];
$sql = <<<DATA
SELECT f.id,
           (SELECT
               top 1 CASE
                   WHEN t.SATIS_TIPI = '0' THEN 'İlk Satış'
                   WHEN t.SATIS_TIPI = '1' THEN 'Yenileme'
                   ELSE 'İlk Satış ve Yenileme'
                   END
           FROM aa_erp_kt_teklifler t
           WHERE t.TEKLIF_TIPI = 1 AND t.X_FIRSAT_NO = f.FIRSAT_NO
       ) AS SATIP,
f.MARKA_MANAGER,
f.BAYI_YETKILI_ISIM,
f.FIRSAT_NO,
stuff((
select ','+convert(varchar(10),tu.SKU)
from aa_erp_kt_teklifler_urunler tu LEFT JOIN aa_erp_kt_teklifler t ON tu.X_TEKLIF_NO = t.TEKLIF_NO where t.TEKLIF_TIPI = 1 AND f.FIRSAT_NO = t.X_FIRSAT_NO 
for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as skular,
    stuff((
    select ','+fl.cozum
    from aa_erp_kt_teklifler_urunler tu 
    LEFT JOIN aa_erp_kt_teklifler t ON tu.X_TEKLIF_NO = t.TEKLIF_NO 
    LEFT JOIN aa_erp_kt_fiyat_listesi fl ON tu.SKU = fl.SKU
    where t.TEKLIF_TIPI = 1 AND f.FIRSAT_NO = t.X_FIRSAT_NO 
    for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as Cozumler,

stuff((
        select ' '+convert(varchar(10),TEKLIF_NO)
        from aa_erp_kt_teklifler where X_FIRSAT_NO=f.FIRSAT_NO order by TEKLIF_TIPI desc
        for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as Teklifler,
(select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te ON tu.X_TEKLIF_NO = te.TEKLIF_NO where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) as TUTAR,
f.PARA_BIRIMI,
CASE
WHEN PARA_BIRIMI = 'USD' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)
WHEN PARA_BIRIMI = 'TRY' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)/(select top 1 USD from aa_erp_kur k order by tarih desc)
WHEN PARA_BIRIMI = 'EUR' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)/(select top 1 USD/EUR from aa_erp_kur k order by tarih desc)
ELSE 0
END AS DLR_TUTAR,
f.REGISTER,
f.GELIS_KANALI,
f.BASLANGIC_TARIHI,
f.BITIS_TARIHI,
month(f.BITIS_TARIHI) as BITIS_AY,
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
f.PROJE_ADI,
f.FIRSAT_ACIKLAMA,
(select top 1 NOTLAR from aa_erp_kt_teklifler where X_FIRSAT_NO=f.FIRSAT_NO and TEKLIF_TIPI=1) as TNOTLAR
FROM LKS.dbo.aa_erp_kt_firsatlar f WHERE DURUM=0 AND SIL='0'
AND f.FIRSAT_NO NOT IN (select FIRSAT_NO from aa_erp_kt_firsatlar f where f.FIRSAT_ANA is null AND f.BAGLI_FIRSAT_NO is not NULL)
DATA;

if (!empty($date1) && !empty($date2)) {
    $sql .= " AND f.BASLANGIC_TARIHI >= '$date1' AND f.BASLANGIC_TARIHI <= '$date2'";
}

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

<?PHP
//Deneme!!! 8
error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

include '../../_conn.php';

$date1= $_GET['date1'];
$date2= $_GET['date2'];

// where SIL<>'1' AND $dates ORDER BY BASLANGIC_TARIHI
$sql = "select
    s.id,
        (SELECT
               top 1 CASE
                   WHEN t.SATIS_TIPI = '0' THEN 'İlk Satış'
                   WHEN t.SATIS_TIPI = '1' THEN 'Yenileme'
                   ELSE 'İlk Satış ve Yenileme'
                   END
           FROM aa_erp_kt_teklifler t
           WHERE t.TEKLIF_TIPI = 1 AND t.X_FIRSAT_NO = f.FIRSAT_NO
       ) AS SATIP,
    inv.DATE_ AS FATTARIHI,
    stuff((
select ','+convert(varchar(30),su.SKU)
from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO = s.SIPARIS_NO
for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as skular,
        stuff((
              select ','+convert(varchar(20),su.LISANS)
              from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO = s.SIPARIS_NO
              for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as lisanslar,
	s.SIPARIS_NO, 
	s.SIPARIS_DURUM,
(CASE
WHEN s.SIPARIS_DURUM = '-1' THEN 'Pasif'
WHEN s.SIPARIS_DURUM = '0' THEN 'Açık'
WHEN s.SIPARIS_DURUM = '1' THEN 'Aktif'
WHEN s.SIPARIS_DURUM = '2' THEN 'Kapalı'
ELSE ''
END) AS SIPARIS_DURUM_YAZI,
        s.SIPARIS_DURUM_ALT,
(CASE
WHEN s.SIPARIS_DURUM_ALT = '0' THEN null
WHEN s.SIPARIS_DURUM_ALT = '11' THEN 'Kara Listede'
WHEN s.SIPARIS_DURUM_ALT = '42' THEN 'Dikkat Listesinde'
WHEN s.SIPARIS_DURUM_ALT = '21' THEN 'Ürün Bekleniyor'
WHEN s.SIPARIS_DURUM_ALT = '31' THEN 'Risk Limiti'
WHEN s.SIPARIS_DURUM_ALT = '22' THEN 'İrsaliye İşleminde'
WHEN s.SIPARIS_DURUM_ALT = '23' THEN 'Fatura İşleminde'
WHEN s.SIPARIS_DURUM_ALT = '24' THEN 'Kota Bekleniyor'
ELSE ''
END) AS SIPARIS_DURUM_ALT_YAZI,
X_TEKLIF_NO,
s.PARCA,
s.KARGO_GONDERI_NO,
s.KARGO_DURUM,
f.MUSTERI_TEMSILCISI,
(select VADE from aa_erp_kt_teklifler t where t.TEKLIF_NO=X_TEKLIF_NO) AS VADE,
s.CD,s.CT,
(select SUM(BIRIM_FIYAT*ADET) AS TOPLAM from aa_erp_kt_siparisler_urunler su WHERE X_SIPARIS_NO = s.SIPARIS_NO) as TOPLAM,
CASE
WHEN f.PARA_BIRIMI = 'USD' THEN (select SUM(su.ADET*su.BIRIM_FIYAT) from aa_erp_kt_siparisler_urunler su where s.SIPARIS_NO=su.X_SIPARIS_NO)
WHEN f.PARA_BIRIMI = 'TRY' THEN (select SUM(su.ADET*su.BIRIM_FIYAT) from aa_erp_kt_siparisler_urunler su where s.SIPARIS_NO=su.X_SIPARIS_NO)/(select top 1 USD from aa_erp_kur k order by tarih desc)
WHEN f.PARA_BIRIMI = 'EUR' THEN (select SUM(su.ADET*su.BIRIM_FIYAT) from aa_erp_kt_siparisler_urunler su where s.SIPARIS_NO=su.X_SIPARIS_NO)/(select top 1 USD/EUR from aa_erp_kur k order by tarih desc)
ELSE 0
END AS DLR_TUTAR,
f.MARKA,
f.BAYI_ADI,
f.MUSTERI_ADI,
f.PARA_BIRIMI,
(select count(*) as kaunt from aa_erp_kt_siparisler_urunler suuu where TIP='Hardware' and suuu.X_SIPARIS_NO = s.SIPARIS_NO) as HardwareVarmi,
s.OZEL_KUR
from aa_erp_kt_siparisler s LEFT JOIN aa_erp_kt_firsatlar f 
ON s.X_FIRSAT_NO = f.FIRSAT_NO
LEFT JOIN LG_319_01_INVOICE inv ON inv.DOCODE=s.SIPARIS_NO";

if (!empty($date1) && !empty($date2)) {
    $sql .= " WHERE s.CD >= '$date1' AND s.CD <= '$date2'";
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

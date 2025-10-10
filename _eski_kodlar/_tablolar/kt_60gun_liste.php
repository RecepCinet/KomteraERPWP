<?PHP
/*
Bayi Ch Kodu
Bayi Ch Ünvanı
Marka
Satış Temsilcisi
Sip. Tarih
Sipariş No
Lisans Başlangıç
Lisans Bitiş
Alış Rakamı
Satış Rakamı
SKU
Ürün Adı
Ürün Seri No
Miktar
Musteri
Musteri Yetkili
Bayi Yetkili
Marka Manager
Kontrat No/Kota No
*/

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../_conn.php';

$cryp=$_GET['cryp'];

$ekyetki="AND f.MUSTERI_TEMSILCISI='$cryp'";

if ($cryp=="gursel.tursun" || $cryp=="gokhan.ilgit" || $cryp=="recep.cinet") {
    $ekyetki='';
}

$sql=<<<DATA
SELECT DISTINCT f.FIRSAT_NO,t.TEKLIF_NO,f.BAYI_CHKODU,f.BAYI_ADI,f.MARKA,f.MUSTERI_TEMSILCISI,s.CD,s.SIPARIS_NO,su.YENILEMETARIHI,f.MUSTERI_ADI
FROM aa_erp_kt_siparisler_urunler su
         LEFT JOIN aa_erp_kt_siparisler s ON su.X_SIPARIS_NO = s.SIPARIS_NO
         LEFT JOIN aa_erp_kt_teklifler t ON s.X_TEKLIF_NO  = t.TEKLIF_NO
         LEFT JOIN aa_erp_kt_firsatlar f ON f.FIRSAT_NO = s.X_FIRSAT_NO
         LEFT JOIN aa_erp_kt_teklifler_urunler tu ON t.TEKLIF_NO = tu.X_TEKLIF_NO AND tu.SKU = su.SKU
WHERE su.YENILEMETARIHI is not null
  AND DATEDIFF(DAY, GETDATE(), su.YENILEMETARIHI) BETWEEN 0 AND 60
  AND t.yenileme_log is null
$ekyetki 
DATA;

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

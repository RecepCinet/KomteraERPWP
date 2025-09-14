<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);

// WordPress integration for user data
include '../../_conn.php';
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "wp-load.php bulunamadı.\n";
    echo "Başlangıç dizini: " . __DIR__ . "\n";
    exit;
}
$date1= $_GET['date1'];
$date2= $_GET['date2'];
$dates="  BASLANGIC_TARIHI>='$date1' AND BASLANGIC_TARIHI<='$date2'";
// where SIL<>'1' AND $dates ORDER BY BASLANGIC_TARIHI
$sql = "SELECT f.id,
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
(select top 1 TEKLIF_NO from aa_erp_kt_teklifler where X_FIRSAT_NO = f.FIRSAT_NO and TEKLIF_TIPI = '1' ) as TEKLIF_NO,
f.PARA_BIRIMI,
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
CASE
WHEN PARA_BIRIMI = 'USD' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)
WHEN PARA_BIRIMI = 'TRY' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)/(select top 1 USD from aa_erp_kur k order by tarih desc)
WHEN PARA_BIRIMI = 'EUR' THEN (select SUM(tu.ADET*tu.B_SATIS_FIYATI) from aa_erp_kt_teklifler_urunler tu INNER JOIN aa_erp_kt_teklifler te  ON tu.X_TEKLIF_NO = te.TEKLIF_NO  where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO)/(select top 1 USD/EUR from aa_erp_kur k order by tarih desc)
ELSE 0
END AS DLR_TUTAR,
f.KAYIDI_ACAN,
f.MUSTERI_TEMSILCISI,
f.MARKA,
f.BAYI_ADI,
f.BAYI_YETKILI_ISIM,
f.MUSTERI_ADI
FROM LKS.dbo.aa_erp_kt_firsatlar f WHERE f.DURUM='1' AND f.SIL='0' AND CHARINDEX(MARKA, '" . implode(',', get_user_meta(get_current_user_id(), 'my_brands', true) ?: []) . "')>0 and $dates
AND f.FIRSAT_NO NOT IN (select FIRSAT_NO from aa_erp_kt_firsatlar f where f.FIRSAT_ANA is null AND f.BAGLI_FIRSAT_NO is not NULL)
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

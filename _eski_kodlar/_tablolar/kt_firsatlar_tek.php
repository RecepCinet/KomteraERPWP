<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../_conn.php';
$date1= $_GET['date1'];
$date2= $_GET['date2'];
$dates="  f.BASLANGIC_TARIHI>='$date1' AND f.BASLANGIC_TARIHI<='$date2'";
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
f.PARA_BIRIMI,
f.REGISTER,
f.BASLANGIC_TARIHI,
f.BITIS_TARIHI,
f.REVIZE_TARIHI,
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
t.TEKLIF_NO,
f.MUSTERI_ADI
FROM aa_erp_kt_teklifler t LEFT OUTER JOIN LKS.dbo.aa_erp_kt_firsatlar f
ON t.X_FIRSAT_NO = f.FIRSAT_NO
WHERE f.DURUM='0' AND f.SIL='0'
AND t.TEKLIF_TIPI = '1' AND CHARINDEX(f.MARKA, '" . $_SESSION['user']['markalar'] . "')>0 and $dates
AND f.FIRSAT_NO NOT IN (select FIRSAT_NO from aa_erp_kt_firsatlar f where f.FIRSAT_ANA is null AND f.BAGLI_FIRSAT_NO is not NULL)
order by f.FIRSAT_NO,t.TEKLIF_NO
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

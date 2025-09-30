<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../../_conn.php';

$sql = "SELECT 
    f.id,
    f.FIRSAT_NO,
    f.PARA_BIRIMI,
    te.TEKLIF_NO,
	s.CD as Tarih,
    SUM(tu.ADET * B_MALIYET) as MALIYET,
    SUM(tu.ADET * tu.B_SATIS_FIYATI) as TUTAR,
    CASE
        WHEN f.PARA_BIRIMI = 'USD' THEN SUM(tu.ADET * B_MALIYET)
        WHEN f.PARA_BIRIMI = 'TRY' THEN SUM(tu.ADET * B_MALIYET) / k.USD
        WHEN f.PARA_BIRIMI = 'EUR' THEN SUM(tu.ADET * B_MALIYET) * (k.USD / k.EUR)
        ELSE 0
    END AS DLR_MALIYET,
    CASE
        WHEN f.PARA_BIRIMI = 'USD' THEN SUM(tu.ADET * tu.B_SATIS_FIYATI)
        WHEN f.PARA_BIRIMI = 'TRY' THEN SUM(tu.ADET * tu.B_SATIS_FIYATI) / k.USD
        WHEN f.PARA_BIRIMI = 'EUR' THEN SUM(tu.ADET * tu.B_SATIS_FIYATI) * (k.USD / k.EUR)
        ELSE 0
    END AS DLR_TUTAR,
    f.KAYIDI_ACAN,
    f.MUSTERI_TEMSILCISI,
    f.MARKA,
    f.BAYI_ADI,
    f.BAYI_YETKILI_ISIM,
    f.MUSTERI_ADI,
    (select TOP 1 KOMISYON_F1 from " . getTableName('aa_erp_kt_teklifler') . " te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) FON1,
	(select TOP 1 KOMISYON_F2 from " . getTableName('aa_erp_kt_teklifler') . " te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) FON2,
	(select TOP 1 KOMISYON_H from " . getTableName('aa_erp_kt_teklifler') . " te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) HARCAMA
FROM
    LKS.dbo." . getTableName('aa_erp_kt_firsatlar') . " f
INNER JOIN
    " . getTableName('aa_erp_kt_teklifler') . " te ON te.X_FIRSAT_NO = f.FIRSAT_NO
INNER JOIN
    " . getTableName('aa_erp_kt_siparisler') . " s ON s.X_FIRSAT_NO = f.FIRSAT_NO
INNER JOIN
    " . getTableName('aa_erp_kt_teklifler_urunler') . " tu ON tu.X_TEKLIF_NO = te.TEKLIF_NO
INNER JOIN
    (SELECT TOP 1 USD, EUR FROM " . getTableName('aa_erp_kur') . " ORDER BY tarih DESC) k ON 1=1
WHERE
    f.DURUM = '1'
    AND f.SIL = '0'
    AND te.PDF = 1
    AND te.TEKLIF_TIPI = 1
    AND f.FIRSAT_NO NOT IN (
        SELECT FIRSAT_NO
        FROM " . getTableName('aa_erp_kt_firsatlar') . "
        WHERE FIRSAT_ANA IS NULL
        AND BAGLI_FIRSAT_NO IS NOT NULL
    )
GROUP BY
    f.id, s.CD, f.FIRSAT_NO, te.TEKLIF_NO, f.PARA_BIRIMI, f.BASLANGIC_TARIHI, f.BITIS_TARIHI, f.REVIZE_TARIHI, k.USD, k.EUR, f.KAYIDI_ACAN, f.MUSTERI_TEMSILCISI, f.MARKA, f.BAYI_ADI, f.BAYI_YETKILI_ISIM, f.MUSTERI_ADI
order by Tarih

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
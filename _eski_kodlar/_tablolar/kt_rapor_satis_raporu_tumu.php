<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../_conn.php';

$norm='ERP_SATIS_ANALIZ_20XX';
$yil=$_GET['yil'];

if ($yil=="2024" || $yil=="2025" || $yil=="2026" || $yil=="2027" || $yil=="2028" || $yil=="2029" || $yil=="2030") {
    $norm='ERP_SATIS_ANALIZ_319_20XX';
}

$sql =<<<DATA
WITH RankedTekliflerUrunler AS (
    SELECT
        tu.*,
        ROW_NUMBER() OVER (PARTITION BY tu.X_TEKLIF_NO, tu.SKU ORDER BY tu.id) AS rn
    FROM
        aa_erp_kt_teklifler_urunler tu
)
SELECT
    tu.B_LISTE_FIYATI AS BLSFIY,
    CONCAT(RIGHT('0' + CAST(sa.AYRAKAM AS VARCHAR(2)), 2), '-', sa.Ay) AS AyAdi,
    sa.*,
    f.MUSTERI_ADI,
    f.BAYI_YETKILI_ISIM,
    f.MARKA_MANAGER,
    f.ETKINLIK,
    te.SATIS_TIPI,
    te.Kampanya,
    CASE
        WHEN te.SATIS_TIPI = '1' THEN 'Yenileme'
        WHEN tu.SATIS_TIPI = 1 THEN 'Yenileme'
        ELSE 'Ilk Satis'
        END AS SATIS_TIPI,
    te.TEKLIF_NO,
    (SELECT TOP 1 seviye FROM aa_erp_kt_bayiler_markaseviyeleri ms WHERE ms.CH_KODU = f.BAYI_CHKODU) AS BayiSeviye
FROM
    $norm sa
        LEFT JOIN aa_erp_kt_teklifler te ON sa.TEKLIFNO = te.TEKLIF_NO
        LEFT JOIN aa_erp_kt_firsatlar f ON f.FIRSAT_NO = te.X_FIRSAT_NO
        LEFT JOIN RankedTekliflerUrunler tu ON tu.X_TEKLIF_NO = sa.TEKLIFNO AND sa.SKU = tu.SKU AND tu.rn = 1
WHERE
    Yil = '$yil'
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
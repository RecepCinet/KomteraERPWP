<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

session_start();

$yil=$_GET['yil'];

if ($_SESSION['enter'] != 1) {
    die("! Once dash ten login olun!");
}

$sqlstring=<<<DATA
SELECT 
    f.id,
    f.FIRSAT_NO,
    f.PARA_BIRIMI,
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
    (select TOP 1 KOMISYON_F1 from aa_erp_kt_teklifler te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) FON1,
	(select TOP 1 KOMISYON_F2 from aa_erp_kt_teklifler te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) FON2,
	(select TOP 1 KOMISYON_H from aa_erp_kt_teklifler te where te.PDF=1 AND te.TEKLIF_TIPI=1 AND te.X_FIRSAT_NO=f.FIRSAT_NO) HARCAMA
FROM 
    LKS.dbo.aa_erp_kt_firsatlar f
INNER JOIN 
    aa_erp_kt_teklifler te ON te.X_FIRSAT_NO = f.FIRSAT_NO
INNER JOIN 
    aa_erp_kt_siparisler s ON s.X_FIRSAT_NO = f.FIRSAT_NO
INNER JOIN 
    aa_erp_kt_teklifler_urunler tu ON tu.X_TEKLIF_NO = te.TEKLIF_NO
INNER JOIN 
    (SELECT TOP 1 USD, EUR FROM aa_erp_kur ORDER BY tarih DESC) k ON 1=1
WHERE
    f.DURUM = '1'
    AND f.SIL = '0'
    AND te.PDF = 1
    AND te.TEKLIF_TIPI = 1
    AND f.FIRSAT_NO NOT IN (
        SELECT FIRSAT_NO
        FROM aa_erp_kt_firsatlar
        WHERE FIRSAT_ANA IS NULL
        AND BAGLI_FIRSAT_NO IS NOT NULL
        AND YEAR(cd) = $yil
    )
GROUP BY
    f.id, s.CD, f.FIRSAT_NO, f.PARA_BIRIMI, f.BASLANGIC_TARIHI, f.BITIS_TARIHI, f.REVIZE_TARIHI, k.USD, k.EUR, f.KAYIDI_ACAN, f.MUSTERI_TEMSILCISI, f.MARKA, f.BAYI_ADI, f.BAYI_YETKILI_ISIM, f.MUSTERI_ADI
order by Tarih
DATA;

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="fon_raporu.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$serverName = "172.16.85.76";
try {
    $options = array(
        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
        PDO::ATTR_TIMEOUT => 9000, // 9000 saniye
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    $conn = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "!!!Crm!!!", $options);
} catch (Exception $e) {
    die("MS SQL Bağlantı Sorunu: " . $e->getMessage());
}

$stmt = $conn->prepare($sqlstring);
if (!$stmt) {
    die("SQL sorgusu hazırlanamadı: " . print_r($conn->errorInfo(), true));
}

if (!$stmt->execute()) {
    die("SQL sorgusu çalıştırılamadı: " . print_r($stmt->errorInfo(), true));
}

$columnsPrinted = false;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if (!$columnsPrinted) {
        echo implode(",", array_keys($row)) . "\n";
        $columnsPrinted = true;
    }
    echo implode(",", array_map('htmlspecialchars', $row)) . "\n";
}

?>
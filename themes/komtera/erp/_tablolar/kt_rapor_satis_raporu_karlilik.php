<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);
$yil = $_GET['yil'];
include '../../_conn.php';

$tablo='ERP_SATIS_ANALIZ_20XX';
if ($yil>="2024") {
    $tablo='ERP_SATIS_ANALIZ_319_20XX';
}

$sql = "SELECT 
f.PARA_BIRIMI,
((te.t_maliyet - ISNULL(te.KOMISYON_H,0))) as TEKLIF_MALIYET,
((te.t_satis - (ISNULL(te.KOMISYON_F1,0) + ISNULL(te.KOMISYON_F2,0)))) AS TEKLIF_TUTAR,
(((te.t_satis - (ISNULL(te.KOMISYON_F1,0) + ISNULL(te.KOMISYON_F2,0)))-(te.t_maliyet - ISNULL(te.KOMISYON_H,0)))) as KAR,
te.X_FIRSAT_NO, s.Fatura_No, s.Yil, s.Siparis_No, s.Ch_Kodu, s.Satis_Temsilcisi, s.Marka, s.Ay, s.CEYREK, s.DONEM, s.Ch_Unvani,
sum(USD_Tutar) As USD_TUTAR,sum(FON_USD) as FON_USD, sum(Net_Usd_Tutar) as NET_USD_TUTAR,
sum(TL_Tutar) AS TL_TUTAR,sum(FON_TL) AS FON_TL, sum(Net_Tl_Tutar) as NET_TL_TUTAR
FROM LKS.dbo.$tablo s
LEFT JOIN " . getTableName('aa_erp_kt_siparisler') . " si on si.SIPARIS_NO LIKE s.Siparis_No + '%' or s.Siparis_No = si.SIPARIS_NO
LEFT JOIN " . getTableName('aa_erp_kt_teklifler') . " te on si.X_TEKLIF_NO  = te.TEKLIF_NO
LEFT JOIN " . getTableName('aa_erp_kt_firsatlar') . " f on f.FIRSAT_NO = te.X_FIRSAT_NO
where s.Yil='$yil' and s.KANAL='GENEL' and SUBSTRING(Siparis_No,1,4)<>'T200'
AND f.PARA_BIRIMI is not NULL
GROUP BY ((te.t_maliyet - ISNULL(te.KOMISYON_H,0))),(((te.t_satis - (ISNULL(te.KOMISYON_F1,0) + ISNULL(te.KOMISYON_F2,0)))-(te.t_maliyet - ISNULL(te.KOMISYON_H,0)))),((te.t_satis - (ISNULL(te.KOMISYON_F1,0) + ISNULL(te.KOMISYON_F2,0)))),f.PARA_BIRIMI,te.X_FIRSAT_NO,s.Fatura_No, s.Yil, s.Siparis_No, s.Ch_Kodu, s.Satis_Temsilcisi, s.Marka, s.Ay, s.CEYREK, s.DONEM, s.Ch_Unvani
order by s.Fatura_No
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

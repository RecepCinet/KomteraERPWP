<?php
// Bayiler Grid - SOPHOS, WATCHGUARD, Dikkat/Kara Liste ile

error_reporting(E_ALL);
ini_set('display_errors', true);

include '../../_conn.php';

$sql = "SELECT
    b.CH_UNVANI,
    b.CH_KODU,
    b.VADE,
    k.dikkat_listesi,
    k.kara_liste,
    (SELECT TOP 1 seviye FROM atest_aa_erp_kt_bayiler_markaseviyeleri s WHERE s.MARKA='SOPHOS' AND s.CH_KODU=b.CH_KODU) AS SOPHOS,
    (SELECT TOP 1 seviye FROM atest_aa_erp_kt_bayiler_markaseviyeleri s WHERE s.MARKA='WATCHGUARD' AND s.CH_KODU=b.CH_KODU) AS WATCHGUARD
FROM aaa_erp_kt_bayiler b
LEFT JOIN atest_aa_erp_kt_bayiler_kara_liste k ON b.CH_KODU = k.ch_kodu
WHERE b.CH_KODU LIKE '120%'
ORDER BY b.CH_UNVANI";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

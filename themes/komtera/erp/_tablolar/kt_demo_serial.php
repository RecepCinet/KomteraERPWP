<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);
include '../../_conn.php';
$sql="select * from aaa_erp_kt_demo_serial";
//$sql = "select MARKA,SKU,SERIAL_NO,ACIKLAMA,
//(select top 1 d.id from aa_erp_kt_demolar d where d.SIL=0 AND d.DEMO_DURUM<>'8' AND (d.SERIAL_NO=ds.SERIAL_NO OR (d.SERIAL_NO is null AND d.SKU=ds.SKU))) AS KILIT,
//(select top 1 'Satıldı' from aa_erp_kt_demolar d where d.SIL=0 AND d.DEMO_DURUM='9' AND (d.SERIAL_NO=ds.SERIAL_NO OR (d.SERIAL_NO is null AND d.SKU=ds.SKU))) AS SATILDI,
//(select top 1 d.DEMO_DURUM_TEXT from aa_erp_kt_demolar_view d where d.SIL=0 AND d.DEMO_DURUM<>'8' AND (d.SERIAL_NO=ds.SERIAL_NO  OR (d.SERIAL_NO is null AND d.SKU=ds.SKU))) AS DDURUM
//from aaa_erp_kt_demo_serial ds
//--UNION ALL
//--select MARKA,SKU,SERIAL_NO,ACIKLAMA,id,'','' from aa_erp_kt_demolar d where d.SIL=0 and (d.DEMO_DURUM<>8 and d.DEMO_DURUM<>9)
//";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>
<?PHP
include '_check.php';
include '../../_conn.php';
require_once '../../inc/table_helper.php';

$gelen = $_POST['data'];
$cs=$_POST['cs'];
$cc=$_POST['cc'];
$marka=$_POST['marka'];
$st = "%";
$bas="%";
if (substr($gelen, 0, 1) == "=") {
    $st = "";
    $bas="";
    $gelen= substr($gelen, 1);
}
$tableName = getTableName('aa_erp_kt_fiyat_listesi');
$filter = "MARKA='$marka' AND (SKU like '$bas$gelen%' or urunAciklama like '$bas$gelen%')";
$sql = "select top 50 * from {$tableName} where $filter order by SKU";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1 = $data[$t]['sku'];
        $sut2 = $data[$t]['urunAciklama'];
        $sut3 = $data[$t]['tur'];
        $sut4 = $data[$t]['cozum'];
        $sut5 = $data[$t]['listeFiyati'];
        ?>
        <tr class='ss-button'>
            <td width='40' align='center'><a href="#" onclick='Sec("COMMON_WINDOW", "Urun_Sec_Back", "<?PHP echo $sut1; ?>");'><?php echo __('Ekle', 'komtera'); ?></a></td>
            <td width='90'><?PHP echo $sut1; ?></td>
            <td><?PHP echo "$sut1-$sut2 ($sut4)"; ?></td>
            <td width="80" align="right"><?PHP echo number_format($sut5, 2, "." , ","); ?></td>
        </tr>
        <?PHP
    }
    ?>
</table>
<?PHP
include '_check.php';
include '../../_conn.php';
$gelen = $_POST['data'];
$cs=$_POST['cs'];
$cc=$_POST['cc'];
$st = "%";
if (substr($gelen, 0, 1) == "=") {
    $st = "";
    $gelen = substr($gelen, 1);
}
$filter = "FIRSAT_NO like '' or BAYI_ADI like '$st$gelen%' or MUSTERI_ADI like '$st$gelen%'";
if ($gelen == "") {
    $filter = "1=1";
}
//$sql = "Select top 22 * from aa_erp_kt_musteriler where $filter order by musteri";
$sql = "select top 50 FIRSAT_NO,BAYI_ADI,MUSTERI_ADI from aa_erp_kt_firsatlar where $filter order by FIRSAT_NO";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1 = $data[$t]['FIRSAT_NO'];
        $sut2 = $data[$t]['BAYI_ADI'] . " (" . $data[$t]['MUSTERI_ADI'] . ")";
        ?>
        <tr onclick='Sec("COMMON_WINDOW", "Firsat_Common_Back", "<?PHP echo $sut1; ?>");' class='ss-button'>
            <td width='90'><?PHP echo $sut1; ?></td><td align='left'><?PHP echo $sut2; ?></td>
        </tr>
        <?PHP
    }
    ?>
</table>
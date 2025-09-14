<?PHP
include '_check.php';
include '../../_conn.php';
$gelen = $_POST['data'];
$st="%";
if (substr($gelen,0,1)=="=") {
    $st="";
    $gelen= substr($gelen,1);
}
$filter="CH_UNVANI like '$st$gelen%' or CH_KODU like '$st$gelen%'";
if ($gelen=="") {
    $filter="1=1";
}
//$sql = "Select top 22 * from aa_erp_kt_musteriler where $filter order by musteri";
$sql="select top 50 CH_KODU,CH_UNVANI from aaa_erp_kt_bayiler where $filter order by CH_UNVANI";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1=$data[$t]['CH_UNVANI'];
        $sut2=$data[$t]['CH_KODU'];
            ?>
            <tr onclick='Sec("Bayiler", "BayilerPrev", "<?PHP echo $sut2; ?>");' class='ss-button'>
                <td><?PHP echo $sut1; ?></td><td width='50' align='center'><?PHP echo $sut2; ?></td>
            </tr>
            <?PHP
    }
    ?>
</table>
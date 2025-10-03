<?PHP
include '_check.php';
include '../_conn.php';
$gelen = $_POST['data'];
$cs=$_POST['cs'];
$cc=$_POST['cc'];
$st="%";
if (substr($gelen,0,1)=="=") {
    $st="";
    $gelen= substr($gelen,1);
}
$filter="musteri like '$st$gelen%'";
if ($gelen=="") {
    $filter="1=1";
}
//$sql = "Select top 22 * from aa_erp_kt_musteriler where $filter order by musteri";
$sql="select top 50 id,musteri,(select count(id) from aa_erp_kt_musteriler_yetkililer where musteri_id=m.id ) as ys from aa_erp_kt_musteriler m
    where musteri is not null AND $filter order by musteri";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $id=$data[$t]['id'];
        $sut1=$data[$t]['musteri'];
        $sut2=$data[$t]['ys'];
            ?>
            <tr onclick='Sec("COMMON_WINDOW", "Musteri_Common_Back", "<?PHP echo $id; ?>");' class='ss-button'>
                <td><?PHP echo $sut1; ?></td><td width='50' align='center'><?PHP echo $sut2; ?></td>
            </tr>
            <?PHP
    }
    ?>
</table>

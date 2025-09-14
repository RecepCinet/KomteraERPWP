<?PHP
include '_check.php';
include '../../_conn_fm.php';
$gelen = $_POST['data'];
$dep = $_POST['dep'];
$cs=$_POST['cs'];
$cc=$_POST['cc'];
$marka=$_POST['marka'];
//$dep = "'Sat','Tek'";
$st = "%";
if (substr($gelen, 0, 1) == "=") {
    $st = "";
    $gelen = substr($gelen, 1);
}
$marka_ek="";
if ($marka!="") {
    $marka_ek=" markalar like '%$marka%' AND ";
}
$filter = "kullanici like '$st$gelen%' and pasif is null and company like '%Komtera%'";
if ($gelen == "") {
    $filter = " 1=1 and pasif is null and company like '%Komtera%' ";
}
//echo $marka;
$sql = "select adiSoyadi,kullanici,company,departman,bu from TF_USERS where $marka_ek right(departman,6)<>'Destek' AND left(departman,3) IN ($dep) AND $filter order by kullanici";
//echo $sql;
$stmt = $conn2->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1 = $data[$t]['kullanici'];
        $sut2 = $data[$t]['bu'];
        $sut3 = $data[$t]['adiSoyadi'];
        ?>
        <tr onclick='Sec("<?PHP echo $cs; ?>", "<?PHP echo $cc; ?>", "<?PHP echo $sut1 . '\n' . $sut2 . '\n' . $sut3; ?>");' class='ss-button'>
            <td><?PHP echo $sut1; ?></td><td><?PHP echo $sut3; ?></td>
        </tr>
        <?PHP
    }
    ?>
</table>
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
$filter = "b.CH_UNVANI like '$st$gelen%' or b.CH_KODU like '$st$gelen%'";
if ($gelen == "") {
    $filter = "1=1";
}
//$sql = "Select top 22 * from aa_erp_kt_musteriler where $filter order by musteri";
$sql = "select top 100 b.CH_KODU,b.CH_UNVANI,k.dikkat_listesi dl,k.kara_liste kl from aaa_erp_kt_bayiler b LEFT JOIN aa_erp_kt_bayiler_kara_liste k
ON b.CH_KODU = k.ch_kodu 
where $filter
order by b.CH_UNVANI";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1 = $data[$t]['CH_UNVANI'];
        $sut2 = $data[$t]['CH_KODU'];
        $dl=$data[$t]['dl'];
        $kl=$data[$t]['kl'];
        $renk="";
        $kl_ek="";
        if ($dl==="1") {
            $renk="style='background: #FF6666;color: #000000;'}";
            $kl_ek=" (Dikkat Listesinde)";
        }
        if ($kl==="1") {
            $renk="style='background: #111111;color: #FFFFFF;'}";
            $kl_ek=" (Kara Listede)";
        }
        ?>
        <tr onclick='Sec("COMMON_WINDOW", "Bayi_Common_Back", "<?PHP echo $sut2; ?>");' class='ss-button'>
            <td <?PHP echo $renk; ?>><?PHP echo $sut1 . $kl_ek ?></td><td width='50' align='center'><?PHP echo $sut2; ?></td>
        </tr>
        <?PHP
    }
    ?>
</table>
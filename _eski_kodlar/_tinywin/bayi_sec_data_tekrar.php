<?PHP
include '_check.php';
include '../_conn.php';
$gelen = $_POST['data'];
$cs=$_POST['cs'];
$cc=$_POST['cc'];
$marka=$_POST['marka'];
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
$sql = "select top 50 b.CH_KODU,b.CH_UNVANI,k.dikkat_listesi dl,k.kara_liste kl,
(select top 1 seviye from aa_erp_kt_bayiler_markaseviyeleri bms where bms.marka='$marka' and bms.CH_KODU=b.CH_KODU) as seviye
from aaa_erp_kt_bayiler b
LEFT JOIN aa_erp_kt_bayiler_kara_liste k
ON b.CH_KODU = k.ch_kodu
where $filter
order by b.CH_UNVANI";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $sut1 = mb_substr($data[$t]['CH_UNVANI'],0,50);
        if (strlen($data[$t]['CH_UNVANI'])>50) {
            $sut1=$sut1."...";
        }
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
        $temp=$data[$t]['seviye'];
        $seviye=$temp;
        if ($temp=="1") {
            $seviye="AUTHORIZED";
        }
        if ($temp=="2") {
            $seviye="SILVER";
        }
        if ($temp=="3") {
            $seviye="GOLD";
        }
        if ($temp=="4") {
            $seviye="PLATINUM";
        }
        
//WHEN bs.seviye = '1' THEN 'AUTHORIZED'
//WHEN bs.seviye = '2' THEN 'SILVER'
//WHEN bs.seviye = '3' THEN 'GOLD'
//WHEN bs.seviye = '4' THEN 'PLATINUM'
        
        $arr=Array(
            "ch_kodu" => $sut2,
            "bayi" => $data[$t]['CH_UNVANI'],
            "seviye" => $seviye
            );
        $json= json_encode($arr);
        $json= base64_encode($json);
        ?>
        <tr onclick="Sec('Bayi_Sec_Yeniden', '<?PHP echo $json; ?>','<?PHP echo $data[$t]['CH_UNVANI']; ?>');" class='ss-button'>
            <td <?PHP echo $renk; ?>><?PHP echo $sut1 . $kl_ek; ?></td><td width='50' align='center'><?PHP echo $sut2; ?></td>
        </tr>
        <?PHP
    }
    ?>
</table>
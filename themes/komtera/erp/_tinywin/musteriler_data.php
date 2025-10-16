<?PHP
include '_check.php';
include '../../_conn.php';
$gelen = $_POST['data'];
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
$sql="select top 100
id,
musteri,
(select count(id) from aa_erp_kt_musteriler_yetkililer where musteri_id=m.id ) as ys,
stuff((
select CHAR(10) + '* ' + f.SEVKIYAT_ADRES
from aa_erp_kt_firsatlar f where f.MUSTERI_ADI = m.musteri
for xml path (''), type).value('.','nvarchar(max)'),1,1,'') as adresler
from aa_erp_kt_musteriler m
where musteri is not null and $filter
order by musteri";

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class="tt" width="100%" border="1" cellpadding="2" cellspacing="2">
    <?PHP
    for ($t = 0; $t < count($data); $t++) {
        $id=$data[$t]['id'];
        $sut1=$data[$t]['musteri'];
        $sut2=$data[$t]['ys'];
        $sut3=$data[$t]['adresler'];
        $sut3=str_replace("\n","<br />", $sut3);
            ?>
            <tr onclick='Sec("Musteriler", "MusterilerEdit", "<?PHP echo $id; ?>");' class='ss-button'>
                <td width='350'><?PHP echo $sut1; ?></td><td align='left'><?PHP echo $sut3; ?></td>
            </tr>
            <?PHP
    }
    ?>
</table>
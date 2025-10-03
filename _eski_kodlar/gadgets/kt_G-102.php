<style>
table {
  border-collapse: collapse;
}

th, td {
  border: 1px solid aliceblue;
  color: #333333;
}
</style>
<?php
// Onay/Is Atama/Bilgilendirmeler
$sil = isset($_GET['sil']) ? $_GET['sil'] : "";
if ($sil != "") {
    include "../_conn.php";
    $string = "delete from aa_erp_kt_is_atama where id='$sil'";
    $stmt = $conn->prepare($string);
    $stmt->execute();
    die();
}
?>
<script>
    function SatirSil(neresi) {
        var els = document.getElementById(neresi).getElementsByTagName("td");
        for (var i = 0; i < els.length; i++) {
            els[i].style.background = "#FFEEEE";
        }
        Sil(neresi);
    }
    function Sil(neresi) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                //document.getElementById("demo").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "http://<?PHP echo $_SERVER[HTTP_HOST]; ?>/gadgets/kt_G-102.php?sil=" + neresi, true);
        xhttp.send();
    }
</script>
<?PHP
$ek="";
if ($cryp==="recep.cinet") {
    $ek=" or 1=1 ";
}
$stan = "kime='$cryp'";
$kactane=15;
// ticket #247
// select a.*,t.ONAY1,t.ONAY2,t.VADE_ONAY from aa_erp_kt_is_atama a LEFT JOIN aa_erp_kt_teklifler t ON a.mid=t.TEKLIF_NO 
$string = "select top 15 ia.*,(select ISNULL(t.ONAY1,0)+ISNULL(t.ONAY2,0) from aa_erp_kt_teklifler t where t.TEKLIF_NO=ia.mid) as ONAYY from aa_erp_kt_is_atama ia
where $stan order by cd desc,ct desc";
//echo $string;
$stmt = $conn->prepare($string);
$stmt->execute();
$datafull = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<br />
<table style="width: 100%;" cellpadding="3" border="0" class="hideextra">
    <thead>
    <th colspan="8" style="background-color: #f2f2f2;text-align: left;width: 100px;height: 28px;">Onay/İş Atama/Bilgilendirmeler (Son <?PHP echo $kactane; ?> Kayıt)</th>
</thead>
<tr>
    <td><b>Modül</b></td><td><b>NO</b></td><td><b>Zaman</b></td><td><b>Beklenen</b></td><td><b>Onay</b></td><td><b>Kimden</b></td><td><b>Kime</b></td><td>&nbsp;</td>
</tr>
<?PHP
$sayy=0;
foreach ($datafull as $key => $data) {
    $sayy++;
        ?>
        <tr id="<?php echo $data['id']; ?>">
            <td width="90" style="background-color: #FFFFFF;text-align: left;"><?php echo $data['modul']; ?></td>
            <td width="90" style="background-color: #FFFFFF;text-align: left;">
                <a href="#" onclick="FileMaker.PerformScriptWithOption('<?php echo $data['modul']; ?>', 'Ac' + '|' + <?php echo "'" . $data['mid'] . "'"; ?>)"><?php echo $data['mid']; ?></a>
            </td>
            <?PHP
            error_reporting(E_ALL);
            ini_set("display_errors", true);

            $dd = $data['cd'];            
            $tt = substr($data['ct'], 0,8);

            $onay=$data['ONAYY'];
            
            ?>
            <td style="background-color: #FFFFFF;text-align: left;"><?php echo $dd . " &nbsp; " . $tt; ?></td>
            <td style="background-color: #FFFFFF;text-align: left;"><?php echo $data['beklenen']; ?></td>
            <td style="background-color: #FFFFFF;text-align: center;"><?php
                if ($onay==1) {
                    echo "✓";
                }
                if ($onay==2) {
                    echo "✓";
                }
                if ($onay==0) {
                    echo "&nbsp;";
                }

                ?></td>
<td width="110" style="background-color: #FFFFFF;text-align: left;"><?php echo $data['kimden']; ?></td>
<td width="110" style="background-color: #FFFFFF;text-align: left;"><?php echo $data['kime']; ?></td>
            <td width="60"  style="background-color: #FFFFFF;text-align: center;"><a href="#" onclick='SatirSil(<?php echo $data['id']; ?>);'>Sil</a></td>
        </tr>
        <?PHP
    }

?>
</table><br />
<?php

if ($sayy>=15) {
    echo "...";
}

?>
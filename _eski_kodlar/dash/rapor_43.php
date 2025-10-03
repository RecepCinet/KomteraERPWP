<?php
error_reporting(0);
ini_set("display_errors",false);

$serverName = "172.16.85.76";

try {
    $options = array(
        "CharacterSet" => "UTF-8"
    );
    $conn3 = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "!!!Crm!!!", $options);
    $conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "!---MS SQL Baglanti Sorunu!---<br />" . PHP_EOL;
    die(print_r($e->getMessage()));
}





$sqlstring="select sum(DLR_TUTAR) as TUTAR from aaaa_acik_siparisler ac";
$stmt = $conn3->query($sqlstring);
$toplam = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['TUTAR'];






?>
<table width="850" border="0" style="border: 0;"><tr>
        <td align="left" bgcolor="black"><h1 style="color: chartreuse"><?PHP echo number_format($toplam,0,"," , ".") . " USD"; ?></h1></td>
    </tr>
</table>
<?php

    $sqlstring="select MARKA,sum(DLR_TUTAR) as TUTAR from aaaa_acik_siparisler ac group by MARKA order by TUTAR desc";
    $stmt = $conn3->query($sqlstring);
    $marka_bazli = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <table width="850" id="table1"><tr><td valign="top" border="0">

<h3>Marka Bazlı:</h3>
    <table width="380" border="0">
        <?PHP
foreach ($marka_bazli as $satir) {
        ?>
        <tr>
           <td align="left"><?PHP echo $satir['MARKA']; ?></td><td align="right"><?PHP echo number_format($satir['TUTAR'],0,"," , "."); ?></td>
        </tr>
        <?PHP
}
        ?>
    </table>

    </td><td valign="top">

<?php

$sqlstring="select MUSTERI_TEMSILCISI,sum(DLR_TUTAR) as TUTAR from aaaa_acik_siparisler ac group by MUSTERI_TEMSILCISI order by MUSTERI_TEMSILCISI";
$stmt = $conn3->query($sqlstring);
$marka_bazli = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
    <h3>Temsilci Bazlı:</h3>
    <table width="380" border="0">
        <?PHP
        foreach ($marka_bazli as $satir) {
            ?>
            <tr>
                <td align="left"><?PHP echo $satir['MUSTERI_TEMSILCISI']; ?></td><td align="right"><?PHP echo number_format($satir['TUTAR'],0,"," , "."); ?></td>
            </tr>
            <?PHP
        }
        ?>
    </table>
            </td></tr></table>
<?PHP
$dur=1;
?>
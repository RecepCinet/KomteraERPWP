<?php
$sqlstring = "select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
group by MARKA
ORDER BY sum([USD_TUTAR]) desc
";
// deneme
$sql=odbc_exec($conn,$sqlstring);

?>
<table width="100%">
    <tr bgcolor="yellow" height="50px;">
        <th>&nbsp;</th>
        <th>Ocak</th>
        <th>Şubat</th>
        <th>Mart</th>
        <th>Nisan</th>
        <th>Mayıs</th>
        <th>Haziran</th>
        <th>Temmuz</th>
        <th>Ağustos</th>
        <th>Eylül</th>
        <th>Ekim</th>
        <th>Kasım</th>
        <th>Aralık</th>
        <th>&nbsp;</th>
    </tr>

    <?PHP
    $toplam1=0;
    $toplam2=0;
    $toplam3=0;
    $toplam4=0;
    $toplam5=0;
    $toplam6=0;
    $toplam7=0;
    $toplam8=0;
    $toplam9=0;
    $toplam10=0;
    $toplam11=0;
    $toplam12=0;
    $toplamt=0;

    while ($rs=odbc_fetch_array($sql)) {
        $toplam1 += $rs['Ocak'];
        $toplam2 += $rs['Subat'];
        $toplam3 += $rs['Mart'];
        $toplam4 += $rs['Nisan'];
        $toplam5 += $rs['Mayis'];
        $toplam6 += $rs['Haziran'];
        $toplam7 += $rs['Temmuz'];
        $toplam8 += $rs['Agustos'];
        $toplam9 += $rs['Eylul'];
        $toplam10 += $rs['Ekim'];
        $toplam11 += $rs['Kasim'];
        $toplam12 += $rs['Aralik'];
        $toplamt += $rs['TOPLAM'];
        ?>
        <tr>
            <td align="left"><?PHP echo "<b>" . $rs['MARKA'] . "</b>" ?></td>
            <td align="right"><?PHP echo number_format($rs['Ocak'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Subat'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Mart'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Nisan'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Mayis'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Haziran'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Temmuz'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Agustos'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Eylul'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Ekim'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Kasim'],0,",",".") ?></td>
            <td align="right"><?PHP echo number_format($rs['Aralik'],0,",",".") ?></td>
            <td align="right" bgcolor="#f0f8ff"><?PHP echo "<b>" . number_format($rs['TOPLAM'],0,",",".") . "</b>" ?></td>
        </tr>
        <?PHP
    }
    ?>
    <tr bgcolor="#f0f8ff">
        <td align="left"></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam1,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam2,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam3,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam4,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam5,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam6,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam7,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam8,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam9,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam10,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam11,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplam12,0,",",".") . "</b>" ?></td>
        <td align="right"><?PHP echo "<b>" . number_format( $toplamt,0,",",".") . "</b>" ?></td>
    </tr>
</table>

<?PHP

$dur=1;

?>


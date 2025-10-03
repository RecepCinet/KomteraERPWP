<?php
$sqlstring = "select 1 as s,MARKA,
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
from ERP_SATIS_ANALIZ_319_20XX
WHERE Yil='2025'
group by MARKA
ORDER BY MARKA

";
// ORDER BY sum([USD_TUTAR]) desc


$sql=odbc_exec($conn,$sqlstring);

?>
<table id='table2' width="100%">
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
<br />
<input type="button" value="Tabloyu Seç Kopyala" onclick="selectElementContents( document.getElementById('table2') );">



<?php

echo "<br /><br /><br />Net Tutar<br /><br />";

$sqlstring = "SELECT
    s1.MARKA,
    s1.Ocak - COALESCE(s2.Ocak, 0) AS Ocak,
    s1.Subat - COALESCE(s2.Subat, 0) AS Subat,
    s1.Mart - COALESCE(s2.Mart, 0) AS Mart,
    s1.Nisan - COALESCE(s2.Nisan, 0) AS Nisan,
    s1.Mayis - COALESCE(s2.Mayis, 0) AS Mayis,
    s1.Haziran - COALESCE(s2.Haziran, 0) AS Haziran,
    s1.Temmuz - COALESCE(s2.Temmuz, 0) AS Temmuz,
    s1.Agustos - COALESCE(s2.Agustos, 0) AS Agustos,
    s1.Eylul - COALESCE(s2.Eylul, 0) AS Eylul,
    s1.Ekim - COALESCE(s2.Ekim, 0) AS Ekim,
    s1.Kasim - COALESCE(s2.Kasim, 0) AS Kasim,
    s1.Aralik - COALESCE(s2.Aralik, 0) AS Aralik,
    s1.TOPLAM - COALESCE(s2.TOPLAM, 0) AS TOPLAM
FROM
    (SELECT
         MARKA,
         SUM(CASE WHEN [AYRAKAM] = '01' THEN [USD_TUTAR] ELSE 0 END) AS Ocak,
         SUM(CASE WHEN [AYRAKAM] = '02' THEN [USD_TUTAR] ELSE 0 END) AS Subat,
         SUM(CASE WHEN [AYRAKAM] = '03' THEN [USD_TUTAR] ELSE 0 END) AS Mart,
         SUM(CASE WHEN [AYRAKAM] = '04' THEN [USD_TUTAR] ELSE 0 END) AS Nisan,
         SUM(CASE WHEN [AYRAKAM] = '05' THEN [USD_TUTAR] ELSE 0 END) AS Mayis,
         SUM(CASE WHEN [AYRAKAM] = '06' THEN [USD_TUTAR] ELSE 0 END) AS Haziran,
         SUM(CASE WHEN [AYRAKAM] = '07' THEN [USD_TUTAR] ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN [AYRAKAM] = '08' THEN [USD_TUTAR] ELSE 0 END) AS Agustos,
         SUM(CASE WHEN [AYRAKAM] = '09' THEN [USD_TUTAR] ELSE 0 END) AS Eylul,
         SUM(CASE WHEN [AYRAKAM] = '10' THEN [USD_TUTAR] ELSE 0 END) AS Ekim,
         SUM(CASE WHEN [AYRAKAM] = '11' THEN [USD_TUTAR] ELSE 0 END) AS Kasim,
         SUM(CASE WHEN [AYRAKAM] = '12' THEN [USD_TUTAR] ELSE 0 END) AS Aralik,
         SUM([USD_TUTAR]) AS TOPLAM
     FROM ERP_SATIS_ANALIZ_319_20XX
     WHERE Yil = '2025'
     GROUP BY MARKA) AS s1
        LEFT JOIN
    (SELECT
         MARKA,
         SUM(CASE WHEN MONTH(FATTAR) = 1 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ocak,
         SUM(CASE WHEN MONTH(FATTAR) = 2 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Subat,
         SUM(CASE WHEN MONTH(FATTAR) = 3 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mart,
         SUM(CASE WHEN MONTH(FATTAR) = 4 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Nisan,
         SUM(CASE WHEN MONTH(FATTAR) = 5 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mayis,
         SUM(CASE WHEN MONTH(FATTAR) = 6 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Haziran,
         SUM(CASE WHEN MONTH(FATTAR) = 7 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN MONTH(FATTAR) = 8 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Agustos,
         SUM(CASE WHEN MONTH(FATTAR) = 9 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Eylul,
         SUM(CASE WHEN MONTH(FATTAR) = 10 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ekim,
         SUM(CASE WHEN MONTH(FATTAR) = 11 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Kasim,
         SUM(CASE WHEN MONTH(FATTAR) = 12 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Aralik,
         SUM(COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0)) AS TOPLAM
     FROM aaaa_erp_kt_komisyon_raporu_ham
     WHERE YEAR(FATTAR) = 2025 AND FATTAR IS NOT NULL
     GROUP BY MARKA) AS s2
    ON s1.MARKA = s2.MARKA
ORDER BY s1.MARKA;


";
// ORDER BY sum([USD_TUTAR]) desc


$sql=odbc_exec($conn,$sqlstring);

?>
<table id='table1' width="100%">
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

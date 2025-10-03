<?php
error_reporting(E_ERROR);

$sqlstring=<<<SQL
select marka from aaaa_siparisler fl
where marka is not null
and CD > DATEADD(day,-15,GETDATE())
group by marka 
SQL;
$sql=odbc_exec($conn,$sqlstring);

$markalar="";

while ($rs = odbc_fetch_array($sql)) {
	$markalar .= "SUM(CASE WHEN MARKA = '" . $rs['marka'] . "' THEN DLR_TUTAR ELSE 0 END) AS [" . $rs['marka'] . "],";
}

$sqlstring=<<<SQL
select format(CD,'dd.MM.yyyy') AS CD,
$markalar
sum(DLR_TUTAR) as Tutar
from aaaa_siparisler
where CD > DATEADD(day,-15,GETDATE())
group by CD
SQL;

$sql=odbc_exec($conn,$sqlstring);
$cikis="";
$arr=Array();

$say=0;
echo "<table id='table1'>";
while ($rs = odbc_fetch_array($sql)) {
    
    if ($say==0) {
        echo "<tr bgcolor='yellow'>";
        for ($col=1; $col<=odbc_num_fields($sql); $col++) {
            echo "<th>"  . odbc_field_name($sql, $col) . "</th>";
            $toplamlar[odbc_field_name($sql, $col)]=0;
        }
        echo "</tr>";
    }
    $say++;
    echo "<tr>";

    for ($col=1; $col<=odbc_num_fields($sql); $col++) {
       
        if ($col==1) {
             echo "<td align=left>";
            echo $rs[odbc_field_name($sql, $col)];
        } else {
            echo "<td align=right>";
            $hj=$rs[odbc_field_name($sql, $col)];
            $toplamlar[odbc_field_name($sql, $col)] += $hj;
            $nomal1="";
            $nomal2="";
            if ($col==odbc_num_fields($sql)) {
                $nomal1="<b>";
                $nomal2="</b>";
            }
            echo $nomal1 . number_format($rs[odbc_field_name($sql, $col)],0,",",".") . $nomal2;
            //if ($col<odbc_num_fields($sql)) {
                //echo "+";
                //$t["aa$col"] = $t[$col] + $rs[odbc_field_name($sql, $col)];
                //echo $t["aa$col"];
            //}
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "<tr>";
    foreach ($toplamlar as $key => $value) {
        echo "<td align=right>";
        if ($value>0) {
        echo "<b>" . number_format($value,0,",",".") . "</b>";
        }
        echo "</td>";
    }
echo "</tr>";
?>
</table>
<?PHP
$dur=1;
?>

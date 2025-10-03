<?php
$conn = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "crm2017!?", $options);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

die();


$sqlstring=<<<SQL
select  f.Marka_Kilidi from T_10 f,T_20 s
where s.firsatNo=f.FirsatLabel AND s."durum_kod"<>2
group by f.Marka_Kilidi
SQL;
$sql=odbc_exec($conn2,$sqlstring);
$cikis="";
$arr=Array();
while ($rs=odbc_fetch_array($sql)) {
    $t="sum(CASE f.Marka_Kilidi WHEN '###ADI###' THEN  s.satis_fiyati END) AS \"###ADI###\",\n";
    $temp=$rs['Marka_Kilidi'];
    $arr[]=$temp;
    $cikis .= str_replace("###ADI###", $temp, $t);
}
$sqlstring = <<<SQL
select f."Musteri_Temsilcisi" AS MT,
        
        
###MARKALAR###
        
sum(s.satis_fiyati) as Tutar from T_10 f,T_20 s
where s.firsatNo=f.FirsatLabel AND s."durum_kod"<>2
group by f."Musteri_Temsilcisi"
ORDER BY MT
SQL;

$sqlstring= str_replace("###MARKALAR###", $cikis, $sqlstring);

//echo $sqlstring;

$sql=odbc_exec($conn2,$sqlstring);

//odbc_fetch_row($sql);
$say=0;
$toplamlar=Array();
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

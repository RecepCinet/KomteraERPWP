<?php

$sqlstring=<<<SQL
select  f.Marka_Kilidi from T_10 f,T_20 s
where s.firsatNo=f.FirsatLabel 
        AND s.cd>=CURDATE-15
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
$sql = <<<SQL
select STRVAL(DAY(s.cd)) + ' ' + STRVAL(MONTHNAME(s.cd)) + ' ' + STRVAL(DAYNAME(s.cd)) + ' ' AS "tarih",
###MARKALAR###
sum(s.satis_fiyati) as "Tutar",
count(s.satis_fiyati) as "Adet"
from T_10 f,T_20 s
where s.firsatNo=f.FirsatLabel AND s.cd>=CURDATE-15
group by s.cd
order by s.cd
SQL;

$sql= str_replace("###MARKALAR###", $cikis, $sql);
//echo $sql;

//  s.cd_m=MONTH(CURDATE) and s.cd_y=YEAR(CURDATE) and
?>

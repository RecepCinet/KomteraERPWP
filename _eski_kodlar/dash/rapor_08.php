<h3>Rapor 8 - Satış Türüne Göre Rakamlar</h3><br />
<?php

$sql="SELECT TOP 3 [FAT_OZEL_KODU] as FOK,sum ([USD Tutar]) as Tutar FROM ERP_SATIS_ANALIZ GROUP BY [FAT_OZEL_KODU] order by Tutar desc
";
$rs=odbc_exec($conn,$sql);
if (!$rs)
  {exit("Error in SQL");}
echo "<table width=250 border=1><tr>";
echo "<th>Ay</th>";
echo "<th align=right>Tutar</th></tr>";
$key="";
$val="";
while (odbc_fetch_row($rs))
{
  $ay= odbc_result($rs,"FOK");
  $tutary=number_format(odbc_result($rs,"Tutar"), 0, ',', '.');
  $tutar=odbc_result($rs,"Tutar");
  
    $key .= "|" . $ay;
    $val .= "|" . $tutar;
      
  
  echo "<tr><td>$ay</td>";
  echo "<td align=right>$tutary</td></tr>";
}
odbc_close($conn);
echo "</table>";
?>

<iframe width="100%;" height="500" src="chart_pie.php?title=Aylık Satış Raporu&key=<?PHP echo $key; ?>&val=<?PHP echo $val; ?>"></iframe>



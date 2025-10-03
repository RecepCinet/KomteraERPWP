<?php
error_reporting(0);
ini_set("display_errors", false);

include '../_conn.php';
$siparis_no = $_GET['siparis_no'];
$url = "select TOP 200 CreateDate,[NO],SONUC,MESAJ,* from ARYD_FIS_AKTARIM where PROJE_KOD='KANAL4' and OZEL_KOD='vatan'";
//echo $url;
$stmt = $conn->prepare($url);
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($rs);
$satir = "<table width=8000 border=0 cellpadding=3 cellspacing=0>\n";
$say = 0;
$bg = "#FFFFFF";
$temp="";
foreach ($rs as $record) {
    if ($say === 0) {
        $satir .= "<tr>\n";
        $sutun = "";
        foreach ($record as $key => $value) {
            $sutun .= "<td><b>" . $key . "</b></td>";
        }
        $satir .= $sutun;
        $satir .= "</tr>\n";
    }
    $say++;
    $satir .= "<tr>\n";
    $sutun = "";
    foreach ($record as $key => $value) {
        $sutun .= "<td>" . $value . "</td>";
         if ($key == "NO") {
            $noo = $value;
        }
    }
    
    
    
        
if ($temp != $noo) {
        if ($bg == "#EEEEEE") {
            $bg = "#FFFFFF";
        } else {
            $bg = "#EEEEEE";
        }
    }
$satir .= "<tr bgcolor='$bg'>\n";
    
    
    
    $temp = $noo;
    
    
    $satir .= $sutun;
    $satir .= "</tr>\n";
}
$satir .= "</table>";

echo $satir;
odbc_close($conn);
?>

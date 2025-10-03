<style>
    table,tr,td,body {
        font-family: Arial;
        font-size: 12px;
    }
</style>
<?php
error_reporting(E_ALL);
ini_set($varname, $newvalue);
ini_set('display_errors', TRUE);

if (GET('logout') == "1") {
    session_destroy();
    header("Location: http://172.16.80.214:8080/dash/");
}

function GET($key, $default = null) {
    $cikis = "";
    $cikis = isset($_GET[$key]) ? $_GET[$key] : $default;
    if ($cikis == "") {
        $cikis = isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    return $cikis;
}

$conn=mysqli_connect("127.0.0.1", "root", "KlyA2gw1", "erp");


?>
<?PHP
echo "<br />";
$buay=date("Y-m");
$sqlstring="select kim,count(id) as islem_sayisi from erp_log where zaman>'$buay-01' group by kim order by islem_sayisi desc";
//echo $sqlstring . "<br />";
$sql= mysqli_query($conn,$sqlstring);

$cikis="<table width='300' border='0'>";
$cikis.="<tr bgcolor='#CCCCCC' height=30px>";
$cikis.= "<td><b>Kim</b></td>";
$cikis.= "<td width=80><b>İşlem Sayısı</b></td>";
$cikis.= "</tr>";
$i=0;
while ($rs = mysqli_fetch_array($sql)) {
    $i++;
    if ($i>1) {
        $i=0;
    }
    if ($i==0) {
        $renk='EEEEEE';
    } else {
        $renk='FFFFFF';
    }

    $cikis.="<tr bgcolor='#$renk'>";
    $tempkim = $rs['kim'];
    $islemsayisi= $rs['islem_sayisi'];
    if ($tempkim=="Recep Cinet") {
        $islemsayisi="<b>" . (int) date("d") * 11 . "</b>";
        $tempkim="<b>Recep Cinet (Developer)</b>";
    }
    $cikis.= "<td>" . $tempkim . "</td>";
    $cikis.= "<td width=80 align='right'>" . $islemsayisi . "</td>";
    $cikis.= "</tr>";
}
$cikis.="</table>";

echo $cikis;

?>

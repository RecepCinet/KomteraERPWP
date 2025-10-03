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
    header("Location: http://172.16.80.214/dash/");
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

$condition="1=1";

$kim=GET("kim");
$ay=GET("ay");
$noo=GET("no");
$yapilan=GET("yapilan");

if ($kim<>"") {
    $condition.=" AND kim like '$kim%' ";
}
if ($ay<>"") {
    $condition.=" AND month(zaman)=$ay ";
}
if ($yapilan<>"") {
    $condition.=" AND yapilan like '%$yapilan%' ";
}
if ($noo<>"") {
    $condition.=" AND xno like '$noo%' ";
}

?>
<table>
<form>
    <tr>
    <td>Ay</td><td><input type="text" name="ay" value="<?PHP echo $ay; ?>" style="width: 60px;"></td>
    <td>Kim</td><td><?PHP
    
    $kisiler="";
    $kisi_getir="select kim from erp_log group by kim";
    $sqlkim= mysqli_query($conn, $kisi_getir);
    
    while ($rs = mysqli_fetch_array($sqlkim)) {
        $kisiler.="|" . $rs['kim'];
    }
    
    $parc= explode("|", $kisiler);
    echo "<select name='kim' id='kim'>";
    echo "<option value=''>Hepsi</option>";
    
    for ($t=1;$t<count($parc);$t++) {
        $ek="";
        if ($kim==$parc[$t]) { $ek=" selected";}
        echo "<option" . $ek . ">"  . $parc[$t] . "</option>";
    }
    echo "</select>";
    
    ?></td>
    <td>Yapilan</td><td><input type="text" name="yapilan" value="<?PHP echo $yapilan; ?>" style="width: 260px;"></td> 
    <td colspan="3">Firsat/Siparis/Serial NO:</td><td><input type="text" name="no" value="<?PHP echo $noo; ?>" style="width: 70px;"></td>
    <td colspan="8">&nbsp;&nbsp;&nbsp;<input type="submit" name="cmd" value="Getir"> (Sadece 1.000 kayit getirir.)</td></tr>
    
</form>
</table>
<?PHP
echo "<br />";
$sqlstring="select * from erp_log WHERE $condition order by zaman desc limit 1000";
//echo $sqlstring . "<br />";
$sql= mysqli_query($conn,$sqlstring);

$cikis="<table width='100%' border='0'>";
    $cikis.="<tr bgcolor='#CCCCCC' height=30px>";
    $cikis.= "<td><b>Yapılan</b></td>";
    $cikis.= "<td width=80><b>NO</b></td>";
    $cikis.= "<td width=180><b>Kim</b></td>";
    $cikis.= "<td width=180><b>Zaman</b></td>";
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
    $cikis.= "<td>" . $rs['yapilan'] . "</td>";
    $cikis.= "<td width=80>" . $rs['xno'] . "</td>";
    $cikis.= "<td width=180>" . $rs['kim'] . "</td>";
    $cikis.= "<td width=180>" . $rs['zaman'] . "</td>";
    $cikis.= "</tr>";
}
$cikis.="</table>";

echo $cikis;

?>

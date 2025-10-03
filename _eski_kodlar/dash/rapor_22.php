<script>
    function Getir() {
        document.marka.submit();
    }
</script>
<?PHP
error_reporting(E_ERROR);
ini_set('display_errors', 1);
$aylar="select marka from VL_AYARLAR_ONAY_KAR order by marka";
$sql=odbc_exec($conn2,$aylar);
$ay=Array(""=>"");
while ($rs=odbc_fetch_array($sql)) {
    $ay[]=$rs['marka'];
}
?>
<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
<div class="ct-chart ct-perfect-fourth" style="width: 400px;height: 500px;"></div>
<?php
$yilham="2018|2019|2020|2021|2022|2023|2024|2025";
$yillar=explode("|",$yilham);
$k=explode(",","[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR],[USD_TUTAR]");
$seri1="";
error_reporting(E_ERROR);
$CONDITION="";
if ($_GET['marka']!="") {
    $CONDITION=" AND MARKA='" . $_GET['marka'] . "'";
    IF ($_GET['marka']=="GEMALTO") {
        $CONDITION=" AND ( MARKA='" . $_GET['marka'] . "' OR MARKA='THALES' )";
    }
    IF ($_GET['marka']=="THALES") {
        $CONDITION=" AND ( MARKA='" . $_GET['marka'] . "' OR MARKA='GEMALTO' )";
    }
    IF ($_GET['marka']=="VASCO") {
        $CONDITION=" AND ( MARKA='" . $_GET['marka'] . "' OR MARKA='ONESPAN' )";
    }
    IF ($_GET['marka']=="ONESPAN") {
        $CONDITION=" AND ( MARKA='" . $_GET['marka'] . "' OR MARKA='VASCO' )";
    }
}
$syy=0;
for ($t=0;$t<count($yillar);$t++) {
	$syy++;
    $standard="ERP_SATIS_ANALIZ_$yillar[$t] WHERE 1=1 ";
    if ((int)$yillar[$t]>=2021) {
        $standard="ERP_SATIS_ANALIZ_20XX WHERE yil='$yillar[$t]'";
    }
    if ((int)$yillar[$t]>=2024) {
        $standard="ERP_SATIS_ANALIZ_319_20XX WHERE yil='$yillar[$t]'";
    }
    $sqlstring = "SELECT SUM($k[$t]) AS TUTAR FROM $standard" . $CONDITION;
    //echo $sqlstring . "<br />";
    $sql = odbc_exec($conn, $sqlstring);
	if ($syy>1) {
		$seri1 .=",";
	}
    while ($rs=odbc_fetch_array($sql)) {
		$seri1 .= round($rs['TUTAR']/1000, 0);
    }
}
?>
<script>
    new Chartist.Bar('.ct-chart', {
        labels: ['2018', '2019', '2020', '2021', '2022', '2023', '2024', '2025'],
        series: [<?PHP echo $seri1; ?>],
    }, {
        distributeSeries: true
    });
</script>
<br />
<FORM name="marka" id="marka" action="">
    Marka: <select name="marka" onchange="Getir();">
        <?PHP
        echo "<option value=''>Hepsi</option>";
        for ($t=1;$t<count($ay);$t++) {
            if ($ay[$t]!="") {
                $ek="";
                if ($_GET['marka']==$ay[$t]) {
                    $ek=" selected";
                }
                echo "<option$ek>$ay[$t]</option>";
            }
        }
        ?>
    </select>
    <input type="hidden" name="menu" value="<?PHP echo $menu; ?>">
    <input type="hidden" name="altmenu" value="<?PHP echo $altmenu; ?>">
</FORM>
<?PHP
$dur=1;
?>
<?php
error_reporting(E_ERROR);
ini_set("display_errors", true);
ini_set('mssql.charset', 'UTF-8');

include '../_conn_fm.php';

$cryp=$_GET['cryp']!="" ? $_GET['cryp'] : $_POST['cryp'];
$crypB_ham=$_GET['crypB']!="" ? $_GET['crypB'] : $_POST['crypB'];
$crypB= substr(base64_decode($crypB_ham), 1 , strlen(base64_decode($crypB_ham))-4);
$tablo = $_GET['tablo'];
if ($cryp!=$crypB || $cryp=="") {
    echo "Hack!";
}

$string="select * from Tf_USERS where kullanici='" . $cryp . "'";
$stmt = $conn2->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$cinsiyet=$data[0]["cinsiyet"];
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<style>
    body,table,td {
        font-family: Verdana;
        font-size: 11px;
    }
    thead {
        font-size: 13px;
    }
    body {
        margin:0;
        padding:0;
    }
</style>
    <script>
        function EnterSubmit(event, cryp) {
            if (event.key === "Enter") {
                Ara(cryp);
            }
        }
    </script>
<script>
       function Ara(c,cb) {
        var kelime=document.getElementById("aranacak").value;
         $.ajax({
            url: '../result.php?cryp=' + c + '&ara='+kelime,
            data: {
//              list: JSON.stringify(gridChanges)
            },
            dataType: "json",
            type: "POST",
            async: true,
            beforeSend: function (jqXHR, settings) {
//
            },
            success: function (changes) {
                
            },
            complete: function (gelen) {
                document.getElementById("gelen").innerHTML = gelen.responseText;
            }
        });
    }
</script>
<!--<script type="text/javascript" src="../snowstorm.js"></script>-->
<script>
snowStorm.snowColor = '#99ccff';   // blue-ish snow!?
snowStorm.flakesMaxActive = 60;    // show more snow on screen at once
snowStorm.useTwinkleEffect = false; // let the snow flicker in and out of view
</script>
<table style="width: 100%;" cellpadding="2">

<?php //if ($cinsiyet == "k") { ?>
<!--    <tr>-->
<!--        <td height="100" align="center" background="http://172.16.84.214/gadgets/cicek.png">-->
<!--            <br /><br /><br /><br /><br />-->
<!--            <h1 id="renkliYazi" style="color: #FFFFFF;">8 Mart Dünya Kadınlar Gününüz Kutlu Olsun!</h1>-->
<!--        </td>-->
<!--    </tr>-->
<!--    <script>-->
<!--        var renkler = ['#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF'];-->
<!--        var sayac = 0;-->
<!--        function renkDegistir() {-->
<!--            document.getElementById('renkliYazi').style.color = renkler[sayac % renkler.length];-->
<!--            sayac++;-->
<!--        }-->
<!--        setInterval(renkDegistir, 2000); // 100 milisaniyede bir renk değiştir-->
<!--    </script>-->
<?php //} ?>

<!--    <tr><td colspan="2" background="../tepesus.png" height="80">&nbsp;</td></tr>-->
    <tr>
        <td style="background-color: #FFFFFF;" align="left">
            <input type="text" id="aranacak" onkeydown="EnterSubmit(event, '<?PHP echo $cryp; ?>')">&nbsp;
            <input type="submit" value="Ara" onclick="Ara('<?PHP echo $cryp; ?>');">
        </td>
        <td style="background-color: #EEEEEE;width:58px;text-align: right;">
            <a href="#" onclick="FileMaker.PerformScript('Gadget','Refresh');">Refresh</a>&nbsp;&nbsp;
        </td>
    </tr>



</table>
<div id="gelen"></div>
<?PHP
include '../_conn.php';
include '../_user.php';

$string="select substr(gadget,1,5) as g from VL_AYARLAR_USER_GADGETS  where \"on\"=1 AND user_user='" . $cryp . "' order by sira";
$stmt = $conn2->prepare($string);
$stmt->execute();
$gadgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

error_reporting(0);
for ($t=0;$t<count($gadgets);$t++) {
    include 'kt_' . $gadgets[$t]['g'] . ".php";
}
error_reporting(E_ERROR);
?>
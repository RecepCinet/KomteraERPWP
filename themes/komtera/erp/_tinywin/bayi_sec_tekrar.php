<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?PHP
$w = explode("\r", $_GET['w']);
$cs=$_GET['cs'];
$cc=$_GET['cc'];

$marka=$_GET['marka'];

?>
<html>
    <head>
<script>
function PaketGonder(url,dat) {
    var xmlhttp;
    if (window.XMLHttpRequest)
    {
        xmlhttp = new XMLHttpRequest();
    } else
    {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            var sonuc = xmlhttp.responseText;
            document.getElementById("gelen").innerHTML = sonuc;
            document.getElementById("ara").focus();
        }
    }
    var params = dat;
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);
}
</script>
<style>
    body,table {
    font-family: Arial;
    color: #444444;
    font-size: 14px;
    border-collapse: collapse;
}
input {
    font-size: 14px;
}
.tt tr {
    cursor: pointer;
}
tr {
    height: 28px;
}
tr.ss-button {
    background-color: #FFFFFF;
}
tr.ss-button:hover {
    background-color: #e0e0e0;
}
tr.ss-button:nth-of-type(odd):hover {
    background: #e0e0e0;
}
tr.ss-button:nth-of-type(odd) {
    background: #e6f4ff;
}
td {
    border-color: #a8c4dc;
}
</style>
        <script>
            function Sec(script, cmd, bayi) {

                var result = confirm(bayi + "\n <?php echo __('seçildi! Emin misiniz?', 'komtera'); ?>");

                if (result === true) {
                    FileMaker.PerformScript(script, atob(cmd));
                } else {
                    //
                }

            }
            function Yazilan(ilk) {
                var yazilan = document.getElementById("ara").value
                console.log(yazilan);
//                if (ilk=='ilk' || yazilan=="") {
                    PaketGonder("bayi_sec_data_tekrar.php", "marka=<?PHP echo $marka; ?>&cs=<?PHP echo $cs;?>&cc=<?PHP echo $cc;?>&data=" + yazilan);
                    //alert('dd');
//                } else {
//                    document.getElementById("gelen").innerHTML = "";
//                }
            }
            function FocusText() {
                Yazilan('ilk');
                document.getElementById("ara").focus();
            }
        </script>
    </head>
    <body style="margin:1;padding:1;" onload="FocusText();">
        <input id="ara" type="text" style="width: 100%;" onkeyup="Yazilan('');">
        <div id="gelen"></div>
    </body>
</html>
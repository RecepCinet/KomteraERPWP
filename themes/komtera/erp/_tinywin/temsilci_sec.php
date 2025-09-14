<?PHP
$dep=$_GET['dep'];
$cs=$_GET['cs'];
$cc=$_GET['cc'];

$marka=$_GET['marka'];
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?PHP
$w = explode("\r", $_GET['w']);
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
    color: #333333;
    font-size: 12px;
    border-collapse: collapse;
}
input {
    font-size: 15px;
}
.tt tr {
    cursor: pointer;
}
tr {
    height: 22px;
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
/*    background: #e6f4ff;*/
    background: #f7f7f7;
}
td {
/*    border-color: #a8c4dc;*/
    border-color: #d6d6d6;
}
</style>
        <script>
            function Sec(script, cmd, id) {
                FileMaker.PerformScript(script, cmd + '\r' + id);
            }
            function Yazilan(ilk) {
 //               if (event.keyCode === 13 || ilk=="ilk") {
                var yazilan = document.getElementById("ara").value
                console.log(yazilan);
//                if (ilk=='ilk' || yazilan=="") {
                    PaketGonder("temsilci_sec_data.php", "marka=<?PHP echo $marka;?>&cs=<?PHP echo $cs;?>&cc=<?PHP echo $cc;?>&dep=<?PHP echo $dep;?>&data=" + yazilan);
                    //alert('dd');
//                } else {
//                    document.getElementById("gelen").innerHTML = "";
//                }
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
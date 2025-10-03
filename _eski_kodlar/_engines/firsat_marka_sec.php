<?PHP
error_reporting(E_ALL);
ini_set("display_errors", true);
include '../_conn_fm.php';
$cryp = $_GET['k'];
$string = "select markalar from TF_USERS where kullanici='" . $cryp . "'";
//echo $string;
$stmt = $conn2->prepare($string);
$stmt->execute();
$ham = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['markalar'];

$arr = explode(chr(13), $ham);
?>
<html>
    <head>
        <style>
            body,table {
                font-family: Arial;
                font-size: 13px;
                border-collapse: collapse;
            }
            .tt tr {
                cursor: pointer;
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
                background: #f6f6f6;
            }
        </style>
        <script>
            function Sec(script, cmd, id) {
                FileMaker.PerformScriptWithOption(script, cmd + '\r' + id);
            }
        </script>
    </head>
    <body style="margin:0;padding:0">
        <table class="tt" width="100%" border="0">
            <?PHP
            sort($arr, SORT_STRING);
            for ($t = 0; $t < count($arr); $t++) {
                ?>
            <tr onclick='Sec("Firsat", "marka_secildi", "<?PHP echo $arr[$t]; ?>");' class='ss-button' style="height: 22px;">
                    <td width='100%'><?PHP echo $arr[$t]; ?></td>
                </tr>
                <?PHP
            }
            ?>
        </table>
    </body>
</html>

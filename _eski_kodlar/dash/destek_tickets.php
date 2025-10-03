<?php
//    error_reporting(E_ALL);
//    ini_set("display_errors", true);
    
function GET($key, $default = null) {
    $cikis = "";
    $cikis = isset($_GET[$key]) ? $_GET[$key] : $default;
    if ($cikis == "") {
        $cikis = isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    return $cikis;
}

if (GET('logout') == "1") {
    session_destroy();
    header("Location: http://172.16.80.214:8080/dash/");
}

$conn = odbc_connect('Logo64', 'crm', '!!!Crm!!!');
if (!$conn) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
    die("Connection Failed: " . $conn);
}

$sqlstring="select [CH_KODU] kod,[CH_UNVANI] unvan from aaa_erp_kt_bayiler order by unvan";
$sql= odbc_exec($conn, $sqlstring);

while ($rs = odbc_fetch_array($sql)) {
    if (strlen($rs['kod'])==14) { 
        $m=iconv("ISO-8859-9","UTF-8",$rs['unvan']);
        $m= str_replace(chr(10) , "", $m);
        $bayi_kod[]=$rs['kod'];
        $bayi_unv[]=$rs['unvan'];
    }
}
//var_dump($bayi_list);

$postdata = http_build_query(
    array(
        'keys' => 'jhsfuy8742987hsjkdh8742983472hhsjhfjsjkf8297981212376bjvnm cbmzb  hdk shhksjdhf8173981731789217891239817273987'
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);

$result = file_get_contents('https://destek.komtera.com/_utopia_ticket_report.php', false, $context);

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=AllTickets.xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);


for ($t=0;$t<count($bayi_kod);$t++) {
  
    $result= str_replace($bayi_kod[$t], $bayi_unv[$t], $result);
}

echo $result;

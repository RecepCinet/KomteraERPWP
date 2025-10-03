<?php
function customError($errno, $errstr) {
    echo "NOK $errno,$errstr";
    die();
}
set_error_handler("customError");

function GET($key, $default = null) {
    $cikis = "";
    $cikis = isset($_GET[$key]) ? $_GET[$key] : $default;
    if ($cikis == "") {
        $cikis = isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    return $cikis;
}

$kim=get('kim');
$modul=get('modul');
$xmo=get('no');
$yapilan=get('yapilan');

if ($kim=="" || $modul=="" || $xmo=="" || $yapilan=="") {
    die("-params");
}

$conn=mysqli_connect("127.0.0.1", "root", "KlyA2gw1", "erp");
$sqlinsert=sprintf("insert into erp_log (kim,modul,xno,yapilan) values ('%s','%s','%s','%s')",
        mysqli_escape_string($conn, $kim),
        mysqli_escape_string($conn, $modul),
        mysqli_escape_string($conn, $xmo),
        mysqli_escape_string($conn, $yapilan)
        );

$cevap= mysqli_query($conn, $sqlinsert);

echo $sqlinsert;

echo "OK";



?>

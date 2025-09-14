<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



$tablo=$_GET["tablo"];



error_reporting(E_ALL);
ini_set("display_errors", true);
ini_set('mssql.charset', 'UTF-8');

include '_conn.php';

    ?>
<?PHP
//}
include 'index_head.php';
include "_tablolar/$tablo.html";
?>
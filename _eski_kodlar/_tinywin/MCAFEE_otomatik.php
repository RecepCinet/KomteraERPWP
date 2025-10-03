<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

require('../XLSXReader.php');


$name="1020211206041903.xlsx";

$file='D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_teklifler_attach\KOTASYON_DOSYASI\\' . $name;

$xlsx = new XLSXReader($file);

print_r($xlsx);


?>

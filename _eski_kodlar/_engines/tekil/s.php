<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

$gel=file_get_contents("http://127.0.0.1/_engines/tekil_getir.php?cmd=teklif_urun_toplamlari&teklif_no=T304436");

echo $gel;


?>

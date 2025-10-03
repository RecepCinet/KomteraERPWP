<?php

function latin($param) {
    $param = str_replace("ı", "i", $param);
    $param = str_replace("ğ", "g", $param);
    $param = str_replace("ü", "u", $param);
    $param = str_replace("ö", "o", $param);
    $param = str_replace("ç", "c", $param);
    $param = str_replace("ş", "s", $param);
    $param = str_replace("İ", "I", $param);
    $param = str_replace("Ğ", "G", $param);
    $param = str_replace("Ü", "U", $param);
    $param = str_replace("Ö", "O", $param);
    $param = str_replace("Ç", "C", $param);
    $param = str_replace("Ş", "S", $param);

    return $param;
}

function dosya_adi(){
    date_default_timezone_set("Europe/Istanbul");
    $z=date("md_hi");
    return $z;
}


?>

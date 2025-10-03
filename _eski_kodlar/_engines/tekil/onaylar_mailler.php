<?php

$vade=$_GET['vade'];
$onay1=$_GET['onay1'];
$onay2=$_GET['onay2'];
$teklif_no=$_GET['teklif_no'];

if ($onay1=="1" && $onay2=="") {

    $uu = "http://127.0.0.1/_engines/onay_mail.php?tip=onaya&b=1&a=1&teklif_no=" . $teklif_no;   
    $mail_cevap = file_get_contents($uu);
    echo $mail_cevap;

}

if ($onay2=="1") {

    $uu = "http://127.0.0.1/_engines/onay_mail.php?tip=onaya&kime=2&b=1&a=1&teklif_no=" . $teklif_no;
    $mail_cevap = file_get_contents($uu);
    echo $mail_cevap;

}


?>

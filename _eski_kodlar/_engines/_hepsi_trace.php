<?php

$teklif_no="T436700";
$firsat_no="F220473";

$d=file_get_contents("http://172.16.84.214/_engines/tekil_getir.php?cmd=kar_oranlari&firsat_no=" . $firsat_no);
print_r($d);

$d=file_get_contents("http://172.16.84.214/_engines/tekil_getir.php?cmd=kara_liste&ch_kodu=" . $ch_kodu);
print_r($d);

$d=file_get_contents("http://172.16.84.214/_engines/tekil_getir.php?cmd=ozel_fiyat&teklif_no=" . $teklif_no);
print_r($d);

$d=file_get_contents("http://172.16.84.214/_engines/vade_kontrolu?acmd=eminmi&tteklif_no=" . $teklif_no);
print_r($d);

$d=file_get_contents("http://172.16.84.214/_engines/vade_kontrolu?acmd=eminmi&tteklif_no=" . $teklif_no);
print_r($d);

$d=file_get_contents("http://172.16.84.214/_engines/vade_kontrolu?acmd=eminmi&tteklif_no=" . $teklif_no);
print_r($d);


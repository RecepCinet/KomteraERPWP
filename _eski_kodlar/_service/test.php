<?php
$kredi_kontrol=file_get_contents("http://172.16.84.214/_service/rapor_kuru_toplam.php?teklif_no=T991903");
if ($kredi_kontrol==1) {
        $ek1=",SIPARIS_DURUM";
        $ek2=",'-1'";
        $ek3=",SIPARIS_DURUM_ALT";
        $ek4=",'31'";
}
echo $kredi_kontrol;
?>

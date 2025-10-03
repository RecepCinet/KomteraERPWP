<?php

error_reporting(0);
ini_set("display_errors", false);

$teklif_no=$_GET['teklif_no'];

$stmt = $conn->prepare("select X_FIRSAT_NO from aa_erp_kt_teklifler f where f.TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$firsat_no=$gelen['X_FIRSAT_NO'];

//print_r($gelen);

$json= file_get_contents("http://127.0.0.1/_engines/tekil_getir.php?cmd=kar_oranlari&firsat_no=$firsat_no");
$gelen= json_decode($json,true);

print_r($gelen);

$onay1_kim=$gelen['onay1_mail'];
$onay2_kim=$gelen['onay2_mail'];

echo "onay1 kim: " . $onay1_kim ;
echo "onay2 kim: " . $onay2_kim ;

$acmd=$_GET['acmd'];

if ($acmd === "kilitle" || $acmd === "kilitle2") {
    $stmt = $conn->prepare("select (select MUSTERI_TEMSILCISI from aa_erp_kt_firsatlar f where f.FIRSAT_NO=t.X_FIRSAT_NO) as MT from aa_erp_kt_teklifler t where t.TEKLIF_NO=:teklif_no");
    $stmt->execute(['teklif_no' => $teklif_no]);
    $mt = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['MT'];
    if ($tteki <= $on1) {
        if ($tteki < $on2) {
            $stmt = $conn->prepare("update aa_erp_kt_teklifler set KILIT=1,ONAY1='0',ONAY1_KIM=:onay1_kim,ONAY2='0',ONAY2_KIM=:onay2_kim where TEKLIF_NO=:teklif_no");
            $stmt->execute(['onay1_kim' => $onay1_kim, 'onay2_kim' => $onay2_kim, 'teklif_no' => $teklif_no,]);
            echo "ONAY2\n";
        } else {
            $stmt = $conn->prepare("update aa_erp_kt_teklifler set KILIT=1,ONAY1='0',ONAY1_KIM=:onay1_kim where TEKLIF_NO=:teklif_no");
            $stmt->execute(['onay1_kim' => $onay1_kim, 'teklif_no' => $teklif_no,]);
            echo "ONAY1\n";
        }
        //Birinciye Mail Gonder:
        $ek = "";
        if ($acmd === "kilitle2") {
            $ek = 'kime=2';
            $info2 = file_get_contents("http://127.0.0.1/kt_is_atama.php?modul=Teklif&mid=$teklif_no&kimden=$mt&kime=$onay2_kim&beklenen=" . urlencode("Kar onayı verilmesi"));
        } else {
            $info1 = file_get_contents("http://127.0.0.1/kt_is_atama.php?modul=Teklif&mid=$teklif_no&kimden=$mt&kime=$onay1_kim&beklenen=" . urlencode("Kar onayı verilmesi"));
        }
        $uu = "http://127.0.0.1/_engines/onay_mail.php?tip=onaya&b=1&$ek&a=1&teklif_no=" . $teklif_no;
        echo $uu;
        $mail_cevap = file_get_contents($uu);
        echo $uu;
    } else {
        echo "OK";
    }
}
?>

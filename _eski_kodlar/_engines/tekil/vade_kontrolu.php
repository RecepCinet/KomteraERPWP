<?php

error_reporting(0);
ini_set("display_errors", false);
 include '../_conn_fm.php';

$teklif_no = $_GET['teklif_no'];
$acmd = $_GET['acmd'];

$stmt = $conn->prepare("select t.VADE,(select MUSTERI_TEMSILCISI from aa_erp_kt_firsatlar f where FIRSAT_NO=t.X_FIRSAT_NO) AS MT,
(select b.VADE from aaa_erp_kt_bayiler b WHERE b.CH_KODU=(select BAYI_CHKODU from aa_erp_kt_firsatlar f where FIRSAT_NO=t.X_FIRSAT_NO)) as BVADE
from aa_erp_kt_teklifler t WHERE t.TEKLIF_NO=:teklif_no
");
$stmt->execute(['teklif_no' => $teklif_no]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$vade=$data['VADE'];
$bvade=$data['BVADE'];
$mt=$data['MT'];

//print_r($data);

##ONAY MAILI
function OnayYaz($conn,$conn2,$teklif_no) {
    global $mt;
    
    //EMaili ogreniyoruz:
    $stmt = $conn->prepare("select kim from aa_erp_kt_ayarlar_onaylar o where kural='BayiVadeDegisimi'");
    $stmt->execute();
    $kim = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['kim'];
       
        $string = "select ePosta from TF_USERS where kullanici='" . $kim . "'";
        //echo $string;
        $stmt = $conn2->prepare($string);
        $stmt->execute();
        $ePosta = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['ePosta'];
        //Teklif Veritabanina ONAY fieldlarini dolduruyoruz:
        $yazstring = "update aa_erp_kt_teklifler set VADE_ONAY='0',VADE_ONAY_KIM='$kim' WHERE TEKLIF_NO='$teklif_no'";
        $stmt = $conn->prepare($yazstring);
        $stmt->execute();
        //EMAIL Gonder;
        
        //Dashboard'a yazdirma:
        $info2 = file_get_contents("http://127.0.0.1/kt_is_atama.php?modul=Teklif&mid=$teklif_no&kimden=$mt&kime=$kim&beklenen=" . urlencode("Vade onayı verilmesi"));
        
        //Mail
        $uu = "http://127.0.0.1/_engines/vade_mail.php?teklif_no=" . $teklif_no;
        $mail_cevap = file_get_contents($uu);
}

if ($vade==="PEŞİN" || $bvade==="KKART") {
    die ("OK");
}

$vvv=0;

if (($bvade==="PEŞİN" || $bvade==="KKART") && $bvade!=$vade) {
    if ($acmd==="eminmi") {
        die("NOK|Bu Bayi için 'Peşin'/'KKart' dışındaki herhangi bir seçenek olduğunda Onay istenecektir!");
    } else if ($acmd==="kilitle") {
        // Kilitle
        $vvv=1;
    }
}

if ((int)$bvade<(int)$vade) {
    if ($acmd==="eminmi") {
        die("NOK|Vade günü arttırıldığı için ONAY istenecektir!");
    } else if ($acmd==="kilitle") {
        // Kilitle
        $vvv=1;
    }
}

if ($vvv===1) {
    OnayYaz($conn,$conn2,$teklif_no);
}

die("OK");

?>

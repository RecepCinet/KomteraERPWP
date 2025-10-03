<?php
error_reporting(0);
ini_set("display-errors", false);

function userdan($mtt) {
    global $conn2;
    $string='select ePosta from TF_USERS where kullanici=\'' . $mtt . '\'';
    //echo $string;
    $stmt = $conn2->prepare($string);
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    return $user['ePosta'];
}

$teklif_no = $_GET['teklif_no'];
$tip= $_GET['tip'];
$kim= $_GET['kim'];

include '../_conn.php';
include '../_conn_fm.php';

$stmt = $conn->prepare("select f.MARKA,f.BAYI_ADI,f.MUSTERI_ADI,f.MUSTERI_TEMSILCISI,t.ONAY1_KIM,ONAY2_KIM,ONAY1_ACIKLAMA,ONAY2_ACIKLAMA,VADE_ONAY_KIM,VADE_ONAY_ACIKLAMA  from aa_erp_kt_teklifler t
INNER JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=f.FIRSAT_NO
where t.TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$gelen['MT_MAIL']=userdan($gelen['MUSTERI_TEMSILCISI']);

print_r($gelen);

$tip_yaz="";
$body="";

if ($tip==="1") {
    $tip_yaz="Tebrikler teklifiniz onaylandi.";
}
if ($tip==="-1") {
    $tip_yaz="Maalesef teklifiniz onaylanmadı.";
}

$kim_yaz="";
if ($kim==="1") {
    $kim_yaz=$gelen['ONAY1_KIM'];
}
if ($kim==="2") {
    $kim_yaz=$gelen['ONAY2_KIM'];
    if ($kim_yaz==="") {
        die();
    }
}
if ($kim==="3") {
    $kim_yaz=$gelen['VADE_ONAY_KIM'];
}

$aciklama="";
if ($kim==="1") {
    $aciklama=$gelen['ONAY1_ACIKLAMA'];
}
if ($kim==="2") {
    $aciklama=$gelen['ONAY2_ACIKLAMA'];
}
if ($kim==="3") {
    $aciklama=$gelen['VADE_ONAY_ACIKLAMA'];
}

$body .= "$tip_yaz ($kim_yaz: " . $aciklama . ")<br /><br />";
$body .= "<a href='http://172.16.84.214/_engines/ac.php?script=Teklif&param=Ac|$teklif_no'>ERP'de Teklifi Aç</a><br /><br />";

$body .= "ERP Team<br /><br />";

require '../PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'bilgi@komtera.com';                 // SMTP username
$mail->CharSet = 'UTF-8';
$mail->Password = '2F&g1D4-5!-ad7S!';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->setFrom("bilgi@komtera.com", $mt);

$mail->addAddress($gelen['MT_MAIL'], $gelen['MT_MAIL']);
//$mail->addAddress("recep@piksel.biz", "recep@piksel.biz");

$mail->isHTML(true);

$mail->Subject = 'Teklif Onayi icin Teklif No: ' . $teklif_no;

$mail->Body = $body;
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'MAIL-OK';
}
?>

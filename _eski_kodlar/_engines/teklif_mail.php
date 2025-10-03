<?php

error_reporting(0);
ini_set("display_errors", false);

$teklif_id = $_GET['teklif_id'];

include '../_conn.php';
include '../_conn_fm.php';

$sqlstring="select MARKA,MUSTERI_TEMSILCISI,BAYI_YETKILI_ISIM,BAYI_YETKILI_EPOSTA,CONCAT(SUBSTRING(BAYI_ADI,1,15),' (',SUBSTRING(MUSTERI_ADI,1,15),') ') as SUBB
from aa_erp_kt_firsatlar f
where FIRSAT_NO=(select X_FIRSAT_NO from aa_erp_kt_teklifler t where t.TEKLIF_NO=:teklif_no)";

$stmt = $conn->prepare($sqlstring);
$stmt->execute(['teklif_no' => $teklif_id]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$marka=$gelen['MARKA'];

$mt=$gelen['MUSTERI_TEMSILCISI'];
$kime_isim=$gelen['BAYI_YETKILI_ISIM'];
$kime=$gelen['BAYI_YETKILI_EPOSTA'];
$subb=$gelen['SUBB'];

$stmt = $conn2->prepare("select adiSoyadi a,ePosta e from TF_USERS where kullanici='$mt'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$mt_mail=$gelen['e'];
$kisi=$gelen['a'];

$stmt = $conn2->prepare("select PDF_TEKLIF_ISMI from TF_teklifler_attach where TEKLIF_NO='$teklif_id'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$pdf=$gelen['PDF_TEKLIF_ISMI'];

$excel= str_replace(".PDF", ".XLSX", $pdf);

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

$mk=$_GET['mk'];
$mm=$_GET['mm'];
$cc=$_GET['cc'];
$mailnot=$_GET['mailnot'];
$mail->addAddress($mm, $mk);

//$mail->addAddress($kime, $kime_isim);    // Add a recipient -------------------------------------------- gitmiyor!
//$mail->addAddress("erptest@komtera.com","erptest@komtera.com");

$mail->addAddress($mt_mail, $mt);    // Add a recipient
$mail->addReplyTo($mt_mail, $mt);
if ($cc <> "") {
    $mail->addCC($cc,$cc);
}

//if ($marka=="MCAFEE" || $marka=="WATCHGUARD") {
//    $mail->addBCC("tunc.gokce@komtera.com");
//}

//$mail->addBCC('bcc@example.com');
if ($pdf <> "") {
    $mail->addAttachment('D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_teklifler_attach\PDF_TEKLIF\\' . $pdf);
}
if ($marka=='ECHOCTI') {
    $mail->addAttachment('D:\ek_pdfler\Komtera_Echo_CTI_Tech.pdf');
}
if ($excel <> "") {
    $mail->addAttachment("D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_teklifler_attach\XLS_TEKLIF\\" . $excel);
}

//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);

$mail->Subject = $subb . 'Teklif: ' . $teklif_id;
$mail->Body = "Sayin " . $mk . "<br /><br />Teklifimiz ekte bilginize sunulmuştur.<br /><br /><span style='font-size: larger;'><b><u>" . $mailnot . "</u></b></span><br /><br />İyi çalışmalar.<br /><b>" . $kisi . "</b><br /><br /><br /><br /><small>";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
$mail->Debug=3;
if (!$mail->send()) {
    echo 'NOK|HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    BotMesaj($teklif_id . "->" . $mail->ErrorInfo);
} else {
    echo $pdf . "\n";
    echo $excel . "\n";
    echo 'OK';
}

$amail=file_get_contents("http://127.0.0.1/_engines/mail_acc_manager.php?teklif_id=" . $teklif_id);

?>
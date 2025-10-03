<?php
error_reporting(E_ALL);
ini_set("display-errors", true);

$firsat_no = $_GET['firsat_no'];

include '../_conn.php';

include '../_conn_fm.php'; //select adiSoyadi a,ePosta e from TF_USERS where kullanici='recep.cinet'

error_reporting(E_ALL);
ini_set("display-errors", true);

$stmt = $conn->prepare("select MUSTERI_TEMSILCISI,BAYI_YETKILI_ISIM,BAYI_YETKILI_EPOSTA
from aa_erp_kt_firsatlar f
where FIRSAT_NO=:fn");
$stmt->execute(['fn' => $firsat_no]); 
$gelen = $stmt->fetch();

$mt=$gelen['MUSTERI_TEMSILCISI'];
$kime_isim=$gelen['BAYI_YETKILI_ISIM'];
$kime=$gelen['BAYI_YETKILI_EPOSTA'];

$stmt = $conn2->prepare("select adiSoyadi a,ePosta e from TF_USERS where kullanici='$mt'");
$stmt->execute();
$gelen2 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$mt_mail=$gelen2['e'];

$stmt = $conn2->prepare("select * from TF_firsatlar_attach where FIRSAT_NO='$firsat_no'");
$stmt->execute();
$gelen3 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$pdf=$gelen3['ALTERNATIFLI_PDF_ISIM'];
$tdata=$gelen3['TEKLIF_NOLARI'];
$tekliflers = explode("|",$tdata);

//$excel= str_replace(".PDF", ".XLSX", $pdf);

require '../PHPMailer/PHPMailerAutoload.php';
error_reporting(E_ALL);
ini_set("display-errors", true);

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

$mail->addAddress($mm, $mk);    // Add a recipient -------------------------------------------- gitmiyor!

//$mail->addAddress($kime, $kime_isim);    // Add a recipient -------------------------------------------- gitmiyor!
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
    $mail->addAttachment('D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_firsatlar_attach\ALTERNATIFLI_PDF\\' . $pdf);
}

for ($t=0;$t<count($tekliflers)-1;$t++) {
    $teklif_no=$tekliflers[$t];
    $stmt = $conn2->prepare("select XLS_ADI from TF_teklifler_attach where TEKLIF_NO='$teklif_no'");
    $stmt->execute();
    $gelen4 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['XLS_ADI'];
    $mail->addAttachment("D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_teklifler_attach\XLS_TEKLIF\\" . $gelen4);
}

//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

$mail->isHTML(true);

$mail->Subject = 'Teklif: ' . $tdata;
$mail->Body = "Merhaba " . $mk . "<br /><br />Teklifimiz ekte bilginize sunulmuştur.<br /><br /><span style='font-size: larger;'><b><u>" . $mailnot . "</u></b></span><br /><br />İyi çalışmalar.<br /><b>" . $mt . "</b><br /><br /><br /><br /><small>";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo $pdf . "\n";
    echo 'MAIL-OK';
}
?>
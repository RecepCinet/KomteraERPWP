<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
require '../PHPMailer/PHPMailerAutoload.php';
$firsat_no = $_GET['firsat_no'];
$teklif_no = $_GET['teklif_no'];
include '../_conn.php';
include '../_conn_fm.php'; //select adiSoyadi a,ePosta e from TF_USERS where kullanici='recep.cinet'
$stmt = $conn->prepare("select f.MUSTERI_TEMSILCISI,f.BAYI_YETKILI_EPOSTA,f.BAYI_YETKILI_ISIM,f.MUSTERI_ADI
from aa_erp_kt_firsatlar f
where FIRSAT_NO=:firsat_no
");
$stmt->execute(['firsat_no' => $firsat_no]);
$gelen = $stmt->fetch();
$mt=$gelen['MUSTERI_TEMSILCISI'];
$firma=$gelen['MUSTERI_ADI'];
$kime_isim=$gelen['BAYI_YETKILI_ISIM'];
$kime=$gelen['BAYI_YETKILI_EPOSTA'];
$stmt = $conn2->prepare("select adiSoyadi a,ePosta e from TF_USERS where kullanici='$mt'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$mt_eposta=$gelen['e'];
$stmt = $conn2->prepare("select LISANS_DOSYASI_ISMI,LISANS_DOSYASI_EK_NOT,lisans_gidecek_mail from TF_teklifler_attach where LISANS_DOSYASI_ISMI is not null AND TEKLIF_NO='$teklif_no'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//$ham=$gelen['LISANS_DOSYASI_ISMI'];
$eknot=$gelen[0]['LISANS_DOSYASI_EK_NOT'];
$kime=$gelen[0]['lisans_gidecek_mail'];
$mail = new PHPMailer;
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'bilgi@komtera.com';                 // SMTP username
$mail->CharSet = 'UTF-8';
$mail->Password = '2F&g1D4-5!-ad7S!';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);// TCP port to connect to
$mail->setFrom("bilgi@komtera.com", $mt);
$mail->addAddress($kime, $kime);    // Add a recipient
$mail->addAddress($mt_eposta, $mt);
$mail->addReplyTo($mt_eposta, $mt);
if ($cc<>"") {
    $mail->addCC($cc);
}
foreach ($gelen as $value) {
   $ham=$value['LISANS_DOSYASI_ISMI']; 
   $mail->addAttachment("d:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_teklifler_attach\LISANS_DOSYASI\\" . $ham);
}
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = $firma . ' Unvanli musteri urunu ile ilgili lisans';
$mail->Body    = "Merhaba " . $kime_isim . "<br /><br />$teklif_no numaralı teklifteki urunle ilgili lisans ekte bilginize sunulmuştur.<br /><br />" . $eknot . "<br /><br />İyi çalışmalar.<br /><b>" . $mt . "</b><br /><br /><br /><small>";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
if(!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}
?>

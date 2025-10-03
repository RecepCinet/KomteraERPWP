<?php
error_reporting(0);
ini_set('display_errors', false);
// http://172.16.80.214/fmi-test/SendMail2.php?mt=Recep Cinet&mt_eposta=recep@piksel.biz&kime=recepcinet@teknomart.com.tr
require '../../PHPMailer/PHPMailerAutoload.php';
$gonderi = $_GET['gonderi'];
$siparis = $_GET['siparis'];

//$stmt = $conn->prepare("select KARGO_GONDERI_NO from aa_erp_kt_siparisler s where s.SIPARIS_NO =:siparis_no ");
//$stmt->execute(['siparis_no' => $siparis]);
//$gonderi = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['KARGO_GONDERI_NO'];

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
$mail->setFrom("bilgi@komtera.com", "bilgi@komtera.com");
$mail->addAddress("mertcoban@ulusalantrepo.com.tr", "mertcoban@ulusalantrepo.com.tr");    // Add a recipient
//$mail->addAddress("yasindogan@ulusalantrepo.com.tr", "yasindogan@ulusalantrepo.com.tr");    // Add a recipient
$mail->addAddress("cagricakilcioglu@ulusalantrepo.com.tr", "cagricakilcioglu@ulusalantrepo.com.tr");    // Add a recipient

//
//$mail->addCC("recep@piksel.biz", "recep@piksel.biz");    // Add a recipient
$mail->addCC("order@komtera.com", "order@komtera.com");
//$mail->addCC("gokhan.ilgit@komtera.com", "gokhan.ilgit@komtera.com");
$mail->addCC("mustafa.sungur@komtera.com", "mustafa.sungur@komtera.com");
$mail->addCC("serhat.aslan@komtera.com", "mustafa.sungur@komtera.com");

//mail->addCC("");
$mail->addAttachment('D:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_siparisler_attach\KARGO_BARCODE\\' . $gonderi . ".png");
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional namesiparisler_attach/KARGO_BARCODE
//  d:\Data\Databases\RC_Data_FMS\Komtera2021\Themes\TF_siparisler_attach\KARGO_BARCODE\
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Siparis ' . $siparis;
$mail->Body = "Merhabalar,
<br /><br /> 
$siparis siparişi için Yurtiçi Kargo Barkod bilgisi ektedir. <br />
(Yurticikargo Gönderi No: $gonderi)
 <br /><br />
İyi çalışmalar dileriz,
 <br /><br />
Komtera Teknoloji A.Ş.<br /><br />";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo $pdf . "\n";
    echo $excel . "\n";
    echo 'Mail Gönderildi.';
}
?>

<?php
$pdf_adi = $_GET['pdf_adi'];
$fatura_no = $_GET['fatura_no'];

include '../_conn.php';

error_reporting(0);
ini_set("display_errors", false);

$ek=' Training';

$sqlstring="select s.belge_no, s.ilgili, s.cc, s.eposta, m.unvan, s.doviz_turu, s.odeme_sekli
from aa_erp_vk_siparisler s LEFT JOIN aaa_erp_vk_musteriler m ON s.cari_kod = m.cari_kod 
where s.belge_no =:fatura_no";

$stmt = $conn->prepare($sqlstring);
$stmt->execute(['fatura_no' => $fatura_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$unvan=$gelen['unvan'];
$mailisim=$gelen['ilgili'];
$doviz_turu=$gelen['doviz_turu'];
$odeme_sekli=$gelen['odeme_sekli'];
$ccler=$gelen['cc'];
$mailmail=$gelen['eposta'];
$bn=$gelen['belge_no'];

$ccler= str_replace("\n", "|", $ccler);
$ccler= str_replace(";", "|", $ccler);
$ccler= str_replace(",", "|", $ccler);

$cc= explode("|", $ccler);

//$sqlstring2="select kod from aa_erp_vk_siparisler_urunler su where su.x_siparis_belge_no =:fatura_no";
//$stmt2 = $conn->prepare($sqlstring2);
//$stmt2->execute(['fatura_no' => $fatura_no]);
//$gelen2 = $stmt2->fetchAll(PDO::FETCH_ASSOC)[0]['kod'];

if (substr($bn,0,2)=="TS") {
    $ek=$bn;
}



//$kime=$gelen['eposta'];

 //$kime="recep@piksel.biz";

require '../../_ERP2021/PHPMailer/PHPMailerAutoload.php';

if (2==1) {
    // Komtera Ayarlari
    $mail = new PHPMailer;
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'bilgi@komtera.com';                 // SMTP username
    $mail->CharSet = 'UTF-8';
    $mail->Password = '2F&g1D4-5!-ad7S!';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->setFrom("bilgi@komtera.com", "Veri Kurtarma");
} else {
    //VK Ayarlari
$mail=new PHPMailer();
$mail->IsSMTP(); 
$mail->Host       = "smtp.office365.com";
$mail->SMTPDebug  = 0;
$mail->SMTPSecure = "tls";
$mail->Username = "order@verikurtarma.com";
$mail->Password = '@celabVK2022';
$mail->SetFrom('order@verikurtarma.com', 'Verikurtarma Hizmetleri');
$mail->AddReplyTo('order@verikurtarma.com', 'Verikurtarma Hizmetleri');
$mail->CharSet = "utf-8";
$mail->IsHTML(true);
$mail->SMTPAuth = true;
$mail->port = 587;
$mail->Encoding = "base64";
}

//$mail->addAddress($mailmail, $mailisim); 
$mail->addAddress("namiksengoz@verikurtarma.com", "namiksengoz@verikurtarma.com"); 
//$mail->addBCC('order@verikurtarma.com');
//$mail->addBCC('service@acelab.eu.com');

$mail->addReplyTo("namiksengoz@verikurtarma.com", "Namik Sengoz");
foreach ($cc as $value) {
    $mail->addCC($value);
}

$temel="
You can find the proforma invoice for the service you ordered in the attachment. The commercial invoice will be issued and sent to you after your payment has been fully credited to our bank accounts.<br>
Details about wire tranfer are specified in the attached proforma.<br>
Please do not hesitate to contact us for your questions.";

$mail->addAttachment('D:\Data\Databases\RC_Data_FMS\VKF\Trus Medikal\t_pdf\pdf\\' . strtolower($pdf_adi));
if (trim($odeme_sekli)=="Card") {
    $temel="You can find the proforma invoice for the service you ordered in the attachment. The commercial invoice will be issued and sent to you after your payment has been fully credited to our bank accounts or via credit card.<br><br>
Attached you can find the payment form. <span style='color:red;text-decoration:underline;text-decoration-color:black;'>Please fill the form and the requested information on proforma invoice</span> and send us to finalize the payment procedure.<br><br>
Please do not hesitate to contact us for your questions.<br><br>
<span style='color:red;text-decoration:underline;text-decoration-color:black;'>In order to complete the payment process, your credit card must be authorized for international mail-order transactions.</span>";
    if ($doviz_turu=="USD") {
        $mail->addAttachment('D:\Data\vk_mail_order_form_eng__USD.pdf');
    } else {
        $mail->addAttachment('D:\Data\vk_mail_order_form_eng__EUR.pdf');
    }
}
$mail->AddEmbeddedImage('D:\Data\mail_imza.png', 'imza');

//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);
$mail->Subject = 'Proforma Invoice: ' . $ek;
$mail->Body = "<b>$unvan</b><br><br>Dear $mailisim<br><br>Thank you for your order.<br><br>" . $temel . "<br><br>Regards.<br><br>NAMIK K. SENGOZ<br>
<a href='mailto:namiksengoz@verikurtarma.com'>namiksengoz@verikurtarma.com</a><br><br><img src='cid:imza'>";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
$mail->Debug=3;
if (!$mail->send()) {
    echo 'NOK|HATA olustu!, mail gonderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    BotMesaj($teklif_id . "->" . $mail->ErrorInfo);
} else {
    echo 'OK';
}
?>

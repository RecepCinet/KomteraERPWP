<?php
error_reporting(E_ALL);
ini_set("display_errors",true);

$teklif_id = $_GET['teklif_id'];

include '../_conn.php';

$stmt = $conn->prepare("select *
from aa_erp_kt_firsatlar f
where FIRSAT_NO=(select X_FIRSAT_NO from aa_erp_kt_teklifler t where t.TEKLIF_NO=:teklif_no)");
$stmt->execute(['teklif_no' => $teklif_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

print_r($data);

$uu="select * from aa_erp_kt_markalar_managers where marka='" . $data['MARKA'] . "' and  yetkili='" . $data['MARKA_MANAGER'] . "'";
$stmt = $conn->prepare($uu);
$stmt->execute();
$data2 = $stmt->fetch(PDO::FETCH_ASSOC);

$istek = $data2['istek'];

print_r($data2);

if ($istek===null) {
    die();
}

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

$mail->setFrom("bilgi@komtera.com", $mt);

$mail->addAddress($data['MARKA_MANAGER_EPOSTA'], $data['MARKA_MANAGER']);
//$mail->addAddress("recep@piksel.biz", $data['MARKA_MANAGER']);

$mail->isHTML(true);

$out=<<<STRING
Merhaba #MARKA_MANAGER#

#MUSTERI_TEMSILCISI# tarafından Markanızla ilgili bir teklif gönderilmiştir;

Teklif No: $teklif_id
Bayi: #BAYI_ADI#
Bayi Yetkili: #BAYI_YETKILI_ISIM#
Müşteri: #MUSTERI_ADI#


STRING;

$out=str_replace("#MARKA_MANAGER#",$data['MARKA_MANAGER'],$out);
$out=str_replace("#MUSTERI_TEMSILCISI#",$data['MUSTERI_TEMSILCISI'],$out);
$out=str_replace("#BAYI_ADI#",$data['BAYI_ADI'],$out);
$out=str_replace("#BAYI_YETKILI_ISIM#",$data['BAYI_YETKILI_ISIM'],$out);
$out=str_replace("#MUSTERI_ADI#",$data['MUSTERI_ADI'],$out);

$out=str_replace("\n","<br />",$out);

$mail->Subject = 'Teklif: ' . $teklif_id;
$mail->Body = $out;
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

?>

<?php
error_reporting(0);
ini_set("display-errors", false);

function userdan($mtt) {
    global $conn2;
    $string='select ePosta from TF_USERS where kullanici=\'' . $mtt . '\'';
    $stmt = $conn2->prepare($string);
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    return $user['ePosta'];
}

$teklif_no = $_GET['teklif_no'];
        
include '../_conn.php';
include '../_conn_fm.php'; //select adiSoyadi a,ePosta e from TF_USERS where kullanici='recep.cinet'

$stmt = $conn->prepare("select f.MARKA,f.BAYI_ADI,f.MUSTERI_ADI,f.MUSTERI_TEMSILCISI,t.ONAY1_KIM,t.ONAY2_KIM from aa_erp_kt_teklifler t
INNER JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=f.FIRSAT_NO
where t.TEKLIF_NO=:teklif_no
");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$mt=$gelen['MUSTERI_TEMSILCISI'];
$mt_ep=userdan($mt);

$onay=$gelen['ONAY1_KIM'];
$ikimi=isset($_GET['kime']) ? $_GET['kime'] : "" ;

$onay_ep=userdan($onay);

echo $mt . " | " . $mt_ep;
echo $onay . " | " . $onay_ep;

$b = "";

$b .= "Marka: " . $gelen['MARKA'] . "<br />";
$b .= "Bayi Adı: " . $gelen['BAYI_ADI'] . "<br />";
$b .= "Müşteri Adı: " . $gelen['MUSTERI_ADI'] . "<br />";
$b .= "Müşteri Temsilcisi: " . $gelen['MUSTERI_TEMSILCISI'] . "<br />";

$toplamlar = file_get_contents("http://127.0.0.1/_engines/tekil_getir.php?cmd=teklif_urun_toplamlari&teklif_no=$teklif_no");
$arr = json_decode($toplamlar, true);

$t ="";

$t .= "Maliyet: " . number_format($arr['MALIYET'],2,",",".") . "<br />";
$t .= "Satış Toplamı: " . number_format($arr['SATIS_TOPLAMI'],2,",",".") . "<br />";
$t .= "Karlılık: " . number_format($arr['HKARLILIK'],2,",",".") . "<br />";

//die();

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

//$mail->addAddress($mt_ep, $mt);
$mail->addAddress($onay_ep, $onay);
//$mail->addAddress("recep@piksel.biz","recep@piksel.biz");

$mail->isHTML(true);

$mail->Subject = 'Vade Onay\'ı için teklif no:' . $teklif_no;

$body =$onceki_bilgi . "<br /><br />";

$body .= "Vade Onayı bekleyen teklif var.<br /><br />";
$body .= "Onay beklenen kişi: " . $onay . "<br /><br />";
$body .= "<a href='http://172.16.84.214/_engines/ac.php?script=Teklif&param=Ac|$teklif_no'>ERP'de Teklifi Aç</a><br /><br />";

$body .= $b . "<br />" . $t . "<br /><br />";

$body .= "ERP Team<br /><br />";
$mail->Body = $body;
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'MAIL-OK';
}
?>

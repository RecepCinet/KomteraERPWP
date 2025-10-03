<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require '../PHPMailer/PHPMailerAutoload.php';

$teklif_no=$_GET['teklif_no'];
$cmd=$_GET['cmd'];
$siparis_no=isset($_GET['siparis_no']) ? $_GET['siparis_no'] : $teklif_no . "-1";

echo "siparis_no: $siparis_no";

function userdan($mtt) {
    global $conn2;
    $string='select ePosta from TF_USERS where kullanici=\'' . $mtt . '\'';
    echo $string;
    $stmt = $conn2->prepare($string);
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
    return $user['ePosta'];
}
        
include '../_conn.php';
include '../_conn_fm.php'; //select adiSoyadi a,ePosta e from TF_USERS where kullanici='recep.cinet'

$stmt = $conn->prepare("select f.* from aa_erp_kt_teklifler t
INNER JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=f.FIRSAT_NO
where t.TEKLIF_NO=:teklif_no
");
$stmt->execute(['teklif_no' => $teklif_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
foreach ($gelen as $key => $value) {
    ${"F_".$key}=$value;
}

print_r($gelen);

$stmt = $conn->prepare("select s.* from aa_erp_kt_siparisler s where (s.DURUM=0 or s.DURUM=1) AND s.SIPARIS_NO=:siparis_no");
$stmt->execute(['siparis_no' => $siparis_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$var=0;
foreach ($gelen as $key => $value) {
	$var=1;
    ${"S_".$key}=$value;
}
if ($var==0) {
	die();
}

//echo "Burada0";
print_r($gelen);

$mail->addBCC("recep@piksel.biz","recep@piksel.biz");
$mail->addBCC("gokhan.ilgit@komtera.com","Gokhan Ilgit");

//echo "Burada1";

$temp_body="";
if ($cmd=="durum1") {

$temp_body = <<<STRING
Sayın $F_BAYI_ADI,
$F_BAYI_YETKILI_ISIM

$teklif_no Numaralı siparişiniz için teşekkür ederiz.
Müşteri: $F_MUSTERI_ADI

Siparişiniz işleme alınmıştır. Ürünleriniz en kısa sürede tarafınıza teslim edilecektir.

Saygılarımızla,
Komtera Teknoloji A.Ş.

STRING;
}
if ($cmd=="durum2") {
$temp_body = <<<STRING
Sayın $BAYI_ADI,
$F_BAYI_YETKILI_ISIM

$teklif_no teklif numaralı ürünleriniz kargoya teslim edilmiştir.

Kargo NO: $S_KARGO_GONDERI_NO
Teslimat Adresi: $F_SEVKIYAT_ADRES . $F_SEVKIYAT_ILCE . "/" . $F_SEVKIYAT_IL

Gönderinizin teslimat sürecini
$S_KARGO_URL
linkinden takip edebilirsiniz.

Gösterdiğiniz ilgi için teşekkür ederiz.

Saygılarımızla,
Komtera Teknoloji A.Ş.

STRING;
}
if ($cmd=="durum3") {
$temp_body = <<<STRING
Sayın $BAYI_ADI,
$F_BAYI_YETKILI_ISIM

$teklif_no teklif numaralı ürünleriniz  $S_KARGO_TESLIM_TARIHI tarihinde adresinize teslim edilmiştir.

Teslim Alan: $S_KARGO_TESLIM_KISI

$S_KARGO_URL

Saygılarımızla,
Komtera Teknoloji A.Ş.

STRING;
}

$temp_body= str_replace("\n", "<br />" ,$temp_body);

echo "Burada2";

$mail = new PHPMailer;
$mail->SMTPDebug = 1;
$mail->isSMTP();
$mail->Host = 'smtp.office365.com';
$mail->SMTPAuth = true;
$mail->Username = 'bilgi@komtera.com';
$mail->CharSet = 'UTF-8';
$mail->Password = '2F&g1D4-5!-ad7S!';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom("bilgi@komtera.com", $F_MUSTERI_TEMSILCISI);
$mail->addReplyTo(userdan($F_MUSTERI_TEMSILCISI), $F_MUSTERI_TEMSILCISI);
$mail->addAddress($F_BAYI_YETKILI_EPOSTA, $F_BAYI_YETKILI_ISIM);
$mail->addCC(userdan($F_MUSTERI_TEMSILCISI), $F_MUSTERI_TEMSILCISI);
$mail->addCC("order@komtera.com","order@komtera.com");
//$mail->addBCC("recep@piksel.biz");

$mail->isHTML(true);
$mail->Subject= $S_SIPARIS_NO . ' Numaralı Sipariş Hakkında';
$mail->Body= $temp_body . "<br /><br /><br /><small><small><small>";

if ($cmd=="durum1") {
    $mail->Subject= $teklif_no . ' Numaralı Sipariş Hakkında (' . $F_MUSTERI_ADI .')';
}
if ($cmd=="durum2") {
    $mail->Subject= $teklif_no . ' Numaralı Sipariş Kargoya verildi';
}
if ($cmd=="durum3") {
    $mail->Subject= $teklif_no . ' Numaralı Sipariş Teslim edildi';
}
$mail->AltBody= $temp_body;

if(!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}

?>

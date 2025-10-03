<?php
$durum = $_GET['durum'];
$id = $_GET['id'];
include '../_conn.php';
include '../PHPMailer/PHPMailerAutoload.php';

error_reporting(E_ALL);
ini_set("display_errors",true);

// -----------------------------------------------------------------------------  GENEL TICKET BILGILERI OKUNUYOR
$statement = $conn->prepare("Select * from aa_erp_kt_demolar where id=:id");
$statement->execute(array(':id' => $id));
$arr = $statement->fetchAll(PDO::FETCH_ASSOC);

print_r($arr);

$mail = new PHPMailer;
// $mail->SMTPDebug = 3;
$mail->isSMTP();
$mail->Host = 'smtp.office365.com';
$mail->SMTPAuth = true;
$mail->Username = 'bilgi@komtera.com';
$mail->CharSet = 'UTF-8';
$mail->Password = '2F&g1D4-5!-ad7S!';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom("bilgi@komtera.com", "bilgi@komtera.com");
//$mail->addAddress("yigit.arici@komtera.com", "Yigit Arici");
$mail->addAddress("mustafa.sungur@komtera.com", "Mustafa Sungur");
$mail->addAddress("serhat.aslan@komtera.com", "Serhat Aslan");
//$mail->addAddress("recep@piksel.biz", "Recep Cinet");

$temp_body="";

if ($durum=="5") {
//Kimlere Bilgilendirme Maili Gidecek?
    include '../_conn_fm.php';
    $stmt2 = $conn2->prepare("SELECT ePosta from TF_USERS where kt_yetki_demolar like '%DE-105%'");
    $stmt2->execute();
    $arr2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    for ($t=0;$t<count($arr2);$t++) {
        $epostatemp=$arr2[$t]['ePosta'];
        $mail->addAddress($epostatemp, $epostatemp);
    }

    $temp_body = <<<STRING
Dikkat Elden Teslim Demo seçildi!

Müşteri Temsilcisi: ###MT###

SKU: ###SKU###
ACIKLAMA: ###ACIKLAMA###

Seri Numara: ###SeriNumara###

Bayi: ###BAYI###
Musteri: ###MUSTERI###

ERP icinde demoyu görüntülemek için: ###ID###

KAI
STRING;
}

if ($durum=="1") {
//Kimlere Bilgilendirme Maili Gidecek?
    include '../_conn_fm.php';
    $stmt2 = $conn2->prepare("select ePosta from TF_USERS u where kt_yetki_demolar like 'DE-106 %'"); //Demo Kapatma Yetkisi
    $stmt2->execute();
    $arr2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    for ($t=0;$t<count($arr2);$t++) {
        $epostatemp=$arr2[$t]['ePosta'];
        $mail->addAddress($epostatemp, $epostatemp);
    }
    $temp_body = <<<STRING
<b>###YARATAN###</b> yeni bir Demo oluşturdu.

Müşteri Temsilcisi: ###MT###
        
SKU: ###SKU###
Seri Numara: ###SeriNumara###

Bayi: ###BAYI###
Musteri: ###MUSTERI###

ERP içinde demoyu açmak için: ###ID###

KAI
STRING;
}

if ($durum=="k") {
    $temp_body = <<<STRING
Sayın ###SAYIN###,
        
Talep etmiş olduğunuz Demo cihazınız kargoya verilmiştir.
        
Marka: ###Marka###
Model: ###Model###
Seri Numara: ###SeriNumara### 
        
Gönderi Kodu : ###GONDERIKODU###
Varis Şubesi : ###VARISSUBESI###
Teslimat Adresi	: ###TESLIMATADRESI### 

Gönderinizin teslimat sürecini "<a href="###URL###">gönderi takibi</a>" (Yurtiçikargo) linkinden takip edebilirsiniz.

Tüm sorularınız, önerileriniz ve destek talepleriniz için ilgili Satış Temsilciniz ile iletişime geçebilir ya da 0216 4165151 numaralı telefondan veya info@komtera.com adresinden bizlere ulaşabilirsiniz.

Not: Tarafınıza gönderilmiş olan Demo cihazın/cihazların aynı şekilde İrsaliye ile eksiksiz, hasarsız ve çalışır durumda tarafımıza teslim edilmesi gerekmektedir. Aksi taktirde ürün bedeli tarafınıza faturalanacaktır.

Saygılarımızla,
Komtera Teknoloji A.Ş.

STRING;
}

if ($durum=="2") {
    $temp_body = <<<STRING
Sayın ###SAYIN###,

Talep etmiş olduğunuz demo cihazınız tarafınıza teslim edilmiştir. Demo ürün ve teslim alan bilgileri aşağıdaki gibidir.        
        
Marka: ###Marka###
Seri Numara: ###SeriNumara### 

Teslim Alan: ###TESLIM_ALAN###
Teslim Tarihi ve Saati: ###TESLIMZAMANI###

Başarılı bir Demo süreci dileriz. 

Not: Tarafınıza gönderilmiş olan Demo cihazın/cihazların aynı şekilde irsaliye ile eksiksiz, hasarsız ve çalışır durumda tarafımıza teslim edilmesi gerekmektedir. Aksi taktirde ürün tarafınıza faturalanacaktır.

Saygılarımızla,
Komtera Teknoloji A.Ş.
STRING;
}

if ($durum=="8") {
    $temp_body = <<<STRING
Sayın ###SAYIN###,
        
Demo cihazı tarafımızdan teslim alınmıştır. 

Marka: ###Marka###
SKU: ###SKU###
Aciklama: ###ACIKLAMA###
Seri Numara: ###SeriNumara### 
        
Umarız Demo süreciniz başarılı geçmiştir. 
Gösterdiğiniz ilgi için teşekkür ederiz.

###NOT###
        
Saygılarımızla,
Komtera Teknoloji A.Ş.
STRING;
}

if ($durum=="4") {
    $temp_body = <<<STRING
Sayın ###SAYIN###,

Talep etmiş olduğunuz Demo cihazınız ofisimizden teslim alınmıştır.

Marka: ###Marka###
Seri Numara: ###SeriNumara### 

Teslim Alan: ###TESLIM_ALAN###
Teslim Tarihi ve Saati: ###TESLIMZAMANI###

Tüm sorularınız, önerileriniz ve destek talepleriniz için ilgili Satış Temsilciniz ile iletişime geçebilir ya da 0216 4165151 numaralı telefondan veya info@komtera.com adresinden bizlere ulaşabilirsiniz.

Not: Tarafınıza teslim edilmiş olan Demo cihazın/cihazların aynı şekilde İrsaliye ile eksiksiz, hasarsız ve çalışır durumda tarafımıza teslim edilmesi gerekmektedir. Aksi taktirde ürün bedeli tarafınıza faturalanacaktır.

Saygılarımızla,
Komtera Teknoloji A.Ş.
STRING;
}

if ($durum=="6") {
    $temp_body = <<<STRING
Sayın ###SAYIN###,

Talep etmiş olduğunuz Demo cihazınız ofisimizden teslim alınmıştır.

Marka: ###Marka###
Model: ###Model###
Seri Numara: ###SeriNumara### 

Teslim Alan: ###TESLIM_ALANE###
Teslim Tarihi ve Saati: ###TESLIMZAMANI2###

Tüm sorularınız, önerileriniz ve destek talepleriniz için ilgili Satış Temsilciniz ile iletişime geçebilir ya da 0216 4165151 numaralı telefondan veya info@komtera.com adresinden bizlere ulaşabilirsiniz.

Not: Tarafınıza teslim edilmiş olan Demo cihazın/cihazların aynı şekilde İrsaliye ile eksiksiz, hasarsız ve çalışır durumda tarafımıza teslim edilmesi gerekmektedir. Aksi taktirde ürün bedeli tarafınıza faturalanacaktır.

Saygılarımızla,
Komtera Teknoloji A.Ş.
STRING;
}

if ($durum=="3") {
die();
}

$temp_body= str_replace("###SKU###", $arr[0]['SKU'] ,$temp_body);
$temp_body= str_replace("###MT###", $arr[0]['MUSTERI_TEMSILCISI'] ,$temp_body);
$temp_body= str_replace("###ACIKLAMA###", $arr[0]['ACIKLAMA'] ,$temp_body);
$temp_body= str_replace("###SAYIN###", $_GET['kime_isim'] ,$temp_body);
$temp_body= str_replace("###Marka###", $arr[0]['MARKA'] ,$temp_body);
$temp_body= str_replace("###Model###", $arr[0]['MODEL'] ,$temp_body);
$temp_body= str_replace("###SKU###", $arr[0]['SKU'] ,$temp_body);
$temp_body= str_replace("###SeriNumara###", $arr[0]['SERIAL_NO'] ,$temp_body);
$temp_body= str_replace("###GONDERIKODU###", $arr[0]['KARGO_NO'] ,$temp_body);
$temp_body= str_replace("###VARISSUBESI###", $arr[0]['ILCE'] ,$temp_body);
$temp_body= str_replace("###TESLIMATADRESI###", $_GET['teslimatadresi'] ,$temp_body);
$temp_body= str_replace("###TESLIM_ALAN###", $_GET['teslimalan'] ,$temp_body);
$temp_body= str_replace("###BAYI###", $arr[0]['BAYI'] ,$temp_body);
$temp_body= str_replace("###MUSTERI###", $arr[0]['BAYININ_MUSTERISI'] ,$temp_body);
$temp_body= str_replace("###TESLIMZAMANI###", $_GET['teslimatzamanlama'] ,$temp_body);
$temp_body= str_replace("###URL###", $_GET['kargourl'] ,$temp_body);
$temp_body= str_replace("###TESLIM_ALANE###", $_GET['teslim_kisi'] ,$temp_body);
$temp_body= str_replace("###TESLIMZAMANI2###", date("d.m.Y") ,$temp_body);
$temp_body= str_replace("###YARATAN###", $arr[0]['CN'] ,$temp_body);
$temp_body= str_replace("\n", "<br />" ,$temp_body);
$urldegistir = '<a href="fmp://172.16.80.214/Komtera2021?script=Demo&param=Ac%0D' . "\n" . $arr[0]['id'] . '">Demo #' . $arr[0]['id'] . '</a>';
$temp_body= str_replace("###ID###", $urldegistir ,$temp_body);

$ab=$_GET['teslim_not'];

if ($ab=="A") {
    $temp_body= str_replace("###NOT###", "---Ürün eksiksiz teslim alındı.---" ,$temp_body);
}
if ($ab=="B") {
    $temp_body= str_replace("###NOT###", "---Ürün hasarlı/eksik teslim alındı, işlem yapılmalı. Müşteri Temsilciniz konu ile ilgili sizinle iletişime geçecektir.---" ,$temp_body);
}
$temp_body= str_replace("###NOT###", "" ,$temp_body);

error_reporting(E_ALL);
ini_set("display_errors",true);

$mail->isHTML(true);
$mail->Subject= 'Demo Ürün Hakkında';
$mail->Body= $temp_body . "<br /><br /><br /><small><small><small>";
$mail->AltBody= $temp_body;

if(!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}

?>

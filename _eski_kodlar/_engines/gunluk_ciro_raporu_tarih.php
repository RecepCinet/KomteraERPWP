<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
?>
<style>

    table {
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
    }

</style>
<?php
error_reporting(E_ERROR);
ini_set("display_errors", false);
$out='<style>

    table {
        border-collapse: collapse;
    }

    table, th, td {
        border: 1px solid black;
    }

</style>';
$out2=$out;

include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn.php';
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn75.php';
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn75_Ulke.php';
//include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn75ger.php';
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\PHPMailer\PHPMailerAutoload.php';



$sql_string=<<<SQL
select f.irsaliyeTarihi,liste.kod,
(select top 1 s.stokadi from crm_ulke_stoklar_sifirlarda s where s.stokkodu=liste.kod) urun,
liste.miktar,liste.birim,
f.cariKod , (select top 1 m.SIRKET_ADI  from crm_ulke_musteriler m where m.CH_KODU=f.cariKod)
from aa_ulke_crm_fatura_urunler liste
INNER JOIN aa_ulke_crm_fatura f ON liste.x_id = f.id
where cast(f.irsaliyeTarihi as date)=cast(getdate() as date)
and f.r_result='success'
SQL;

$stmt = $conn75ulke->prepare($sql_string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$out0 = "<table border='1'>";
if (!empty($data)) {
    $out0 .= "<tr>";
    foreach ($data[0] as $key => $value) {
        $out0 .= "<th>" . $key . "</th>";
    }
    $out0 .= "</tr>";
}
$prevSecondCol = null;
foreach ($data as $row) {
//    if ($prevSecondCol !== null && $prevSecondCol != array_values($row)[2]) {
//        $out0 .= "<tr><td colspan=\"" . count($row) . "\">&nbsp;</td></tr>";
//    }
    $out0 .= "<tr>";
    foreach ($row as $cell) {
        $out0 .= "<td>" . $cell . "</td>";
    }
    $out0 .= "</tr>";
    $prevSecondCol = array_values($row)[2];
}
$out0 .= "</table>";
echo $out0;

if ($data) {

    $mail3 = new PHPMailer;
    $mail3->SMTPDebug = 0;
    $mail3->isSMTP();
    $mail3->Host = 'smtp.office365.com';
    $mail3->SMTPAuth = true;
    $mail3->Username = 'teklif@lidyum.com';
    $mail3->CharSet = 'UTF-8';
    $mail3->Password = 'BVpzf668';
    $mail3->SMTPSecure = 'tls';
    $mail3->Port = 587;
    $mail3->setFrom("teklif@lidyum.com", "teklif@lidyum.com");
    //$mail2->addReplyTo();
    //$mail3->addAddress("gokhan.ilgit@komtera.com","Gokhan Ilgit");
    $mail3->addBCC("recep.cinet@komtera.com","Recep Cinet");
    $mail3->addBCC("sevkiyat@lidyum.com","sevkiyat@lidyum.com");
    $mail3->isHTML(true);
    $mail3->Subject= "Bugun Cikis Yapilan Irsaliye Bilgileri";
    $mail3->Body= $out0 . "<br /><br /><br /><small><small><small>";

    if(!$mail3->send()) {
        echo 'HATA oluştu!, mail gönderilemedi!';
        echo 'Mailer Error: ' . $mail3->ErrorInfo;
    } else {
        echo 'Mail Gönderildi.';
    }

} else {
    echo "kayit yok!";
}


// satinalma Irsaliye:
$sql_string='select * from TP_SATINALMA_IRSALIYE_ALL where cast(EklenmeTarihi as date) = cast(getdate() as date) order by EklenmeTarihi desc,Sirket';
$stmt = $conn75ulke->prepare($sql_string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$out0 = "<table border='1'>";
if (!empty($data)) {
    $out0 .= "<tr>";
    foreach ($data[0] as $key => $value) {
        $out0 .= "<th>" . $key . "</th>";
    }
    $out0 .= "</tr>";
}
$prevSecondCol = null;
foreach ($data as $row) {
//    if ($prevSecondCol !== null && $prevSecondCol != array_values($row)[2]) {
//        $out0 .= "<tr><td colspan=\"" . count($row) . "\">&nbsp;</td></tr>";
//    }
    $out0 .= "<tr>";
    foreach ($row as $cell) {
        $out0 .= "<td>" . $cell . "</td>";
    }
    $out0 .= "</tr>";
    $prevSecondCol = array_values($row)[2];
}
$out0 .= "</table>";
echo $out0;

if ($data) {

    $mail3 = new PHPMailer;
    $mail3->SMTPDebug = 0;
    $mail3->isSMTP();
    $mail3->Host = 'smtp.office365.com';
    $mail3->SMTPAuth = true;
    $mail3->Username = 'teklif@lidyum.com';
    $mail3->CharSet = 'UTF-8';
    $mail3->Password = 'BVpzf668';
    $mail3->SMTPSecure = 'tls';
    $mail3->Port = 587;
    $mail3->setFrom("teklif@lidyum.com", "teklif@lidyum.com");
    //$mail2->addReplyTo();
    //$mail3->addAddress("gokhan.ilgit@komtera.com","Gokhan Ilgit");
    $mail3->addBCC("recep.cinet@komtera.com","Recep Cinet");
    $mail3->addBCC("sevkiyat@lidyum.com","sevkiyat@lidyum.com");
    $mail3->isHTML(true);
    $mail3->Subject= "Bugun Girilen Irsaliye Bilgileri";
    $mail3->Body= $out0 . "<br /><br /><br /><small><small><small>";

    if(!$mail3->send()) {
        echo 'HATA oluştu!, mail gönderilemedi!';
        echo 'Mailer Error: ' . $mail3->ErrorInfo;
    } else {
        echo 'Mail Gönderildi.';
    }

} else {
    echo "kayit yok!";
}

$outgun='<th>
					<td width=170><b>Firma</b></td>
					<td width=175><b>Açıklama</b></td>
					<td width=80><b>TL</b></td>
					<td width=80><b>DOVIZ</b></td>
					</th>';

function array2Html($array, $table = true)
{
	$tableHeader = '<th>
					<td width=170><b>Firma</b></td>
					<td width=175><b>Açıklama</b></td>
					<td width=80><b>TL</b></td>
					<td width=80><b>DOVIZ</b></td>
					</th>';
    $out = '';
    foreach ($array as $key => $value) {
        $konum="left";
        if (is_array($value)) {
            array_keys($value);
            $out .= '<tr>';
            $out .= array2Html($value, false);
            $out .= '</tr>';
        } else {
            $yazval=$value;
            if ($key==="TL_TUTAR" || $key==="USD_TUTAR") {
                $konum="right";
                $yazval=number_format($value,"0", "" , ",");
            }
            $out .= "<td align='$konum'>".htmlspecialchars($yazval)."</td>";
        }	
    }
    if ($table) {
        return  '<table width=580 border="1" cellpadding="3">' . $tableHeader . $out . '</table>';
    } else {
        return $out;
    }
}

$firmalar=array('319');
foreach ($firmalar as $firma) {
$stmt = $conn->prepare("select '$firma' as FKOD,FIRMA,[AÇIKLAMA],TL_TUTAR,USD_TUTAR from ARY_CIRO_RAPOR_" . $firma);
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
$out .= array2Html($gelen);
$out .= "<br/><br/>";
}
// 818,018,819,029,'023'
$firmalar=array('120');
foreach ($firmalar as $firma) {
    $stmt = $conn75->prepare("select '$firma' as FKOD,FIRMA,[AÇIKLAMA],TL_TUTAR,USD_TUTAR from ARY_CIRO_RAPOR_" . $firma);
    $stmt->execute();
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out .= array2Html($gelen);
    $out .= "<br/><br/>";
}
$firmalar=array('524');
foreach ($firmalar as $firma) {
    $stmt = $conn75ulke->prepare("select '$firma' as FKOD,FIRMA,[AÇIKLAMA],TL_TUTAR,USD_TUTAR from ARY_CIRO_RAPOR_" . $firma);
    $stmt->execute();
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out .= array2Html($gelen);
    $out .= "<br/><br/>";
    $out2 .= array2Html($gelen);
    $out2 .= "<br/><br/>";
}

//$firmalar=array('218');
//foreach ($firmalar as $firma) {
//    $stmt = $conn->prepare("select '$firma' as FKOD,FIRMA,[AÇIKLAMA],TL_TUTAR,USD_TUTAR from ARY_CIRO_RAPOR_" . $firma);
//    $stmt->execute();
//    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    $out .= array2Html($gelen);
//    $out .= "<br/><br/>";
//}
//$firmalar=array('718','518','618');
//foreach ($firmalar as $firma) {
//    $stmt = $conn75->prepare("select '$firma' as FKOD,FIRMA,[AÇIKLAMA],TL_TUTAR,USD_TUTAR from ARY_CIRO_RAPOR_" . $firma);
//    $stmt->execute();
//    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    $out .= array2Html($gelen);
//    $out .= "<br/><br/>";
//}
echo $out;

echo "<br />---------------------------------------------------------<br /><br />";

echo $out2;

$mail = new PHPMailer;
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = 'smtp.office365.com';
$mail->SMTPAuth = true;
$mail->Username = 'bilgi@komtera.com';
$mail->CharSet = 'UTF-8';
$mail->Password = '2F&g1D4-5!-ad7S!';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom("bilgi@komtera.com", "bilgi@komtera.com");
//$mail->addReplyTo();
$mail->addAddress("gokhan.ilgit@komtera.com","Gokhan Ilgit");
$mail->addCC("cem.gunal@komtera.com","Cem Gunal");
$mail->addCC("cemgunal@gmail.com","Cem Gunal GMail");
$mail->addCC("gizem.tiftikci@komtera.com","Gizem Tiftikci");
$mail->addBCC("recep.cinet@komtera.com","Recep Cinet");
$mail->addCC("fatos.akkok@komtera.com","Fatos Akkok");
$mail->isHTML(true);
$mail->Subject= "Gunluk Ciro Raporu";
$mail->Body= $out . "<br /><br /><br /><small><small><small>";

if(!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}

$mail2 = new PHPMailer;
$mail2->SMTPDebug = 0;
$mail2->isSMTP();
$mail2->Host = 'smtp.office365.com';
$mail2->SMTPAuth = true;
$mail2->Username = 'teklif@lidyum.com';
$mail2->CharSet = 'UTF-8';
$mail2->Password = 'BVpzf668';
$mail2->SMTPSecure = 'tls';
$mail2->Port = 587;
$mail2->setFrom("teklif@lidyum.com", "teklif@lidyum.com");
//$mail2->addReplyTo();
$mail2->addAddress("nefi.huner@lidyum.com","Nefi Huner");
$mail2->addCC("gokay.gunyuz@lidyum.com","Gokay Gunduz");
$mail2->addBCC("recep.cinet@komtera.com","Recep Cinet");
$mail2->isHTML(true);
$mail2->Subject= "Lidyum Gunluk Ciro Raporu";
$mail2->Body= $out2 . "<br /><br /><br /><small><small><small>";

if(!$mail2->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail2->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}









sleep(3);

?>
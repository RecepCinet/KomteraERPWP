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
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\PHPMailer\PHPMailerAutoload.php';

$sql_string=<<<SQL
select
    DISTINCT
    t.TEKLIF_NO,
    s.CD as TARIH,
    f.BAYI_CHKODU,
    f.BAYI_ADI,
    f.PARA_BIRIMI,
    t.t_satis as TUTAR,
    t.TEKLIF_TIPI,
    t.KOMTERA_HIZMET_BEDELI
from aa_erp_kt_teklifler t
         LEFT JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=FIRSAT_NO
         LEFT JOIN aa_erp_kt_siparisler s ON s.X_TEKLIF_NO=t.TEKLIF_NO
WHERE t.KOMTERA_HIZMET_BEDELI>0
  AND f.DURUM=1
  AND MONTH(s.CD) = MONTH(DATEADD(MONTH, -1, GETDATE()))
  AND YEAR(s.CD) = YEAR(DATEADD(MONTH, -1, GETDATE()))
  AND DAY(GETDATE()) = 1
  AND t.TEKLIF_TIPI=1;
SQL;

$stmt = $conn->prepare($sql_string);
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
    //$mail->addAddress("gokhan.ilgit@komtera.com", "Gokhan Ilgit");
    $mail->addAddress("recep.cinet@komtera.com", "Recep Cinet");
    $mail->addAddress("ekrem.kolday@komtera.com", "Ekrem Kolday");
    $mail->addAddress("gursel.tursun@komtera.com", "Gursel Tursun");

    $mail->isHTML(true);
    $mail->Subject = "Aylik Komtera Hizmet Raporu (Siparis Tarihine Gore)";
    $mail->Body = $out0 . "<br /><br /><br /><small><small><small>";

    if (!$mail->send()) {
        echo 'HATA oluştu!, mail gönderilemedi!';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Mail Gönderildi.';
    }
}

sleep(1);

?>
<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn.php';
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\PHPMailer\PHPMailerAutoload.php';

function array2Html($array, $table = true)
{
    $out = '';
    foreach ($array as $key => $value) {
        $konum="left";
        if (is_array($value)) {
            if (!isset($tableHeader)) {
                $tableHeader =
                    '<th>' .
                    implode('</th><th>', array_keys($value)) .
                    '</th>';
            }
            array_keys($value);
            $out .= '<tr>';
            $out .= array2Html($value, false);
            $out .= '</tr>';
        } else {
            $yazval=$value;
            if ($key==="SiparisTutari" || $key==="Fon1" || $key==="Fon2" || $key==="FonToplam") {
                $konum="right";
                $yazval=number_format($value,"0", "" , ",");
            }
            $out .= "<td align='$konum'>".htmlspecialchars($yazval)."</td>";
        }
    }

    if ($table) {
        return '<table border="1" cellpadding="3">' . $tableHeader . $out . '</table>';
    } else {
        return $out;
    }
}

$stmt = $conn->prepare("
DECLARE @datecol datetime = GETDATE();
DECLARE @WeekNum INT
      , @YearNum char(4);
SELECT @WeekNum = (DATEPART(WK, @datecol))-1
     , @YearNum = CAST(DATEPART(YY, @datecol) AS CHAR(4));
DECLARE @BA datetime;
SELECT @BA=DATEADD(day, 1, DATEADD(wk, DATEDIFF(wk, 6, '1/1/' + @YearNum) + (@WeekNum-1), 6));
DECLARE @BI datetime;
SELECT @BI=DATEADD(day, -1, DATEADD(wk, DATEDIFF(wk, 5, '1/1/' + @YearNum) + (@WeekNum-1), 5));
select
t.id,
	(select TOP 1 FATURA_ISLEM_TARIHI FAT_NO
		FROM ARYD_FIS_AKTARIM ak
		where ak.[NO]=(select top 1 SIPARIS_NO from aa_erp_kt_siparisler s2 where t.TEKLIF_NO=s2.X_TEKLIF_NO)) as CD,
t.TEKLIF_NO,
f.BAYI_ADI,
f.PARA_BIRIMI,
(select sum(ADET*BIRIM_FIYAT) from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO like TEKLIF_NO+'-%') AS TOPTUT,
(ISNULL(KOMISYON_F1,0) + ISNULL(KOMISYON_F2,0) + ISNULL(KOMISYON_H,0)) AS KOMISYON_TOPLAM,
    (select TOP 1 trim(substring(MESAJ,20,18)) FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=(select top 1 SIPARIS_NO from aa_erp_kt_siparisler s3 where t.TEKLIF_NO=s3.X_TEKLIF_NO)) as FATURA_NO
from aa_erp_kt_teklifler t
LEFT JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=f.FIRSAT_NO
where (t.KOMISYON_F1>0 or t.KOMISYON_F2>0) AND (select top 1 SIPARIS_DURUM from aa_erp_kt_siparisler s4 where t.TEKLIF_NO=s4.X_TEKLIF_NO) = '2'
AND TEKLIF_NO IN (select TNO from aaaa_kapali_ve_tam_siparisler where PARCA=OLAN)
AND (select TOP 1 FATURA_ISLEM_TARIHI FAT_NO
		FROM ARYD_FIS_AKTARIM ak
		where ak.[NO]=(select top 1 SIPARIS_NO from aa_erp_kt_siparisler s2 where t.TEKLIF_NO=s2.X_TEKLIF_NO))>@BA 
		AND (select TOP 1 FATURA_ISLEM_TARIHI FAT_NO
		FROM ARYD_FIS_AKTARIM ak
		where ak.[NO]=(select top 1 SIPARIS_NO from aa_erp_kt_siparisler s2 where t.TEKLIF_NO=s2.X_TEKLIF_NO))<@BI
");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$out=array2Html($gelen);

$mail = new PHPMailer;
$mail->SMTPDebug = 3;
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
$mail->addAddress("gizem.tiftikci@komtera.com","gizem.tiftikci@komtera.com");
$mail->addAddress("mehmet.taskin@komtera.com","mehmet.taskin@komtera.com");
$mail->addCC("gokhan.ilgit@komtera.com","Gokhan Ilgit");
//$mail->addBCC("recep@piksel.biz");

$mail->isHTML(true);
$mail->Subject= "Haftalik Komisyon Raporu";
$mail->Body= $out . "<br /><br /><br /><small><small><small>";

$mail->AltBody= $temp_body;

if(!$mail->send()) {
    echo 'HATA oluştu!, mail gönderilemedi!';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Mail Gönderildi.';
}

?>

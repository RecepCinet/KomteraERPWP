<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

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
                $yazval=number_format($value,"0", "," , ".");
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
(select TOP 1 FATURA_ISLEM_TARIHI FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=s.SIPARIS_NO) as Tarih,
(select TOP 1 trim(substring(MESAJ,20,18)) FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=s.SIPARIS_NO) as FaturaNO,
f.BAYI_ADI as BayiAdi,
(select sum(ADET*BIRIM_FIYAT) from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO like TEKLIF_NO+'-%') AS SiparisTutari,
KOMISYON_F1 as Fon1,
KOMISYON_F2 as Fon2,
(ISNULL(KOMISYON_F1,0) + ISNULL(KOMISYON_F2,0) + ISNULL(KOMISYON_H,0)) AS FonToplam
from aa_erp_kt_teklifler t
LEFT JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO=f.FIRSAT_NO
LEFT JOIN aa_erp_kt_siparisler s ON t.TEKLIF_NO = s.X_TEKLIF_NO
where (t.KOMISYON_F1>0 or t.KOMISYON_F2>0) AND s.SIPARIS_DURUM = 2
and (select TOP 1 FATURA_ISLEM_TARIHI FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=s.SIPARIS_NO)>=@BA and (select TOP 1 FATURA_ISLEM_TARIHI FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=s.SIPARIS_NO)<=@BI
order by (select TOP 1 FATURA_ISLEM_TARIHI FAT_NO FROM ARYD_FIS_AKTARIM ak where ak.[NO]=s.SIPARIS_NO)
");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo array2Html($gelen);

?>

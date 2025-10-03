<?php

error_reporting(0);
ini_set('display_errors', false);

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';
function getCellStyle($align = 'left', $bold = false) {
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    );
    if ($bold) {
        $style['font'] = array('bold' => true);
    }
    return $style;
}

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();
include '__func.php';
include '../_conn.php';

$yil=$_GET['yil'];

try {
$sql=<<<DATA
select
    DISTINCT 
    sa.Tarih,
    f.MARKA,
    t.TEKLIF_NO,
    t.KOMTERA_HIZMET_ADI,
    t.KOMTERA_HIZMET_BEDELI,
    f.PARA_BIRIMI,
    (select sum(Usd_Tutar) as USDTUTAR from ERP_SATIS_ANALIZ_319_20XX sa where sa.TEKLIFNO=t.TEKLIF_NO group by sa.TEKLIFNO) as USDTUTAR,
    (select sum(TL_Tutar) as USDTUTAR from ERP_SATIS_ANALIZ_319_20XX sa where sa.TEKLIFNO=t.TEKLIF_NO group by sa.TEKLIFNO) as TL_Tutar,
    k.eur as EUR_KUR,
    k.usd as USD_KUR
from aa_erp_kt_teklifler t
         left join aa_erp_kt_firsatlar f ON f.FIRSAT_NO=t.X_FIRSAT_NO
         left join aa_erp_kur k ON k.tarih=(select top 1 CAST(Tarih AS DATE) from ERP_SATIS_ANALIZ_319_20XX sa where sa.TEKLIFNO=t.TEKLIF_NO)
         left join ERP_SATIS_ANALIZ_319_20XX sa on sa.TEKLIFNO=t.TEKLIF_NO 
         where TEKLIF_TIPI=1 AND KOMTERA_HIZMET_BEDELI>0
        and EXISTS(SELECT 1 FROM ERP_SATIS_ANALIZ_319_20XX WHERE TEKLIFNO = t.TEKLIF_NO)
and f.DURUM=1 and sa.Yil=$yil
DATA;

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $headers = [
        'Marka', 'Teklif No', 'Komtera Hizmet Adi', 'Komtera Hizmet Bedeli', 'Para Birim', 'Teklif USD Tutar', 'Teklif TL Tutar', 'EUR KUR', 'USD KUR'
    ];
    foreach ($headers as $col => $header) {
        $sheet->setCellValueByColumnAndRow($col, 1, $header);
    }
    $sheet->getStyle('A1:Z1')->applyFromArray(getCellStyle('center', true));

    $row = 2;
    foreach ($data as $record) {
        $col = 0;
        foreach ($record as $key => $value) {
            $sheet->setCellValueByColumnAndRow($col++, $row, $value);
        }
        $row++;
    }
    foreach (range('A', 'Z') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $filename = 'KomHizRaporu' . $yil . "_" . date("mdhi") . '.xlsx';
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');
    $conn = null;
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

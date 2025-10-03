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

function formatSalesType($type) {
    return $type == 0 ? 'ilk satis' : 'yenileme';
}

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();
include '__func.php';
include '../_conn.php';

try {
    $sql = "SELECT
    tu.TIP, tu.SATIS_TIPI, tu.SKU, tu.ACIKLAMA,
    f.MARKA_MANAGER, f.BAYI_YETKILI_ISIM, f.FIRSAT_NO, f.PARA_BIRIMI,
    tu.B_MALIYET*ADET as Maliyet,
    tu.B_SATIS_FIYATI*ADET as SatisTutari,
    CASE
        WHEN f.PARA_BIRIMI = 'USD' THEN (tu.ADET*tu.B_SATIS_FIYATI)
        WHEN f.PARA_BIRIMI = 'TRY' THEN (tu.ADET*tu.B_SATIS_FIYATI)/(select top 1 USD from aa_erp_kur k order by tarih desc)
        WHEN f.PARA_BIRIMI = 'EUR' THEN (tu.ADET*tu.B_SATIS_FIYATI) * ((select top 1 USD/EUR from aa_erp_kur k order by tarih desc))
        ELSE 0
        END AS DlrTutar,
    f.REGISTER, f.GELIS_KANALI, f.BASLANGIC_TARIHI, f.BITIS_TARIHI,
    MONTH(f.BITIS_TARIHI) as BITIS_AY, f.REVIZE_TARIHI, f.KAYIDI_ACAN,
    f.MUSTERI_TEMSILCISI, f.MARKA, f.ETKINLIK, f.BAYI_ADI, f.BAYI_YETKILI_ISIM,
    f.MUSTERI_ADI, f.OLASILIK, f.KAYBEDILME_NEDENI, f.KAYBEDILME_NEDENI_DIGER,
    f.PROJE_ADI, f.FIRSAT_ACIKLAMA
FROM
    aa_erp_kt_teklifler_urunler tu
        LEFT JOIN
    aa_erp_kt_teklifler t ON t.TEKLIF_NO = tu.X_TEKLIF_NO
        LEFT JOIN
    aa_erp_kt_firsatlar f ON f.FIRSAT_NO = t.X_FIRSAT_NO
WHERE
    f.DURUM = 0 AND f.SIL = 0 AND t.TEKLIF_TIPI = 1
";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $headers = [
        'Tip', 'Satis Tipi', 'SKU', 'Aciklama', 'Marka Manager', 'Bayi Yetkili Isim',
        'Firsat No', 'Para Birimi', 'Maliyet', 'SatisTutari', 'DlrTutar', 'Register', 'Gelis Kanali', 'Baslangic Tarihi', 'Bitis Tarihi',
        'Bitis Ayi', 'Revize Tarihi', 'Kayidi Acan', 'Musteri Temsilcisi', 'Marka',
        'Etkinlik', 'Bayi Adi', 'Bayi Yetkili Isim', 'Musteri Adi', 'Olasilik',
        'Kaybedilme Nedeni', 'Kaybedilme Nedeni Diger', 'Proje Adi', 'Firsat Aciklama'
    ];

    foreach ($headers as $col => $header) {
        $sheet->setCellValueByColumnAndRow($col, 1, $header);
    }
    $sheet->getStyle('A1:Z1')->applyFromArray(getCellStyle('center', true));

    $row = 2;
    foreach ($data as $record) {
        $col = 0;
        foreach ($record as $key => $value) {
            if ($key == 'SATIS_TIPI') {
                $value = formatSalesType($value);
            }
            $sheet->setCellValueByColumnAndRow($col++, $row, $value);
        }
        $row++;
    }

    foreach (range('A', 'Z') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $filename = 'AcikFirsatlarSatisTipi_' . date("Y-m-d") . '.xlsx';
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

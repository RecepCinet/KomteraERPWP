<?php

error_reporting(0);
ini_set('display_errors', false);

$firsat_no  = $_GET['firsat_no'];
$siparis_no = $_GET['siparis_no'];
$teklif_no = $_GET['teklif_no'];

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';

$ince = array(
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ),),
);
$kalin = array(
    'font' => array(
        'bold' => true,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'outline' => array(
            'style' => PHPExcel_Style_Border::BORDER_THICK,
            'color' => array('argb' => 'FF000000'),
        ),),
);
$sol = array('alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        ));
$sag = array('alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        ));
$noryaz = array(
    'font' => array(
        'bold' => false,
        ));
$boldyaz = array(
    'font' => array(
        'bold' => true,
        ));

// Mavi Renk: BDD7EE
function urun($x, $y, $quantity, $unit_price, $discount, $prod, $sku, $serial) {
    //echo $prod . "-" . $sku . "-" . $serial . "<br />";
    global $objPHPExcel;
    global $ince;
    global $kalin;
    global $sol;
    global $noryaz;
    global $sag;

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y, $x + 14, $y);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y, "Product Information");

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y + 1, $x + 2, $y + 1);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y + 1, "Product");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 3, $y + 1, $x + 14, $y + 1);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 3, $y + 1, $prod);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y + 2, $x + 2, $y + 2);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y + 2, "SKU");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 3, $y + 2, $x + 14, $y + 2);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 3, $y + 2, $sku);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y + 3, $x + 2, $y + 3);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y + 3, "Serial");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 3, $y + 3, $x + 14, $y + 3);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 3, $y + 3, $serial);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y + 4, $x + 2, $y + 4);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y + 4, "Quantity");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x, $y + 5, $x + 2, $y + 5);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 3, $y + 4, $x + 5, $y + 4);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 3, $y + 4, "Unit Price");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 3, $y + 5, $x + 5, $y + 5);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 6, $y + 4, $x + 8, $y + 4);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 6, $y + 4, "Sub-Total");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 6, $y + 5, $x + 8, $y + 5);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 9, $y + 4, $x + 11, $y + 4);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 9, $y + 4, "Discount");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 9, $y + 5, $x + 11, $y + 5);

    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 12, $y + 4, $x + 14, $y + 4);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 12, $y + 4, "Total");
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($x + 12, $y + 5, $x + 14, $y + 5);

    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x, $y + 4, $x + 14, $y + 4)->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFBDD7EE');

    //$st=number_format($quantity*$unit_price, 2, ',', '.');
    $st = $quantity * $unit_price;
    $cikis = (1 - ($discount / 100)) * ($st);
    $t = number_format(($st * ($discount * 100)) / 100, 2, ',', '.');

    //$tham=$st*(1-$discount)*($st);

    $discount_yaz = number_format($discount * 100, 2, ',', '.');

    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $y + 5, $quantity);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 3, $y + 5, "TL" . number_format($unit_price, 2, ',', '.'));
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 6, $y + 5, "TL" . (string) $st);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 9, $y + 5, "%" . $discount_yaz);
    $t = number_format(((1 - $discount) * 100 * ($quantity * $unit_price)) / 100, 2, ',', '.');
    $tham = ((1 - $discount) * 100 * ($quantity * $unit_price)) / 100;
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x + 12, $y + 5, "TL" . (string) $t);

    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x, $y, $x + 14, $y + 5)->applyFromArray($ince);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x, $y, $x + 14, $y + 5)->applyFromArray($kalin);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x, $y + 1, $x, $y + 3)->applyFromArray($sol);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x, $y + 5, $x + 14, $y + 5)->applyFromArray($noryaz);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x + 3, $y + 1, $x + 3, $y + 3)->applyFromArray($noryaz);
    $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($x + 3, $y + 1, $x + 3, $y + 3)->applyFromArray($sol);

    return $tham;
}

include '__func.php';
include '../_conn.php';

$objPHPExcel = PHPExcel_IOFactory::load("Sophos_PO.xlsx"); 
$objPHPExcel->setActiveSheetIndex(0);
$row = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;

$objPHPExcel->getActiveSheet()->SetCellValue('C3', 'Komtera' . date("d-m-Y"));

$stmt = $conn->prepare("select * from aa_erp_kt_firsatlar where FIRSAT_NO='$firsat_no'");
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$chkodu = "";

    $adrestemp = explode("\n", $rs['BAYI_ADRES']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C25', latin($adrestemp[0]));
    if (count($adrestemp) == 2) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C26', latin($adrestemp[1]));
    }
    if (count($adrestemp) == 3) {
        $objPHPExcel->getActiveSheet()->SetCellValue('C27', latin($adrestemp[2]));
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('C28', latin($rs['SEVKIYAT_IL']));

    $objPHPExcel->getActiveSheet()->SetCellValue('C24', latin($rs['MUSTERI_ADI']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C34', latin($rs['MUSTERI_YETKILI_TEL']));

    $objPHPExcel->getActiveSheet()->SetCellValue('C30', 'TURKIYE');
    $objPHPExcel->getActiveSheet()->SetCellValue('C32', latin($rs['MUSTERI_YETKILI_ISIM']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C33', latin($rs['MUSTERI_YETKILI_EPOSTA']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C45', latin($rs['BAYI_YETKILI_ISIM']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C46', latin($rs['BAYI_YETKILI_EPOSTA']));
    $chkodu = $rs['BAYI_CHKODU'];

    $objPHPExcel->getActiveSheet()->SetCellValue('C37', latin($rs['BAYI_ADI']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C38', latin($rs['BAYI_ADRES']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C39', latin($rs['BAYI_ADRES']));

    $objPHPExcel->getActiveSheet()->SetCellValue('C41', latin($rs['BAYI_SEHIR']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C43', latin($rs['BAYI_ILCE']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C47', latin($rs['BAYI_YETKILI_TEL']));

//$sqlstring = "select adet,maliyet,indirim,aciklama,sku,Lisans from T_20_SIP_URUN where \"teklif_no\"='$teklif_no' and sip_ek_no=$ek_no";
//$sql = odbc_exec($conn, $sqlstring);

$stmt = $conn->prepare("select su.SKU,tu.B_MALIYET,tu.O_MALIYET,tu.B_LISTE_FIYATI,su.ADET,su.ACIKLAMA,su.LISANS from aa_erp_kt_siparisler_urunler su LEFT JOIN aa_erp_kt_teklifler_urunler tu ON tu.SKU=su.SKU WHERE su.X_SIPARIS_NO = '$siparis_no'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$bas = -4;
$total = 0;

foreach ($gelen as $rs) {
    $bas = $bas + 7;
    $sku2 = $rs['SKU'];
    $maliyet = $rs['B_MALIYET'];
    $kotasyon = $rsa['O_MALIYET'];
    $maliyett = $rsa['B_LISTE_FIYATI'];
    $maliyet = $maliyett;
    $discc = 0.45;
    if ($kotasyon != "") {
        if ($maliyet == 0) {
            $discc = 0;
        } else {
            $discc = 1 - (($kotasyon * (int) $rs['ADET']) / ((int) $maliyet * $rs['ADET']));
        }
    }
    $cevap = urun(4, $bas, $rs['ADET'], $maliyet, $discc, $rs['ACIKLAMA'], $rs['SKU'], $rs['LISANS']);
    $total = $total + $cevap;
    //$temp_sku=$cevap;
}
$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(10, $bas + 4 + 3, 15, $bas + 4 + 3);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $bas + 4 + 3, "Sub-Total Amount");
$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10, $bas + 4 + 3)->applyFromArray($sag);
$objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow(16, $bas + 4 + 3, 18, $bas + 4 + 3);
$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(16, $bas + 4 + 3, 18, $bas + 4 + 3)->applyFromArray($ince);
$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10, $bas + 4 + 3)->applyFromArray($boldyaz);
$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(10, $bas + 4 + 3)->applyFromArray($boldyaz);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $bas + 4 + 3, "TL" . (string) number_format($total, 2, ',', '.'));
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $siparis_no . "_" . dosya_adi() . '.xls"');
header('Cache-Control: max-age=0');
$sheet = $objPHPExcel->getActiveSheet();
$objWriter->save('php://output');
?>

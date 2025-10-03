<?php

error_reporting(0);
ini_set('display_errors', false);

// PARAMETRELER
$firsat_no = $_GET['firsat_no'];
$siparis_no = $_GET['siparis_no'];
$teklif_no = $_GET['teklif_no'];

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';
include '../_conn.php';
include '__func.php';

$stmt = $conn->prepare("select * from aa_erp_kt_firsatlar where FIRSAT_NO='$firsat_no'");
$stmt->execute();
$f = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

// DEĞİŞKENLER — EXCEL’DE YERLEŞECEK ALANLAR
$partner_company_name = $f['BAYI_ADI'];
$partner_country = 'Turkey';
$partner_city = $f['BAYI_SEHIR'];
$partner_state = $f['BAYI_ILCE'];
$partner_postal_code = '-';
$partner_account_number = $f['BAYI_CH_KODU'];

$end_user_company_name = $f['MUSTERI_ADI'];
$end_user_country = 'Turkey';
$end_user_city = $f['SEVKIYAT_IL'];
$end_user_state = $f['SEVKIYAT_ILCE'];
$end_user_postal_code = '-';

$bill_company_name = $f['BAYI_ADI'];
$bill_address = $f['BAYI_ADRES'];
$bill_reference_person = $f['BAYI_YETKILI_ISIM'];
$bill_telephone = $f['BAYI_YETKILI_TEL'];
$bill_email = $f['BAYI_YETKILI_EPOSTA'];
$bill_license_key_email = '?';

function INDI($kod) {
    switch ($kod) {
        case "N": return .8;
        case "S": return .21;
        case "P": return .18;
        case "M": return .25;
        case "A": return .35;
        case "V": return .25;
        case "E": return .25;
        case "F": return .25;
        default:  return .25;
    }
}

function urun($aciklama, $sku, $bayi_fiyati, $adet, $sum_bayi_fiyati, $indirim) {
    global $objPHPExcel, $conn, $row;

    $stmt = $conn->prepare("select * from aa_erp_kt_fiyat_listesi where SKU='$sku'");
    $stmt->execute();
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

    $lf = $gelen['listeFiyati'];
    $tt = $lf * (int) $adet;
    $indirim_orani = INDI($gelen['wgCategory']);
    $iskonto = $tt * $indirim_orani;
    $komtera_cost = $tt - $iskonto;

    $objPHPExcel->getActiveSheet()->setCellValue("A$row", $aciklama);
    $objPHPExcel->getActiveSheet()->setCellValue("B$row", $sku);
    $objPHPExcel->getActiveSheet()->setCellValue("C$row", $lf);
    $objPHPExcel->getActiveSheet()->setCellValue("D$row", $adet);
    $objPHPExcel->getActiveSheet()->setCellValue("E$row", $tt);
    $objPHPExcel->getActiveSheet()->setCellValue("F$row", $indirim_orani); // Discount Rate
    $objPHPExcel->getActiveSheet()->setCellValue("G$row", ""); // Reseller Discount
    $objPHPExcel->getActiveSheet()->setCellValue("H$row", ""); // Special Bid Discount
    $objPHPExcel->getActiveSheet()->setCellValue("I$row", ""); // Promotions
    $objPHPExcel->getActiveSheet()->setCellValue("J$row", ""); // Special Bid OPP#
    $objPHPExcel->getActiveSheet()->setCellValue("K$row", floatval($komtera_cost));

    $row++;
    return $komtera_cost;
}

// EXCEL DOSYASINI YÜKLE
$objPHPExcel = PHPExcel_IOFactory::load("Watchguard_PO.xlsx"); // yeni şablon
$objPHPExcel->setActiveSheetIndex(0);

// SİPARİŞ BİLGİLERİ
$objPHPExcel->getActiveSheet()->SetCellValue('B5', "WG-" . date("YmdHis"));
$objPHPExcel->getActiveSheet()->SetCellValue('B6', date("d.m.Y"));

// PARTNER ALANLARI
$objPHPExcel->getActiveSheet()->SetCellValue('B8', $partner_company_name);
$objPHPExcel->getActiveSheet()->SetCellValue('B9', $partner_country);
$objPHPExcel->getActiveSheet()->SetCellValue('B10', $partner_city);
$objPHPExcel->getActiveSheet()->SetCellValue('B11', $partner_state);
$objPHPExcel->getActiveSheet()->SetCellValue('B12', $partner_postal_code);
$objPHPExcel->getActiveSheet()->SetCellValue('B13', $partner_account_number);

// END USER ALANLARI
$objPHPExcel->getActiveSheet()->SetCellValue('B14', $end_user_company_name);
$objPHPExcel->getActiveSheet()->SetCellValue('B15', $end_user_country);
$objPHPExcel->getActiveSheet()->SetCellValue('B16', $end_user_city);
$objPHPExcel->getActiveSheet()->SetCellValue('B17', $end_user_state);
$objPHPExcel->getActiveSheet()->SetCellValue('B18', $end_user_postal_code);

// BILL TO / SHIP TO ALANLARI
//$objPHPExcel->getActiveSheet()->SetCellValue('B21', $bill_company_name);
//$objPHPExcel->getActiveSheet()->SetCellValue('B22', $bill_address);
//$objPHPExcel->getActiveSheet()->SetCellValue('B23', $bill_reference_person);
//$objPHPExcel->getActiveSheet()->SetCellValue('B24', $bill_telephone);
//$objPHPExcel->getActiveSheet()->SetCellValue('B25', $bill_email);
//$objPHPExcel->getActiveSheet()->SetCellValue('B26', $bill_license_key_email);

// ÜRÜNLERİ ÇEK
$stmt = $conn->prepare("select * from aa_erp_kt_siparisler_urunler su WHERE X_SIPARIS_NO='$siparis_no'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ürün satırları 29’dan başlıyor
$row = 29;
$say = 0;
$topla=0.0;
foreach ($gelen as $satir) {
    if ($say > 0) {
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($row, 1);
    }
    $gelen=urun($satir['ACIKLAMA'], $satir['SKU'], $satir['BIRIM_FIYAT'], $satir['ADET'], $satir['BIRIM_FIYAT'], $satir['LISANS']);
    $topla += $gelen;
    $say++;
}

// TOPLAM YAZ
//$objPHPExcel->getActiveSheet()->setCellValue("K" . ($row + 1), $topla);

// DOSYAYI ÇIKAR
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $siparis_no . "_" . dosya_adi() . '.xlsx"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
exit;

?>

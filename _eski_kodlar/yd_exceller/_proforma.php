<?php

error_reporting(0);
ini_set('display_errors', false);

$firsat_no  = $_GET['firsat_no'];
$siparis_no = $_GET['siparis_no'];
$teklif_no = $_GET['teklif_no'];

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';


function urun($x,$y,$sku,$aciklama,$adet,$birim,$toplam) {
    global $objPHPExcel;
    $bayi_fiyati= str_replace(",", ".", $bayi_fiyati);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x , $y, $sku);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+1, $y, $aciklama);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, $adet);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+3, $y, (float)$birim);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+4, $y, (float)$birim*(float)$adet);
}

include '__func.php';
include '../_conn.php';

$objPHPExcel = PHPExcel_IOFactory::load("proforma.xlsx");
$objPHPExcel->setActiveSheetIndex(0);
$row = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;

$stmt = $conn->prepare("select * from aa_erp_kt_firsatlar where FIRSAT_NO='$firsat_no'");
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$stmt = $conn->prepare("select ADRES1,ADRES2,ILCE,SEHIR,VERGI_NO,VERGI_DAIRESI from aaa_erp_kt_bayiler b where b.CH_KODU = '" . $rs['BAYI_CHKODU'] . "'");
$stmt->execute();
$by = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];



   $chkodu="";     

	$objPHPExcel->getActiveSheet()->SetCellValue('F5', ("Tarih: " . date('d.m.Y')));
    
    $objPHPExcel->getActiveSheet()->SetCellValue('B14', ($rs['BAYI_ADI']));
	$objPHPExcel->getActiveSheet()->SetCellValue('B15', ($by['ADRES1']));
	$objPHPExcel->getActiveSheet()->SetCellValue('B16', ($by['ADRES2']));
	$objPHPExcel->getActiveSheet()->SetCellValue('B17', ($by['ILCE'] . "/" . $by['IL']));
	$objPHPExcel->getActiveSheet()->SetCellValue('B18', ($by['VERGI_DAIRESI'] . ", " . $by['VERGI_NO']));
	
	$objPHPExcel->getActiveSheet()->SetCellValue('F19', ($rs['PARA_BIRIMI']));
	$objPHPExcel->getActiveSheet()->SetCellValue('F60', ($rs['PARA_BIRIMI']));
	
    

//$sql = odbc_exec($conn, $sqlstring);
$stmt = $conn->prepare("select SKU,ACIKLAMA,ADET,BIRIM_FIYAT,ADET*BIRIM_FIYAT as TOPLAM from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO='$siparis_no'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($rs);
$bas=23;
$total=0;

foreach ($gelen as $rs) {
    $bas=$bas+1;
    $s1=$rs['SKU'];
    $s2=$rs['ACIKLAMA'];
    $s3=$rs['ADET'];
    $s4=$rs['BIRIM_FIYAT'];
    $s5=$rs['TOPLAM'];
    urun(1,$bas,$s1,$s2,$s3,$s4,$s5);
    //echo "|".$bas."|".$s1."|".$s2."|".$s3."|".$s4."|".$s5."|".$s6;
    //$total=$total+$cevap;
}

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save('yd_exceller/Sophos_PO_u.xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $siparis_no . "_" . dosya_adi() . '.xls"');
header('Cache-Control: max-age=0');
$sheet = $objPHPExcel->getActiveSheet();
$objWriter->save('php://output');

?>

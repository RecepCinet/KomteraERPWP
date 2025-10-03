<?php

error_reporting(0);
ini_set('display_errors', false);

$firsat_no  = $_GET['firsat_no'];
$siparis_no = $_GET['siparis_no'];
$teklif_no = $_GET['teklif_no'];

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';

// urun(1,$bas,$rs['sku'],$rs['tur'],$rs['adet'],$rs['bayi_fiyati'],$rs['sum_bayi_fiyati'],$rs['Lisans']);

function urun($x,$y,$sku,$tur,$adet,$bayi_fiyati,$sum_bayi_fiyati,$lisans) {
    global $objPHPExcel;
    $bayi_fiyati= str_replace(",", ".", $bayi_fiyati);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x , $y, $sku);
    //$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+2, $y, $yaz);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+4, $y, $adet);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+6, $y, (float)$bayi_fiyati);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+7, $y, (float)$bayi_fiyati*(int)$adet);
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x+9, $y, $lisans);
}

include '__func.php';
include '../_conn.php';

$objPHPExcel = PHPExcel_IOFactory::load("Trellix_PO.xlsx");
$objPHPExcel->setActiveSheetIndex(0);
$row = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;

//$sqlstring = "select * from T_10 where \"FirsatLabel\"='$firsat_no'";
////echo $sqlstring . "<br />";
//$sql = odbc_exec($conn, $sqlstring);

$stmt = $conn->prepare("select * from aa_erp_kt_firsatlar where FIRSAT_NO='$firsat_no'");
$stmt->execute();
$rs = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];


   $chkodu="";     

    
    $objPHPExcel->getActiveSheet()->SetCellValue('E9', latin($rs['BAYI_ADI']));
    $byet=explode(" ", latin($rs['BAYI_YETKILI_ISIM']));
    $objPHPExcel->getActiveSheet()->SetCellValue('E23', $byet[0]);
    $objPHPExcel->getActiveSheet()->SetCellValue('E24', $byet[1]);
    
    $objPHPExcel->getActiveSheet()->SetCellValue('E9', latin($rs['BAYI_ADI']));

    
    $adrestemp=explode ("\n" , $rs['BAYI_ADRES']);
        //$objPHPExcel->getActiveSheet()->SetCellValue('B10', latin($adrestemp[0]));
    if (count($adrestemp)==2) {
        $objPHPExcel->getActiveSheet()->SetCellValue('B11', latin($adrestemp[1]));
    }
     if (count($adrestemp)==3) {
        $objPHPExcel->getActiveSheet()->SetCellValue('B12', latin($adrestemp[2]));
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('B13', latin($rs['Musteri_Sehir']));
    
    $objPHPExcel->getActiveSheet()->SetCellValue('B9', latin($rs['MUSTERI_ADI']));
    $objPHPExcel->getActiveSheet()->SetCellValue('B26', latin($rs['MUSTERI_YETKILI_TEL']));
    
    $objPHPExcel->getActiveSheet()->SetCellValue('E16', 'TURKEY');
    $objPHPExcel->getActiveSheet()->SetCellValue('B16', 'TURKEY');
    
    $musteri= explode(" " , latin($rs['MUSTERI_YETKILI_ISIM']));

    $objPHPExcel->getActiveSheet()->SetCellValue('B23', isset($musteri[0]) ? $musteri[0] : "");
    $objPHPExcel->getActiveSheet()->SetCellValue('B24', isset($musteri[1]) ? $musteri[1] : "" );
    
    $objPHPExcel->getActiveSheet()->SetCellValue('B27', latin($rs['MUSTERI_YETKILI_EPOSTA']));
    
    $objPHPExcel->getActiveSheet()->SetCellValue('E27', latin($rs['BAYI_YETKILI_EPOSTA']));
    $chkodu = $rs['BAYI_CHKODU'];


    $objPHPExcel->getActiveSheet()->SetCellValue('E13', latin($rs['BAYI_SEHIR']));
    $objPHPExcel->getActiveSheet()->SetCellValue('E26', latin($rs['BAYI_YETKILI_TEL']));
    
    $objPHPExcel->getActiveSheet()->SetCellValue('E10', substr(latin($rs['BAYI_ADRES']),0,50) );
    $objPHPExcel->getActiveSheet()->SetCellValue('E11', substr(latin($rs['BAYI_ADRES']),50,50) );
// $objPHPExcel->getActiveSheet()->SetCellValue('E12', latin($rs['address3']));
////$sqlstring = "select * from T_20_SIP_URUN where \"teklif_no\"='$teklif_no' and sip_ek_no=$ek_no";
//$sqlstring = 'select su.maliyet,su.row_maliyet,su.sku as skk,su.adet,su.bayi_fiyati,su.sum_bayi_fiyati,su.Lisans,(select count_hardware from T_95_FIYAT_LISTESI where SKU=\'MV2ECE-AA-AA\' group BY count_hardware) as count_hardware 
//from T_20_SIP_URUN su
//where su."teklif_no"=\'' . $teklif_no . '\' and su.sip_ek_no=' . $ek_no . '
//'; //group by su.maliyet,su.row_maliyet,su.sku,f.count_hardware,su.adet,su.bayi_fiyati,su.sum_bayi_fiyati,su.Lisans
////echo $sqlstring;
//$sql = odbc_exec($conn, $sqlstring);
$stmt = $conn->prepare("select SKU,'1',ADET,
(select top 1 CASE WHEN B_MALIYET>0 THEN B_MALIYET ELSE O_MALIYET END from aa_erp_kt_teklifler_urunler tu where tu.SKU=su.SKU and tu.X_TEKLIF_NO='$teklif_no') as maliyet,
(select top 1 CASE WHEN B_MALIYET>0 THEN B_MALIYET*ADET ELSE O_MALIYET*ADET END from aa_erp_kt_teklifler_urunler tu where tu.SKU=su.SKU and tu.X_TEKLIF_NO='$teklif_no')*ADET as row_maliyet,
LISANS
from aa_erp_kt_siparisler_urunler su WHERE X_SIPARIS_NO='$siparis_no'");
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//print_r($rs);
$bas=32;
$total=0;

foreach ($gelen as $rs) {
    $bas=$bas+1;
    $s1=$rs['SKU'];
    $s2='1';
    $s3=$rs['ADET'];
    $s4=$rs['maliyet'];
    $s5=$rs['row_maliyet'];
    $s6=$rs['LISANS'];
    urun(0,$bas,$s1,$s2,$s3,$s4,$s5,$s6);
    //echo "|".$bas."|".$s1."|".$s2."|".$s3."|".$s4."|".$s5."|".$s6;
    //$total=$total+$cevap;
}

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save('yd_exceller/Sophos_PO_u.xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $siparis_no . "_" . dosya_adi() . '.xlsx"');
header('Cache-Control: max-age=0');
$sheet = $objPHPExcel->getActiveSheet();
$objWriter->save('php://output');
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';
// urun(1,$bas,$rs['sku'],$rs['tur'],$rs['adet'],$rs['bayi_fiyati'],$rs['sum_bayi_fiyati'],$rs['Lisans']);

include '__func.php';

$conn = odbc_connect('KomteraERP', 'Recep Cinet', 'KlyA2gw1');
if (!$conn) {
    die("ERP bağlantısında sorun var!");
}

$previous_week = strtotime("-1 week +1 day");
$start_week = strtotime("last monday midnight",$previous_week);
$end_week = strtotime("next friday",$start_week);

$yaz1 = date("Y-m-d",$start_week);
$yaz2 = date("Y-m-d",$start_week);

//$gun1 = date("d",$start_week);
//$ay1=date("m",$start_week);
//$gun2 = date("d",$end_week);
//$ay2=date("m",$end_week);

$sqlstring="SELECT s.logo_siparis_no_map as c0,s.cd AS c1,'sku' as c2,'qua' as c3,f.Bayi as c4,'TR' as c5,f.Bayi as c6,'TR' as c7,f.Musteri as c8,'' as c9,'' as c10,'' as c11
FROM T_20 s,T_10 f
where s.firsatNo = f.FirsatLabel
AND s.cd>=DATE'$yaz1' AND s.cd<=DATE'$yaz2' AND f.Marka_Kilidi='WATCHGUARD'
";

$sql=odbc_exec($conn,$sqlstring);



$objPHPExcel = PHPExcel_IOFactory::load("WGposhaftalikrapor.xlsx");
$objPHPExcel->setActiveSheetIndex(1);
$row = $objPHPExcel->getActiveSheet()->getHighestRow() + 1;



while ($rs=odbc_fetch_array($sql)) {
    $c0 = $rs['c0'];
    $c1 = $rs['c1'];
    $c2 = $rs['c2'];
    $c3 = $rs['c3'];
    $c4 = $rs['c4'];
    $c5 = $rs['c5'];
    $c6 = $rs['c6'];
    $c7 = $rs['c7'];
    $c8 = $rs['c8'];
    $c9 = $rs['c9'];
    $c10 = $rs['c10'];
    $c11 = $rs['c11'];

    $c0p=explode("-",$c0);
    // kac SKU var onu ogren sindide:
    $sqlstring2 = "select sku,adet from T_20_SIP_URUN
where T_20_SIP_URUN.teklif_no='" . $c0p[0] . "' and T_20_SIP_URUN.sip_ek_no=" . $c0p[1] . "
";
    $sql2 = odbc_exec($conn, $sqlstring2);

    while ($rs2 = odbc_fetch_array($sql2)) {
        $c2=$rs2['sku'];
        $c3=$rs2['adet'];
        $ne=6;
        $objPHPExcel->getActiveSheet()->insertNewRowBefore($ne, 1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $ne, $c1);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $ne, $c2);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $ne, $c3);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $ne, $c4);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $ne, $c5);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $ne, $c6);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $ne, $c7);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $ne, $c8);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $ne, $c9);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $ne, $c10);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $ne, $c11);

    }
}

//$objPHPExcel->getActiveSheet()->SetCellValue('C6', date("dd.mm.YY"));
//$objPHPExcel->getActiveSheet()->SetCellValue('C5', "WG-" . date("YY") . "-" );
//$sqlstring = "select * from T_50_BAYI where \"Ch Kodu\"='$chkodu'";
////echo $sqlstring . "<br />";
//$sql = odbc_exec($conn, $sqlstring);
//
//while ($rs = odbc_fetch_array($sql)) {
//    //$objPHPExcel->getActiveSheet()->SetCellValue('E13', latin($rs['sehir']));
//    //$objPHPExcel->getActiveSheet()->SetCellValue('E26', latin($rs['telefon']));
//    // $objPHPExcel->getActiveSheet()->SetCellValue('E12', latin($rs['address3']));
//}
//$sqlstring = "select * from T_20_SIP_URUN where \"teklif_no\"='$teklif_no' and sip_ek_no=$ek_no";

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//$objWriter->save('yd_exceller/Sophos_PO_u.xlsx');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . dosya_adi() . '.xls"');
header('Cache-Control: max-age=0');
$sheet = $objPHPExcel->getActiveSheet();
$objWriter->save('php://output');
?>

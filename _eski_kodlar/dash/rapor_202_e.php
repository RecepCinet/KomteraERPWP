<?php
error_reporting(0);
ini_set("display_errors",false);

session_start();

if ($_SESSION['enter'] != 1) {
    die("! Once dash ten login olun!");
}

$sqlstring=<<<DATA
select b.CH_UNVANI,b.SEHIR,b.ILCE ,y.yetkili,y.telefon,y.eposta  from aaa_erp_kt_bayiler b
left join aa_erp_kt_bayiler_yetkililer y
on b.CH_KODU = y.CH_KODU
DATA;

$serverName = "172.16.85.76";
try {
    $options = array(
        PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8,
        PDO::ATTR_TIMEOUT => 9000,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    $conn = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "!!!Crm!!!", $options);
} catch (Exception $e) {
    die("MS SQL Bağlantı Sorunu: " . $e->getMessage());
}

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';

// Yeni bir PHPExcel nesnesi oluştur
$objPHPExcel = new PHPExcel();

// Veritabanı sorgusu
$stmt = $conn->prepare($sqlstring);
if (!$stmt) {
    die("SQL sorgusu hazırlanamadı: " . print_r($conn->errorInfo(), true));
}

if (!$stmt->execute()) {
    die("SQL sorgusu çalıştırılamadı: " . print_r($stmt->errorInfo(), true));
}

// Sütun başlıkları için sıra
$column = 'A';
// Sütun başlıklarını yazdır
$firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($firstRow) {
    foreach ($firstRow as $columnName => $value) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column++ . '1', $columnName);
    }
}

$rowNumber = 2;
do {
    $column = 'A';
    foreach ($firstRow as $cell) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($column++ . $rowNumber, $cell);
    }
    $rowNumber++;
} while ($firstRow = $stmt->fetch(PDO::FETCH_ASSOC));

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>
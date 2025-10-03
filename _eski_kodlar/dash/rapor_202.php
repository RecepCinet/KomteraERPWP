<?php

error_reporting(0);
ini_set('display_errors', false);

require_once '../PHPExcel.php';
require_once '../PHPExcel/IOFactory.php';

// Function to define cell styles
function getCellStyle($align = 'left', $bold = false) {
    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
  );
  if ($bold) {
      $style['font'] = array('bold' => true);
  }
  return $style;
}

// Create a new Excel workbook
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// Database connection details (replace with your actual credentials)
include '__func.php';
include '../_conn.php';

try {
    // Define the query
    $sql = "SELECT b.CH_UNVANI, b.SEHIR, b.ILCE, y.yetkili, y.telefon, y.eposta FROM aaa_erp_kt_bayiler b LEFT JOIN aa_erp_kt_bayiler_yetkililer y ON b.CH_KODU = y.CH_KODU";

    // Execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch data as associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers (optional, adjust column names as needed)
    $sheet->setCellValueByColumnAndRow(0, 1, 'Company Name');
    $sheet->setCellValueByColumnAndRow(1, 1, 'City');
    $sheet->setCellValueByColumnAndRow(2, 1, 'District');
    $sheet->setCellValueByColumnAndRow(3, 1, 'Contact Name');
    $sheet->setCellValueByColumnAndRow(4, 1, 'Phone');
    $sheet->setCellValueByColumnAndRow(5, 1, 'Email');

    // Apply styles to headers (optional)
    $sheet->getStyle('A1:F1')->applyFromArray(getCellStyle('center', true));

    // Start data row from the second row
    $row = 2;

    // Write data to the sheet
    foreach ($data as $record) {
        $sheet->setCellValueByColumnAndRow(0, $row, $record['CH_UNVANI']);
        $sheet->setCellValueByColumnAndRow(1, $row, $record['SEHIR']);
        $sheet->setCellValueByColumnAndRow(2, $row, $record['ILCE']);
        $sheet->setCellValueByColumnAndRow(3, $row, $record['yetkili']);
        $sheet->setCellValueByColumnAndRow(4, $row, $record['telefon']);
        $sheet->setCellValueByColumnAndRow(5, $row, $record['eposta']);
        $row++;
    }

    // Auto-size columns (optional)
    foreach (range('A', 'F') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Set filename (replace with your desired filename)
    $filename = 'dealer_data_' . date("Y-m-d") . '.xlsx';

    // Create a new Excel Writer
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

    // Set headers for download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Save the Excel file
    $objWriter->save('php://output');

    // Close the database connection
    $conn = null;
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<?php
session_start();
include '../_conn.php';

// Simple Excel writer class using built-in functions
class SimpleExcelWriter {

    public static function writeXLSX($data, $headers, $filename) {

        // Create a temporary directory for XML files
        $tempDir = sys_get_temp_dir() . '/excel_' . uniqid();
        mkdir($tempDir);
        mkdir($tempDir . '/xl');
        mkdir($tempDir . '/xl/worksheets');
        mkdir($tempDir . '/_rels');
        mkdir($tempDir . '/xl/_rels');

        // Create [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>';
        file_put_contents($tempDir . '/[Content_Types].xml', $contentTypes);

        // Create _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        file_put_contents($tempDir . '/_rels/.rels', $rels);

        // Create xl/_rels/workbook.xml.rels
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>';
        file_put_contents($tempDir . '/xl/_rels/workbook.xml.rels', $workbookRels);

        // Create xl/workbook.xml
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>';
        file_put_contents($tempDir . '/xl/workbook.xml', $workbook);

        // Create worksheet data
        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>';

        // Add headers
        $worksheet .= '<row r="1">';
        foreach ($headers as $col => $header) {
            $cellRef = self::columnLetter($col + 1) . '1';
            $worksheet .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($header) . '</t></is></c>';
        }
        $worksheet .= '</row>';

        // Add data rows
        foreach ($data as $rowIndex => $row) {
            $rowNum = $rowIndex + 2;
            $worksheet .= '<row r="' . $rowNum . '">';

            foreach ($headers as $col => $header) {
                $cellRef = self::columnLetter($col + 1) . $rowNum;
                $value = isset($row[$header]) ? $row[$header] : '';

                // Check if value is numeric
                if (is_numeric($value)) {
                    $worksheet .= '<c r="' . $cellRef . '"><v>' . $value . '</v></c>';
                } else {
                    $worksheet .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . htmlspecialchars($value) . '</t></is></c>';
                }
            }
            $worksheet .= '</row>';
        }

        $worksheet .= '</sheetData></worksheet>';
        file_put_contents($tempDir . '/xl/worksheets/sheet1.xml', $worksheet);

        // Create ZIP file
        $zip = new ZipArchive();
        if ($zip->open($filename, ZipArchive::CREATE) === TRUE) {
            self::addDirectoryToZip($zip, $tempDir, '');
            $zip->close();

            // Clean up temp directory
            self::deleteDirectory($tempDir);

            return true;
        }

        // Clean up temp directory on failure
        self::deleteDirectory($tempDir);
        return false;
    }

    private static function columnLetter($columnNumber) {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)) . $letter;
            $columnNumber = intval($columnNumber / 26);
        }
        return $letter;
    }

    private static function addDirectoryToZip($zip, $dir, $zipPath) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $dir . '/' . $file;
                $zipFilePath = $zipPath . $file;

                if (is_dir($filePath)) {
                    $zip->addEmptyDir($zipFilePath);
                    self::addDirectoryToZip($zip, $filePath, $zipFilePath . '/');
                } else {
                    $zip->addFile($filePath, $zipFilePath);
                }
            }
        }
    }

    private static function deleteDirectory($dir) {
        if (!is_dir($dir)) return;

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $dir . '/' . $file;
                if (is_dir($filePath)) {
                    self::deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }
        rmdir($dir);
    }
}

// Handle export request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marka'])) {

    $marka = $_POST['marka'];

    if (empty($marka)) {
        echo json_encode(['success' => false, 'message' => 'Marka seçilmedi.']);
        exit;
    }

    // Define column order exactly as specified
    $columnOrder = [
        'sku',
        'urunAciklama',
        'marka',
        'tur',
        'lisansSuresi',
        'listeFiyati',
        'paraBirimi',
        'wgCategory',
        'wgUpcCode',
        'cozum',
        'listeFiyatiUpLift',
        'a_iskonto4',
        'a_iskonto3',
        'a_iskonto2',
        'a_iskonto1',
        's_iskonto4',
        's_iskonto3',
        's_iskonto2',
        's_iskonto1',
        'a_iskonto4_r',
        'a_iskonto3_r',
        'a_iskonto2_r',
        'a_iskonto1_r',
        's_iskonto4_r',
        's_iskonto3_r',
        's_iskonto2_r',
        's_iskonto1_r'
    ];

    // Use exact field names as headers for seamless import
    $headers = $columnOrder;

    try {
        // Use the same query structure as the original kt_fiyat_listesi.php
        $fields = implode(', ', $columnOrder);
        $sql = "SELECT $fields FROM aa_erp_kt_fiyat_listesi WHERE marka='$marka'";

        $stmt = $conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($data)) {
            echo json_encode(['success' => false, 'message' => 'Bu marka için veri bulunamadı.']);
            exit;
        }

        // Generate filename
        $filename = tempnam(sys_get_temp_dir(), 'excel_export_') . '.xlsx';
        $downloadFilename = $marka . '.xlsx';

        // Create Excel file
        if (SimpleExcelWriter::writeXLSX($data, $columnOrder, $filename)) {

            // Set headers for file download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
            header('Content-Length: ' . filesize($filename));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            // Output file and clean up
            readfile($filename);
            unlink($filename);
            exit;

        } else {
            echo json_encode(['success' => false, 'message' => 'Excel dosyası oluşturulamadı.']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
}
?>
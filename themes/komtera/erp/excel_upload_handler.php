<?php
session_start();
include '../_conn.php';

// Simple and reliable Excel reader using unzip command
class SimpleExcelReader {

    public static function readXLSX($filename) {
        $data = [];

        if (!file_exists($filename)) {
            error_log("EXCEL: File not found: $filename");
            return $data;
        }

        // Create temporary directory
        $tempDir = dirname($filename) . '/temp_excel_' . uniqid();
        if (!mkdir($tempDir)) {
            error_log("EXCEL: Cannot create temp directory: $tempDir");
            return $data;
        }

        try {
            // Extract Excel file using unzip command
            $unzipCommand = "unzip -q \"$filename\" -d \"$tempDir\"";
            exec($unzipCommand, $output, $returnCode);

            if ($returnCode !== 0) {
                error_log("EXCEL: unzip failed with code: $returnCode");
                self::cleanupTempDir($tempDir);
                return $data;
            }

            error_log("EXCEL: Successfully extracted Excel file");

            // Read shared strings if exists
            $sharedStrings = [];
            $sharedStringsPath = "$tempDir/xl/sharedStrings.xml";
            if (file_exists($sharedStringsPath)) {
                $sharedStringsXML = file_get_contents($sharedStringsPath);
                if (preg_match_all('/<t>([^<]*)<\/t>/s', $sharedStringsXML, $matches)) {
                    $sharedStrings = $matches[1];
                }
                error_log("EXCEL: Loaded " . count($sharedStrings) . " shared strings");
            }

            // Read worksheet XML
            $worksheetPath = "$tempDir/xl/worksheets/sheet1.xml";
            if (!file_exists($worksheetPath)) {
                error_log("EXCEL: sheet1.xml not found");
                self::cleanupTempDir($tempDir);
                return $data;
            }

            $xmlContent = file_get_contents($worksheetPath);

            // Parse rows using regex
            if (preg_match_all('/<row r="(\d+)"[^>]*>(.*?)<\/row>/s', $xmlContent, $rowMatches, PREG_SET_ORDER)) {
                error_log("EXCEL: Found " . count($rowMatches) . " rows");

                foreach ($rowMatches as $rowMatch) {
                    $rowNum = (int)$rowMatch[1];
                    $rowXML = $rowMatch[2];
                    $rowData = [];

                    // Parse cells in this row
                    if (preg_match_all('/<c r="([A-Z]+\d+)"[^>]*>(.*?)<\/c>/s', $rowXML, $cellMatches, PREG_SET_ORDER)) {
                        $cellsArray = [];

                        foreach ($cellMatches as $cellMatch) {
                            $cellRef = $cellMatch[1];
                            $cellXML = $cellMatch[2];
                            $cellValue = '';

                            // Check if it's a shared string reference
                            if (preg_match('/<v>(\d+)<\/v>/', $cellXML, $valueMatch)) {
                                $index = (int)$valueMatch[1];
                                if (strpos($cellMatch[0], 't="s"') !== false && isset($sharedStrings[$index])) {
                                    $cellValue = $sharedStrings[$index];
                                } else {
                                    $cellValue = $valueMatch[1];
                                }
                            }
                            // Check for inline string
                            elseif (preg_match('/<t>([^<]*)<\/t>/', $cellXML, $textMatch)) {
                                $cellValue = $textMatch[1];
                            }

                            $col = self::columnFromCellRef($cellRef);
                            $cellsArray[$col] = trim($cellValue);
                        }

                        // Build ordered row array
                        if (!empty($cellsArray)) {
                            $maxCol = max(array_keys($cellsArray));
                            for ($i = 0; $i <= $maxCol; $i++) {
                                $rowData[] = isset($cellsArray[$i]) ? $cellsArray[$i] : '';
                            }

                            // Only add non-empty rows
                            if (!empty(array_filter($rowData, function($cell) { return $cell !== ''; }))) {
                                $data[] = $rowData;
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            error_log("EXCEL: Exception: " . $e->getMessage());
        }

        // Clean up
        self::cleanupTempDir($tempDir);

        error_log("EXCEL: Read complete. Total rows: " . count($data));

        // Debug first few rows
        if (!empty($data)) {
            error_log("EXCEL: First row: " . json_encode($data[0]));
            if (count($data) > 1) {
                error_log("EXCEL: Second row: " . json_encode($data[1]));
            }
        }

        return $data;
    }

    private static function columnFromCellRef($cellRef) {
        $column = preg_replace('/[0-9]+/', '', $cellRef);
        $result = 0;
        $length = strlen($column);

        for ($i = 0; $i < $length; $i++) {
            $result = $result * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        return $result - 1;
    }

    private static function cleanupTempDir($tempDir) {
        if (is_dir($tempDir)) {
            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($tempDir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($tempDir);
            } catch (Exception $e) {
                error_log("EXCEL: Cleanup failed: " . $e->getMessage());
            }
        }
    }

    public static function readCSV($filename) {
        $data = [];
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($rowData = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $rowData;
            }
            fclose($handle);
        }
        return $data;
    }
}

// Simple header mapping function - direct field name matching
function createHeaderMapping($headers) {
    // Valid database fields
    $validFields = [
        'sku', 'urunAciklama', 'marka', 'tur', 'cozum', 'lisansSuresi',
        'wgCategory', 'wgUpcCode', 'paraBirimi', 'listeFiyati', 'listeFiyatiUpLift',
        'a_iskonto4', 'a_iskonto3', 'a_iskonto2', 'a_iskonto1',
        's_iskonto4', 's_iskonto3', 's_iskonto2', 's_iskonto1',
        'a_iskonto4_r', 'a_iskonto3_r', 'a_iskonto2_r', 'a_iskonto1_r',
        's_iskonto4_r', 's_iskonto3_r', 's_iskonto2_r', 's_iskonto1_r'
    ];

    $headerMapping = [];

    foreach ($headers as $index => $header) {
        if (in_array($header, $validFields)) {
            $headerMapping[$header] = $index;
        }
    }

    return $headerMapping;
}

// SKU analysis function
function analyzeSKUChanges($conn, $excelSKUs, $targetMarka) {
    $analysis = [
        'toDelete' => [],
        'toUpdate' => [],
        'toInsert' => [],
        'unchanged' => []
    ];

    // Get existing SKUs from database for the target marka
    $sql = "SELECT sku FROM aa_erp_kt_fiyat_listesi WHERE marka = :marka";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':marka', $targetMarka);
    $stmt->execute();
    $existingSKUs = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Find SKUs to delete (in DB but not in Excel)
    $analysis['toDelete'] = array_diff($existingSKUs, $excelSKUs);

    // Find SKUs to update (in both DB and Excel)
    $analysis['toUpdate'] = array_intersect($existingSKUs, $excelSKUs);

    // Find SKUs to insert (in Excel but not in DB)
    $analysis['toInsert'] = array_diff($excelSKUs, $existingSKUs);

    return $analysis;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {

    // Get target marka from POST data
    $targetMarka = isset($_POST['target_marka']) ? trim($_POST['target_marka']) : '';

    // Check if this is a preview request
    $isPreview = isset($_POST['preview']) && $_POST['preview'] === 'true';

    $uploadDir = '../uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['excel_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Extract marka from filename if possible
    function extractMarkaFromFilename($filename) {
        $fileBaseName = preg_replace('/\.(xlsx|xls|csv)$/i', '', $filename);
        if (preg_match('/^([A-Z][A-Z0-9_-]+)_/i', $fileBaseName, $matches)) {
            return strtoupper($matches[1]);
        }
        return '';
    }

    $fileMarka = extractMarkaFromFilename($fileName);

    // If no target marka is selected but we can extract it from filename, ask for confirmation
    if (empty($targetMarka) && !empty($fileMarka) && !$isPreview) {
        echo json_encode([
            'success' => false,
            'needConfirmation' => true,
            'message' => __('Marka Seçilmemiş Ancak Dosya Adından', 'komtera') . " '{$fileMarka}' " . __('Markası Algılandı. Bu Markaya Import Edilecek, Emin Misiniz?', 'komtera'),
            'suggestedMarka' => $fileMarka,
            'fileName' => $fileName
        ]);
        exit;
    }

    // If confirmation is received, use the suggested marka
    if (empty($targetMarka) && !empty($fileMarka)) {
        $targetMarka = $fileMarka;
    }

    // Check for upload errors
    if ($fileError !== 0) {
        echo json_encode(['success' => false, 'message' => __('Dosya Yükleme Hatası: ', 'komtera') . $fileError]);
        exit;
    }

    // Validate file size (max 10MB)
    if ($fileSize > 10 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => __('Dosya Boyutu Çok Büyük (Maks. 10MB)', 'komtera')]);
        exit;
    }

    // Validate file extension
    $allowedExtensions = ['xlsx', 'xls', 'csv'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => __('Geçersiz Dosya Türü. Sadece Excel (.xlsx, .xls) ve CSV Dosyaları Kabul Edilir', 'komtera')]);
        exit;
    }

    // Generate unique filename
    $uniqueFileName = uniqid() . '_' . $fileName;
    $uploadPath = $uploadDir . $uniqueFileName;

    // Move uploaded file
    if (move_uploaded_file($fileTmpName, $uploadPath)) {

        try {
            // Read the Excel/CSV file
            $data = [];

            error_log("EXCEL DEBUG: Processing file: $uploadPath, Extension: $fileExtension, Size: " . filesize($uploadPath));

            if ($fileExtension === 'csv') {
                $data = SimpleExcelReader::readCSV($uploadPath);
                error_log("EXCEL DEBUG: CSV read returned " . count($data) . " rows");
            } else if ($fileExtension === 'xlsx') {
                error_log("EXCEL DEBUG: About to call readXLSX");
                $data = SimpleExcelReader::readXLSX($uploadPath);
                error_log("EXCEL DEBUG: readXLSX returned " . count($data) . " rows");
            } else {
                // For .xls files, we'll need a different approach or suggest converting to .xlsx
                echo json_encode(['success' => false, 'message' => __('.xls Dosyaları Desteklenmemektedir. Lütfen .xlsx Formatında Kaydedin', 'komtera')]);
                unlink($uploadPath);
                exit;
            }

            // Process the data
            if (empty($data)) {
                echo json_encode(['success' => false, 'message' => __('Dosya Boş veya Okunamadı', 'komtera')]);
                unlink($uploadPath);
                exit;
            }

            // Assume first row contains headers
            $headers = array_shift($data);
            $errors = [];

            // DEBUG: Log headers to error log instead of stopping execution
            error_log("EXCEL HEADERS DEBUG: " . json_encode($headers));

            // DEBUG: Show what we read from first row
            if (empty($headers)) {
                echo json_encode([
                    'success' => false,
                    'message' => __('İlk Satır (Header) Boş Geldi', 'komtera'),
                    'debug' => [
                        'total_rows' => count($data),
                        'first_data_row' => isset($data[0]) ? $data[0] : 'yok'
                    ]
                ]);
                unlink($uploadPath);
                exit;
            }

            // Create dynamic header mapping
            $headerMapping = createHeaderMapping($headers);

            // Debug: Log actual headers for troubleshooting
            error_log("DEBUG - Excel Headers: " . json_encode($headers));
            error_log("DEBUG - Header Mapping: " . json_encode($headerMapping));

            // ENHANCED DEBUG: Return detailed error info
            if (empty($headerMapping)) {
                $response = [
                    'success' => false,
                    'message' => __('DEBUG: Hiç Header Eşleşmedi', 'komtera'),
                    'debug' => [
                        'excel_headers' => $headers,
                        'valid_fields' => [
                            'sku', 'urunAciklama', 'marka', 'tur', 'cozum', 'lisansSuresi',
                            'wgCategory', 'wgUpcCode', 'paraBirimi', 'listeFiyati', 'listeFiyatiUpLift',
                            'a_iskonto4', 'a_iskonto3', 'a_iskonto2', 'a_iskonto1',
                            's_iskonto4', 's_iskonto3', 's_iskonto2', 's_iskonto1',
                            'a_iskonto4_r', 'a_iskonto3_r', 'a_iskonto2_r', 'a_iskonto1_r',
                            's_iskonto4_r', 's_iskonto3_r', 's_iskonto2_r', 's_iskonto1_r'
                        ]
                    ]
                ];
                echo json_encode($response);
                unlink($uploadPath);
                exit;
            }

            // Validate required columns
            $requiredFields = ['sku', 'urunAciklama'];
            foreach ($requiredFields as $field) {
                if (!isset($headerMapping[$field])) {
                    $errors[] = __('Gerekli Sütun Bulunamadı:', 'komtera') . " $field";
                }
            }

            if (!empty($errors)) {
                $response = [
                    'success' => false,
                    'message' => __('Excel Dosyası Formatı Hatalı: ', 'komtera') . implode(', ', $errors),
                    'debug' => [
                        'excel_headers' => $headers,
                        'header_mapping' => $headerMapping,
                        'missing_fields' => array_diff($requiredFields, array_keys($headerMapping))
                    ]
                ];
                echo json_encode($response);
                unlink($uploadPath);
                exit;
            }

            // Extract SKUs from Excel data
            $excelSKUs = [];
            $excelData = [];
            foreach ($data as $rowIndex => $row) {
                if (isset($headerMapping['sku']) && !empty($row[$headerMapping['sku']])) {
                    $sku = trim($row[$headerMapping['sku']]);
                    $excelSKUs[] = $sku;

                    // Prepare row data
                    $rowData = [];
                    foreach ($headerMapping as $dbField => $excelIndex) {
                        $value = isset($row[$excelIndex]) ? trim($row[$excelIndex]) : '';
                        $rowData[$dbField] = $value;
                    }
                    $excelData[$sku] = $rowData;
                }
            }

            // Analyze changes
            $analysis = analyzeSKUChanges($conn, $excelSKUs, $targetMarka);

            // If this is a preview request, return analysis
            if ($isPreview) {
                $response = [
                    'success' => true,
                    'preview' => true,
                    'analysis' => [
                        'toDelete' => count($analysis['toDelete']),
                        'toUpdate' => count($analysis['toUpdate']),
                        'toInsert' => count($analysis['toInsert']),
                        'deleteList' => array_slice($analysis['toDelete'], 0, 10), // First 10 for preview
                        'insertList' => array_slice($analysis['toInsert'], 0, 10),  // First 10 for preview
                        'mappedFields' => array_keys($headerMapping),
                        'totalExcelRows' => count($excelSKUs)
                    ],
                    'message' => __('Önizleme Hazır. Değişiklikleri Onaylayın', 'komtera')
                ];
                echo json_encode($response);
                unlink($uploadPath);
                exit;
            }

            // Start transaction for data safety
            $conn->beginTransaction();

            try {
                $deletedCount = 0;
                $updatedCount = 0;
                $insertedCount = 0;

                // STEP 1: DELETE SKUs that exist in DB but not in Excel
                if (!empty($analysis['toDelete'])) {
                    $deleteQuery = "DELETE FROM aa_erp_kt_fiyat_listesi WHERE marka = :marka AND sku IN (" .
                                  str_repeat('?,', count($analysis['toDelete']) - 1) . "?)";
                    $deleteStmt = $conn->prepare($deleteQuery);

                    $params = [$targetMarka];
                    foreach ($analysis['toDelete'] as $sku) {
                        $params[] = $sku;
                    }

                    if ($deleteStmt->execute($params)) {
                        $deletedCount = $deleteStmt->rowCount();
                    }
                }

                // STEP 2: UPDATE existing SKUs
                if (!empty($analysis['toUpdate'])) {
                    foreach ($analysis['toUpdate'] as $sku) {
                        if (isset($excelData[$sku])) {
                            $rowData = $excelData[$sku];

                            // Build dynamic UPDATE query based on mapped fields
                            $updateFields = [];
                            $updateParams = [];

                            foreach ($headerMapping as $dbField => $excelIndex) {
                                if ($dbField !== 'sku') { // Don't update SKU field
                                    $updateFields[] = "$dbField = ?";
                                    $updateParams[] = $rowData[$dbField];
                                }
                            }

                            if (!empty($updateFields)) {
                                $updateQuery = "UPDATE aa_erp_kt_fiyat_listesi SET " .
                                             implode(', ', $updateFields) .
                                             " WHERE sku = ? AND marka = ?";

                                $updateParams[] = $sku;
                                $updateParams[] = $targetMarka;

                                $updateStmt = $conn->prepare($updateQuery);

                                if ($updateStmt->execute($updateParams)) {
                                    $updatedCount++;
                                }
                            }
                        }
                    }
                }

                // STEP 3: INSERT new SKUs
                if (!empty($analysis['toInsert'])) {
                    foreach ($analysis['toInsert'] as $sku) {
                        if (isset($excelData[$sku])) {
                            $rowData = $excelData[$sku];

                            // Ensure marka is set to target marka
                            $rowData['marka'] = $targetMarka;

                            // Build dynamic INSERT query
                            $insertFields = array_keys($rowData);
                            $insertPlaceholders = str_repeat('?,', count($insertFields) - 1) . '?';
                            $insertValues = array_values($rowData);

                            $insertQuery = "INSERT INTO aa_erp_kt_fiyat_listesi (" .
                                         implode(', ', $insertFields) .
                                         ") VALUES ($insertPlaceholders)";

                            $insertStmt = $conn->prepare($insertQuery);

                            if ($insertStmt->execute($insertValues)) {
                                $insertedCount++;
                            }
                        }
                    }
                }

                // Commit transaction
                $conn->commit();

            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }

            // Clean up uploaded file
            unlink($uploadPath);

            // Prepare response message with brand and operation information
            $brandInfo = '';
            if ($fileMarka && $targetMarka && $fileMarka !== strtoupper($targetMarka)) {
                $brandInfo = " (" . $fileMarka . " " . __('markasından', 'komtera') . " " . $targetMarka . " " . __('markasına aktarıldı', 'komtera') . ")";
            } else if ($fileMarka && !$targetMarka) {
                $brandInfo = " (" . $fileMarka . " " . __('markası', 'komtera') . ")";
            } else if (!$fileMarka && $targetMarka) {
                $brandInfo = " (" . $targetMarka . " " . __('markasına aktarıldı', 'komtera') . ")";
            }

            $operationSummary = "• " . $deletedCount . " " . __('SKU silindi', 'komtera') . "\n• " . $updatedCount . " " . __('SKU güncellendi', 'komtera') . "\n• " . $insertedCount . " " . __('yeni SKU eklendi', 'komtera');

            // Return response
            $response = [
                'success' => true,
                'message' => __('Senkronizasyon Tamamlandı', 'komtera') . "$brandInfo\n\n$operationSummary",
                'operations' => [
                    'deleted' => $deletedCount,
                    'updated' => $updatedCount,
                    'inserted' => $insertedCount,
                    'total' => $deletedCount + $updatedCount + $insertedCount
                ],
                'source_brand' => $fileMarka,
                'target_brand' => $targetMarka,
                'sync_mode' => true
            ];

            echo json_encode($response);

        } catch (Exception $e) {
            unlink($uploadPath);
            echo json_encode(['success' => false, 'message' => __('Dosya İşleme Hatası: ', 'komtera') . $e->getMessage()]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => __('Dosya Yüklenemedi', 'komtera')]);
    }

} else {
    echo json_encode(['success' => false, 'message' => __('Geçersiz İstek', 'komtera')]);
}
?>
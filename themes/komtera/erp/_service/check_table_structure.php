<?php
// WordPress integration
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "wp-load.php bulunamadı.\n";
    echo "Başlangıç dizini: " . __DIR__ . "\n";
    exit;
}

// Database connection
include dirname(__DIR__) . '/_conn.php';

header('Content-Type: application/json');

try {
    // Tablo yapısını kontrol et
    $sql = "SELECT
                COLUMN_NAME,
                DATA_TYPE,
                CHARACTER_MAXIMUM_LENGTH,
                IS_NULLABLE,
                COLUMN_DEFAULT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_NAME = '" . getTableName('aa_erp_kt_firsatlar') . "'
            ORDER BY ORDINAL_POSITION";

    $stmt = $conn->query($sql);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($columns)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tablo bulunamadı: ' . getTableName('aa_erp_kt_firsatlar')
        ]);
        exit;
    }

    // Problem olabilecek alanları tespit et
    $potentialIssues = [];
    $shortColumns = [];

    foreach ($columns as $column) {
        $columnName = $column['COLUMN_NAME'];
        $dataType = $column['DATA_TYPE'];
        $maxLength = $column['CHARACTER_MAXIMUM_LENGTH'];

        // Metin alanları için kısa uzunlukları tespit et
        if (($dataType === 'varchar' || $dataType === 'nvarchar' || $dataType === 'char') && $maxLength !== null) {
            if ($maxLength < 50) {
                $shortColumns[] = [
                    'column' => $columnName,
                    'type' => $dataType,
                    'max_length' => $maxLength,
                    'issue' => "Kısa alan ($maxLength karakter)"
                ];
            }

            // Specific problem areas based on the error data
            $checkData = [
                'FIRSAT_NO' => 7,        // "F266300"
                'MARKA' => 6,            // "SOPHOS"
                'GELIS_KANALI' => 7,     // "Komtera"
                'OLASILIK' => 11,        // "1-Discovery"
                'MUSTERI_TEMSILCISI' => 5, // "admin"
                'PROJE_ADI' => 4,        // "TEST"
                'FIRSAT_ACIKLAMA' => 28, // "TEst icin acilmis bir firsat"
                'BAYI_ADI' => 32,        // "KOMTERA TEKNOLOJİ ANONİM ŞİRKETİ"
                'BAYI_CHKODU' => 14,     // "120.01.01.1274"
                'BAYI_YETKILI_ISIM' => 12, // "Gökhan Ilgıt"
                'MUSTERI_ADI' => 41,     // "Piksel Bilgisayar Ltd. Şti. (Recep Cinet)"
                'MUSTERI_YETKILI_ISIM' => 11, // "Recep Cinet"
                'KAYIDI_ACAN' => 5       // "admin"
            ];

            if (isset($checkData[$columnName])) {
                $dataLength = $checkData[$columnName];
                if ($maxLength !== null && $dataLength > $maxLength) {
                    $potentialIssues[] = [
                        'column' => $columnName,
                        'max_length' => $maxLength,
                        'data_length' => $dataLength,
                        'issue' => "Data uzunluğu ($dataLength) maksimum uzunluktan ($maxLength) büyük!"
                    ];
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'table_structure' => $columns,
        'short_columns' => $shortColumns,
        'potential_issues' => $potentialIssues,
        'analysis' => [
            'total_columns' => count($columns),
            'short_columns_count' => count($shortColumns),
            'issues_count' => count($potentialIssues)
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
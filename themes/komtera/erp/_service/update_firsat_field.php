<?php
// Hataları JSON formatında döndürmek için display_errors kapalı olmalı
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// JSON header'ı en başta
header('Content-Type: application/json');

// Table helper
include dirname(dirname(__DIR__)) . '/inc/table_helper.php';

// Database connection
include dirname(__DIR__) . '/_conn.php';

try {
    if (!isset($_POST['firsat_no']) || empty($_POST['firsat_no'])) {
        throw new Exception('Fırsat numarası belirtilmedi');
    }

    if (!isset($_POST['field']) || empty($_POST['field'])) {
        throw new Exception('Alan adı belirtilmedi');
    }

    $firsat_no = $_POST['firsat_no'];
    $field = $_POST['field'];
    $value = $_POST['value'] ?? '';

    // İzin verilen alanlar
    $allowed_fields = [
        'bayi_yetkili' => [
            'BAYI_YETKILI_ISIM' => 'yetkili_isim',
            'BAYI_YETKILI_TEL' => 'yetkili_tel',
            'BAYI_YETKILI_EPOSTA' => 'yetkili_eposta'
        ],
        'musteri' => [
            'MUSTERI_ADI' => 'musteri_adi'
        ],
        'musteri_yetkili' => [
            'MUSTERI_YETKILI_ISIM' => 'yetkili_isim',
            'MUSTERI_YETKILI_TEL' => 'yetkili_tel',
            'MUSTERI_YETKILI_EPOSTA' => 'yetkili_eposta'
        ]
    ];

    if (!isset($allowed_fields[$field])) {
        throw new Exception('Geçersiz alan: ' . $field);
    }

    // Kolon uzunluk limitleri (karakterlerde - NVARCHAR için her karakter 2 byte)
    $fieldLimits = [
        'BAYI_YETKILI_ISIM' => 25,
        'BAYI_YETKILI_TEL' => 11,
        'BAYI_YETKILI_EPOSTA' => 50,
        'MUSTERI_ADI' => 100,
        'MUSTERI_YETKILI_ISIM' => 100,
        'MUSTERI_YETKILI_TEL' => 11,
        'MUSTERI_YETKILI_EPOSTA' => 50
    ];

    // POST'tan gelen değerleri al ve trim et
    $updates = [];
    $params = [':firsat_no' => $firsat_no];
    $truncatedFields = [];

    foreach ($allowed_fields[$field] as $db_column => $post_key) {
        if (isset($_POST[$post_key])) {
            $updates[] = "$db_column = :$post_key";
            // Değerleri trim et ve null ise boş string yap
            $value = trim($_POST[$post_key] ?? '');

            // Uzunluk kontrolü ve kısaltma
            if (isset($fieldLimits[$db_column])) {
                $maxLength = $fieldLimits[$db_column];
                $currentLength = mb_strlen($value);

                if ($currentLength > $maxLength) {
                    $truncatedFields[] = "$db_column: $currentLength → $maxLength karakter";
                    $value = mb_substr($value, 0, $maxLength);
                }
            }

            $params[":$post_key"] = $value;
        }
    }

    if (empty($updates)) {
        throw new Exception('Güncellenecek veri bulunamadı');
    }

    // Kısaltma yapıldıysa logla
    if (!empty($truncatedFields)) {
        error_log("FIRSAT GÜNCELLEME - Alan kısaltması yapıldı: " . implode(', ', $truncatedFields));
    }

    $sql = "UPDATE " . getTableName('aa_erp_kt_firsatlar') . "
            SET " . implode(', ', $updates) . "
            WHERE FIRSAT_NO = :firsat_no";

    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $val) {
        // SQL Server için string'leri NVARCHAR olarak bind et (UTF-8 için)
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Alan başarıyla güncellendi'
        ]);
    } else {
        throw new Exception('Güncelleme başarısız oldu');
    }

} catch (PDOException $e) {
    http_response_code(500);

    // Parametre uzunluklarını hesapla
    $paramLengths = [];
    if (isset($params)) {
        foreach ($params as $key => $val) {
            $paramLengths[$key] = [
                'value' => $val,
                'length' => mb_strlen($val)
            ];
        }
    }

    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage(),
        'sql' => $sql ?? 'SQL oluşturulamadı',
        'params' => $params ?? [],
        'param_lengths' => $paramLengths
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

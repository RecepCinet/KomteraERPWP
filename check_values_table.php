<?php
/**
 * Tablo Kontrol ve Oluşturma - MSSQL
 * URL: https://erptest.komtera.com/check_values_table.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>aa_erp_kt_values Tablo Kontrolü (MSSQL)</h2>";

// MSSQL bağlantısı
$serverName = "172.16.85.76,1433";
$database = "LKS";
$username = "crm";
$password = "!!!Crm!!!";

echo "<p>Veritabanı: <strong>$database</strong> @ <strong>$serverName</strong></p>";

try {
    $dsn = "sqlsrv:Server=$serverName;Database=$database;Encrypt=1;TrustServerCertificate=1";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<p style='color: green;'>✓ MSSQL bağlantısı başarılı!</p>";

    // Test ortamı için tablo ismi
    $table_name = 'atest_aa_erp_kt_values';

    echo "<h3>Kontrol edilen tablo: $table_name</h3>";

    // Tablo var mı kontrol et
    $check = $conn->query("SELECT OBJECT_ID('$table_name', 'U') as table_exists")->fetch();

    if ($check['table_exists']) {
        echo "<p style='color: green;'>✓ Tablo mevcut!</p>";

        // İçeriğini göster
        $rows = $conn->query("SELECT * FROM [$table_name]")->fetchAll();
        echo "<h4>Tablodaki kayıtlar:</h4>";
        echo "<pre>" . print_r($rows, true) . "</pre>";

    } else {
        echo "<p style='color: orange;'>⚠ Tablo mevcut değil, oluşturuluyor...</p>";

        // Tablo oluştur
        $sql = "
        CREATE TABLE [$table_name] (
            [id] INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
            [key] NVARCHAR(255) NOT NULL UNIQUE,
            [value] NVARCHAR(MAX) NULL
        );

        INSERT INTO [$table_name] ([key], [value]) VALUES ('teklif_notu', '');
        ";

        $conn->exec($sql);
        echo "<p style='color: green;'>✓ Tablo oluşturuldu ve ilk kayıt eklendi!</p>";
    }

    echo "<hr>";
    echo "<h3>✅ İŞLEM TAMAMLANDI!</h3>";
    echo "<p><a href='/wp-admin/admin.php?page=ayarlar_slug&module=teklif_notu'>Teklif Notu Modülünü Test Et</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ HATA: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

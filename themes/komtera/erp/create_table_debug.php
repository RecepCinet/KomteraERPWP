<?php
/**
 * Tablo Oluşturma ve Debug
 */

// WordPress global wpdb
global $wpdb;

// Table helper
require_once __DIR__ . '/../../inc/table_helper.php';

$table_name = getTableName('aa_erp_kt_values');

echo "<h2>Tablo Debug Bilgileri</h2>";
echo "<p>Tablo ismi: <strong>$table_name</strong></p>";

// Charset
$charset_collate = $wpdb->get_charset_collate();

// SQL
$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_unique` (`key`)
) $charset_collate";

echo "<h3>SQL:</h3>";
echo "<pre>$sql</pre>";

// Çalıştır
$result = $wpdb->query($sql);

echo "<h3>Sonuç:</h3>";
if ($result === false) {
    echo "<p style='color: red;'>❌ HATA: " . $wpdb->last_error . "</p>";
} else {
    echo "<p style='color: green;'>✓ SQL çalıştırıldı!</p>";
}

// Tablo var mı kontrol et
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

echo "<h3>Tablo Kontrolü:</h3>";
if ($table_exists) {
    echo "<p style='color: green;'>✓ Tablo mevcut: <strong>$table_exists</strong></p>";

    // İlk kayıt ekle
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM `$table_name` WHERE `key` = %s",
        'teklif_notu'
    ));

    if (!$exists) {
        $insert_result = $wpdb->insert(
            $table_name,
            ['key' => 'teklif_notu', 'value' => ''],
            ['%s', '%s']
        );

        if ($insert_result) {
            echo "<p style='color: green;'>✓ İlk kayıt eklendi!</p>";
        } else {
            echo "<p style='color: red;'>❌ Kayıt eklenemedi: " . $wpdb->last_error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ Kayıt zaten mevcut</p>";
    }

} else {
    echo "<p style='color: red;'>❌ Tablo bulunamadı!</p>";
}

// Tüm tabloları listele
echo "<h3>Veritabanındaki Tüm Tablolar:</h3>";
$tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>" . $table[0] . "</li>";
}
echo "</ul>";
?>

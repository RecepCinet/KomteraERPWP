<?php
/**
 * aa_erp_kt_values Tablosu Oluşturma Script'i
 * Bu dosyayı tarayıcıdan bir kez çalıştırın: http://erptest.komtera.com/create_values_table.php
 */

// WordPress'i yükle
require_once(__DIR__ . '/wp-load.php');

global $wpdb;

// Table helper'ı include et
require_once get_stylesheet_directory() . '/inc/table_helper.php';

echo "<h2>aa_erp_kt_values Tablosu Oluşturuluyor...</h2>";

// Test ortamı için tablo ismi
$table_name = getTableName('aa_erp_kt_values');

echo "<p>Tablo ismi: <strong>$table_name</strong></p>";

// Tablo oluştur
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

echo "<p style='color: green;'>✓ Tablo oluşturuldu!</p>";

// İlk kayıt ekle
$exists = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM `$table_name` WHERE `key` = %s",
    'teklif_notu'
));

if (!$exists) {
    $wpdb->insert(
        $table_name,
        ['key' => 'teklif_notu', 'value' => ''],
        ['%s', '%s']
    );
    echo "<p style='color: green;'>✓ 'teklif_notu' kaydı eklendi!</p>";
} else {
    echo "<p style='color: blue;'>ℹ 'teklif_notu' kaydı zaten mevcut.</p>";
}

echo "<hr>";
echo "<p><strong>Tamamlandı!</strong> Şimdi bu dosyayı silebilir ve modülü test edebilirsin.</p>";
echo "<p><a href='/wp-admin/admin.php?page=ayarlar_slug&module=teklif_notu'>Teklif Notu Modülünü Aç</a></p>";
?>

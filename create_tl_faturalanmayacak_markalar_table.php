<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once __DIR__ . '/themes/komtera/_conn.php';

echo "<h2>TL Faturalanmayacak Markalar Tablosu Oluşturma</h2>";

$sql = "CREATE TABLE IF NOT EXISTS `atest_aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marka` varchar(100) NOT NULL COMMENT 'Marka adı',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marka` (`marka`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Tablo başarıyla oluşturuldu: atest_aa_erp_kt_ayarlar_tl_faturalanmayacak_markalar</p>";

    echo "<p><strong>Tablo yapısı:</strong></p>";
    echo "<pre>";
    echo "- id (AUTO_INCREMENT)\n";
    echo "- marka (varchar 100, UNIQUE)\n";
    echo "- created_at (timestamp)\n";
    echo "</pre>";

    echo "<hr>";
    echo "<p><a href='https://erptest.komtera.com/wp-admin/admin.php?page=ayarlar_slug&module=tl_faturalanmayacak_markalar' target='_blank'>→ TL Faturalanmayacak Markalar Modülüne Git</a></p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "<p style='color: orange;'>⚠ Tablo zaten mevcut</p>";
    } else {
        echo "<p style='color: red;'>✗ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

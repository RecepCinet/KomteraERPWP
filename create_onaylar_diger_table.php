<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// WordPress connection
require_once __DIR__ . '/themes/komtera/_conn.php';

echo "<h2>Diğer Onaylar Tablosu Oluşturma</h2>";

$sql = "CREATE TABLE IF NOT EXISTS `atest_aa_erp_kt_ayarlar_onaylar_diger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) NOT NULL COMMENT 'Anahtar kelime/key',
  `aciklama` text DEFAULT NULL COMMENT 'Açıklama',
  `kullanici` varchar(255) DEFAULT NULL COMMENT 'WordPress kullanıcı display name',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $conn->exec($sql);
    echo "<p style='color: green;'>✓ Tablo başarıyla oluşturuldu: atest_aa_erp_kt_ayarlar_onaylar_diger</p>";

    // Örnek kayıt ekle
    $insertSql = "INSERT INTO `atest_aa_erp_kt_ayarlar_onaylar_diger` (`key_name`, `aciklama`, `kullanici`)
                  VALUES ('teklif_onay', 'Teklif onayı için mail gönderilecek kullanıcı', 'Admin User')";

    $conn->exec($insertSql);
    echo "<p style='color: green;'>✓ Örnek kayıt başarıyla eklendi</p>";

    echo "<p><strong>Tablo yapısı:</strong></p>";
    echo "<pre>";
    echo "- id (AUTO_INCREMENT)\n";
    echo "- key_name (varchar 100, UNIQUE)\n";
    echo "- aciklama (text)\n";
    echo "- kullanici (varchar 255)\n";
    echo "- created_at (timestamp)\n";
    echo "- updated_at (timestamp)\n";
    echo "</pre>";

    echo "<hr>";
    echo "<p><a href='https://erptest.komtera.com/wp-admin/admin.php?page=ayarlar_slug&module=onaylar_diger' target='_blank'>→ Diğer Onaylar Modülüne Git</a></p>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "<p style='color: orange;'>⚠ Tablo zaten mevcut</p>";
    } else {
        echo "<p style='color: red;'>✗ Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

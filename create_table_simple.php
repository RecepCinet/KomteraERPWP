<?php
/**
 * Basit Tablo Oluşturma - WordPress'siz
 * URL: https://erptest.komtera.com/create_table_simple.php
 */

// Hata gösterimi
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>aa_erp_kt_values Tablo Oluşturma</h2>";

// Veritabanı bağlantısı - WordPress wp-config.php değerlerini kullanmalıyız
// Eğer wp-config.php varsa include edelim
$wp_config_path = __DIR__ . '/wp-config.php';
if (!file_exists($wp_config_path)) {
    // Bir üst dizinde olabilir
    $wp_config_path = dirname(__DIR__) . '/wp-config.php';
}

if (file_exists($wp_config_path)) {
    echo "<p>wp-config.php bulundu: $wp_config_path</p>";

    // wp-config.php'yi parse et (include etmeden)
    $config_content = file_get_contents($wp_config_path);

    // DB bilgilerini regex ile çek
    preg_match("/define\s*\(\s*'DB_NAME'\s*,\s*'([^']+)'/", $config_content, $db_name);
    preg_match("/define\s*\(\s*'DB_USER'\s*,\s*'([^']+)'/", $config_content, $db_user);
    preg_match("/define\s*\(\s*'DB_PASSWORD'\s*,\s*'([^']+)'/", $config_content, $db_password);
    preg_match("/define\s*\(\s*'DB_HOST'\s*,\s*'([^']+)'/", $config_content, $db_host);
    preg_match("/\\$table_prefix\s*=\s*'([^']+)'/", $config_content, $table_prefix);

    $DB_NAME = $db_name[1] ?? '';
    $DB_USER = $db_user[1] ?? '';
    $DB_PASSWORD = $db_password[1] ?? '';
    $DB_HOST = $db_host[1] ?? 'localhost';
    $TABLE_PREFIX = $table_prefix[1] ?? 'wp_';

    echo "<p>Veritabanı: <strong>$DB_NAME</strong></p>";
    echo "<p>Kullanıcı: <strong>$DB_USER</strong></p>";
    echo "<p>Host: <strong>$DB_HOST</strong></p>";
    echo "<p>Prefix: <strong>$TABLE_PREFIX</strong></p>";

    try {
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "<p style='color: green;'>✓ Veritabanına bağlanıldı!</p>";

        // Tablo ismi (test ortamı için atest_ prefix'i)
        $table_name = 'atest_aa_erp_kt_values';

        echo "<h3>Tablo Oluşturuluyor: $table_name</h3>";

        // Tablo oluştur
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `key` varchar(255) NOT NULL,
          `value` text,
          PRIMARY KEY (`id`),
          UNIQUE KEY `key_unique` (`key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Tablo oluşturuldu!</p>";

        // İlk kayıt ekle
        $check = $pdo->query("SELECT COUNT(*) FROM `$table_name` WHERE `key` = 'teklif_notu'")->fetchColumn();

        if (!$check) {
            $stmt = $pdo->prepare("INSERT INTO `$table_name` (`key`, `value`) VALUES (:key, :value)");
            $stmt->execute(['key' => 'teklif_notu', 'value' => '']);
            echo "<p style='color: green;'>✓ 'teklif_notu' kaydı eklendi!</p>";
        } else {
            echo "<p style='color: blue;'>ℹ 'teklif_notu' kaydı zaten mevcut</p>";
        }

        echo "<hr>";
        echo "<h3>✅ TAMAMLANDI!</h3>";
        echo "<p><a href='/wp-admin/admin.php?page=ayarlar_slug&module=teklif_notu'>Teklif Notu Modülünü Aç</a></p>";
        echo "<p><small>Bu dosyayı şimdi silebilirsin: create_table_simple.php</small></p>";

    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ HATA: " . $e->getMessage() . "</p>";
    }

} else {
    echo "<p style='color: red;'>❌ wp-config.php bulunamadı!</p>";
    echo "<p>Aramılan yollar:</p>";
    echo "<ul>";
    echo "<li>" . __DIR__ . '/wp-config.php</li>';
    echo "<li>" . dirname(__DIR__) . '/wp-config.php</li>';
    echo "</ul>";
}
?>

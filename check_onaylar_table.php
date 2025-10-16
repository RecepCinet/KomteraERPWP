<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once __DIR__ . '/themes/komtera/_conn.php';
require_once __DIR__ . '/themes/komtera/inc/table_helper.php';

echo "<h2>Onaylar Tablosu Kontrolü</h2>";

$tableName = getTableName('aa_erp_kt_ayarlar_onaylar');
echo "<p><strong>Tablo adı:</strong> $tableName</p>";

// Tablo yapısını göster
echo "<h3>Tablo Yapısı (DESCRIBE):</h3>";
try {
    $stmt = $conn->query("DESCRIBE $tableName");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Kayıtları göster
echo "<h3>Mevcut Kayıtlar (SELECT *):</h3>";
try {
    $stmt = $conn->query("SELECT * FROM $tableName LIMIT 5");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($data) > 0) {
        echo "<p><strong>Toplam kayıt:</strong> " . count($data) . "</p>";
        echo "<table border='1' cellpadding='5'>";

        // Header
        echo "<tr>";
        foreach (array_keys($data[0]) as $column) {
            echo "<th>" . htmlspecialchars($column) . "</th>";
        }
        echo "</tr>";

        // Rows
        foreach ($data as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Kayıt yok</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Hata: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

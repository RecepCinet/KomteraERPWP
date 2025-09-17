<?php
// Excel test script - upload bir excel dosyası ve ne olduğunu görelim

if ($_FILES && isset($_FILES['test_file'])) {
    $file = $_FILES['test_file'];
    $uploadPath = '../uploads/test_' . time() . '.xlsx';

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        echo "<h3>Dosya yüklendi: $uploadPath</h3>";
        echo "<p>Dosya boyutu: " . filesize($uploadPath) . " bytes</p>";

        // ZIP olarak açmayı dene
        $zip = new ZipArchive;
        $result = $zip->open($uploadPath);

        if ($result === TRUE) {
            echo "<h4>✅ ZIP olarak açıldı</h4>";
            echo "<p>Dosya sayısı: " . $zip->numFiles . "</p>";

            echo "<h5>ZIP içeriği:</h5><ul>";
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                echo "<li>$entry</li>";
            }
            echo "</ul>";

            // Shared strings var mı?
            $sharedStrings = $zip->getFromName('xl/sharedStrings.xml');
            if ($sharedStrings) {
                echo "<h5>✅ Shared strings bulundu (" . strlen($sharedStrings) . " bytes)</h5>";
                echo "<pre>" . htmlspecialchars(substr($sharedStrings, 0, 500)) . "...</pre>";
            } else {
                echo "<h5>❌ Shared strings bulunamadı</h5>";
            }

            // Worksheet var mı?
            $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            if ($worksheet) {
                echo "<h5>✅ Worksheet bulundu (" . strlen($worksheet) . " bytes)</h5>";
                echo "<pre>" . htmlspecialchars(substr($worksheet, 0, 500)) . "...</pre>";
            } else {
                echo "<h5>❌ Worksheet bulunamadı</h5>";
            }

            $zip->close();
        } else {
            echo "<h4>❌ ZIP olarak açılamadı (Error: $result)</h4>";

            // Dosyanın ilk birkaç byte'ını görelim
            $handle = fopen($uploadPath, 'rb');
            $header = fread($handle, 20);
            fclose($handle);

            echo "<p>Dosya başlangıcı (hex): " . bin2hex($header) . "</p>";
            echo "<p>Dosya başlangıcı (text): " . htmlspecialchars($header) . "</p>";
        }

        // Temizle
        unlink($uploadPath);

    } else {
        echo "Dosya yüklenemedi!";
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Excel Test</title>
</head>
<body>
    <h2>Excel Dosyası Test</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="test_file" accept=".xlsx,.xls" required>
        <button type="submit">Test Et</button>
    </form>
</body>
</html>
<?php
function BotMesaj($mesaj) {
    $var = print_r($mesaj, true);
    $url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=" . urlencode( $var));
}
$serverName = "172.16.85.76";
try {
    $dsn = "sqlsrv:server=$serverName;"
        . "Database=LKS;"
        . "Encrypt=yes;"
        . "TrustServerCertificate=true;"
        . "LoginTimeout=60";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::SQLSRV_ATTR_QUERY_TIMEOUT => 900,
        "CharacterSet" => "UTF-8"
    ];
    $conn = new PDO($dsn, "crm", "!!!Crm!!!", $options);
} catch (Exception $e) {
    echo "!---MS SQL Baglanti Sorunu!---<br />\n";
    die($e->getMessage());
}
?>
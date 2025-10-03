<meta http-equiv="refresh" content="60">
<?php
error_reporting(0);
ini_set("display_errors", false);
ini_set('mssql.charset', 'UTF-8');

header('Content-Type: text/html; charset=utf-8');

$serverName = "172.16.85.76";
try {
    $options = array(
        "CharacterSet" => "UTF-8"
    );
    $conn = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "!!!Crm!!!", $options);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "!---MS SQL Baglanti Sorunu!---<br />" . PHP_EOL;
    die(print_r($e->getMessage()));
}

$sqlstring = 'select * from aaa_erp_kt_bayiler b
LEFT JOIN aa_erp_kt_bayiler_kara_liste kl ON b.CH_KODU = kl.ch_kodu
WHERE kl.kara_liste = 1 order by b.CH_UNVANI';
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$temp="";

foreach ($gelen as $value) {
    if ($temp!=$value['CH_UNVANI']) {
        echo "<h1>" . mb_substr($value['CH_UNVANI'], 0, 1) . "</h1>";
    }
    echo $value['CH_UNVANI']. " <b><small>" . $value['CH_KODU'] . "</small></b><br />";
    $temp=$value['CH_UNVANI'];
}

while ($rs=odbc_fetch_object($sql)) {
    if ($temp!=mb_substr($rs->bayi, 0, 1)) {
        echo "<h1>" . mb_substr($rs->bayi, 0, 1) . "</h1>";
    }
    echo $rs->bayi . "<br />";
    $temp=mb_substr($rs->bayi, 0, 1);
}
echo "<br /><br />";
?>

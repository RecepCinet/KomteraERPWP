<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

include '../_conn.php';

$stmt = $conn->prepare("select kur,banka,iban from aa_erp_kt_bankalar where kur=:kur order by sira");
$stmt->execute(['kur' => $_GET['kur']]); 
$gelen = $stmt->fetchAll();

for ($t=0;$t<count($gelen);$t++) {
    echo $gelen[$t]['kur'] . chr(9) . $gelen[$t]['banka'] . chr(9) . $gelen[$t]['iban'] . "\n";
}

?>

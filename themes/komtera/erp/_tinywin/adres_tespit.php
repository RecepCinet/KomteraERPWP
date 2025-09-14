<?php

include '../../_conn.php';

$il = $_GET['il'];
$adres = $_GET['adres'];

$adres = str_replace("-", " ", $adres);
$adres = str_replace(":", "", $adres);
$adres = str_replace(".", " ", $adres);
$adres = str_replace("/", " ", $adres);
$adres = str_replace(chr(10), " ", $adres);
$adres = str_replace(chr(13), " ", $adres);
$kelime = explode(" ", $adres);

$sql = "select * from aa_erp_il_ilce where il='$il' AND (###)";
$dc = "";
$say = 0;
for ($t = 0; $t < count($kelime); $t++) {
    if ($kelime[$t] != "" and strlen($kelime[$t])>1) {
        if ($say > 0) {
            $dc .= " or ";
        }
        $dc .= " ilce='" . $kelime[$t] . "' ";
        $say++;
    }
}
$sql = str_replace("###", $dc, $sql);

$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo $data[0]['ilce'];
?>

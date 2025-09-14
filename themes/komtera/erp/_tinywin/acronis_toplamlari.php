<?php
error_reporting(E_ALL);

include "../_conn.php";

$url = "select acronis_bayi,sum(tutar) as TUTAR from aa_erp_kt_acronis_fatura_kes group by acronis_bayi";
$stmt = $conn->prepare($url);
$stmt->execute();
$data= $stmt->fetchAll(PDO::FETCH_ASSOC);

//print_r($data);

echo "<table width='100%'>";

foreach ($data as $satir) {
    echo "<tr><td>" . $satir['acronis_bayi'] . "</td><td align='right'>" . $satir['TUTAR'] . "</td><tr>";
}

?>

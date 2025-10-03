<?php
$siparis_no = $_GET['siparis_no'];
$stmt = $conn->prepare("select LISANS from aa_erp_kt_siparisler_urunler su where su.X_SIPARIS_NO =:siparis_no");
$stmt->execute(['siparis_no' => $siparis_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
$out="";
$say=0;
foreach ($gelen as $key => $satir) {
    $say++;
    if ($say>1) {
        $out .= ", ";    
    }
    $out .= $satir['LISANS'];
}
echo $out;
?>

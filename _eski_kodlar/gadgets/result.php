<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

$ne = $_GET['ara'];
if ($ne === "") {
    die();
}
include '../_conn.php';
$cryp=$_GET['cryp']!="" ? $_GET['cryp'] : $_POST['cryp'];
include '../_conn_fm.php';
include '../_user.php';
$markalar=$_SESSION['user'][0]['markalar'];
$markalar="'" . str_replace("\r","','",$markalar) . "'";

echo "<table width='100%' cellpadding=10><tr><td>";

$stmt = $conn->prepare("select FIRSAT_NO AS CEVAP from aa_erp_kt_firsatlar f where f.SIL<>1 and f.MARKA in ($markalar) AND FIRSAT_NO like :ne order by id desc");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Fırsatlar:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Firsat', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select t.TEKLIF_NO as CEVAP,f.MARKA from aa_erp_kt_teklifler t
LEFT JOIN aa_erp_kt_firsatlar f ON t.X_FIRSAT_NO = f.FIRSAT_NO 
WHERE f.MARKA IN ($markalar) AND (TEKLIF_NO like :ne)
order by t.id desc");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Teklif Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Teklif', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select s.SIPARIS_NO as CEVAP from aa_erp_kt_siparisler s LEFT JOIN aa_erp_kt_firsatlar f
ON s.X_FIRSAT_NO = f.FIRSAT_NO 
WHERE f.MARKA in ($markalar) AND  SIPARIS_NO like :ne order by s.id desc
");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Sipariş Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Siparis', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select id as CEVAP from aa_erp_kt_demolar t WHERE id like :ne order by id desc");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Demo Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Demo', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo "#" . $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select t.TEKLIF_NO as CEVAP from aa_erp_kt_firsatlar f LEFT JOIN aa_erp_kt_teklifler t ON f.FIRSAT_NO = t.X_FIRSAT_NO 
where f.MARKA IN ($markalar) AND t.TEKLIF_NO IN (select X_TEKLIF_NO from aa_erp_kt_teklifler_urunler tu where tu.SKU like :ne)"
);
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Teklif içinde SKU Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Teklif', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select t.TEKLIF_NO as CEVAP from aa_erp_kt_firsatlar f LEFT JOIN aa_erp_kt_teklifler t ON f.FIRSAT_NO = t.X_FIRSAT_NO 
where f.MARKA IN ($markalar) AND t.TEKLIF_NO IN (select X_TEKLIF_NO from aa_erp_kt_teklifler_urunler tu where tu.SKU like :ne)"
);
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Sipariş içinde SKU Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['CEVAP'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Teklif', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a> <?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select X_SIPARIS_NO,ACIKLAMA from aa_erp_kt_siparisler_urunler su where ACIKLAMA like :ne");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Sipariş içinde Aciklama Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['X_SIPARIS_NO'] . " (" . $value['ACIKLAMA'] . ")";
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Siparis', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a><br /><?PHP
    }
    echo "<br /><br />";
}

$stmt = $conn->prepare("select X_SIPARIS_NO from aa_erp_kt_siparisler_urunler su where LISANS like :ne");
$stmt->execute(['ne' => "%" . $ne . "%"]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($gelen)) {
    echo "<b>Siparis ici Serial No Bulundu:</b><br />";
    foreach ($gelen as $value) {
        $yaz = $value['X_SIPARIS_NO'];
        ?><a href="#" onclick="FileMaker.PerformScriptWithOption('Siparis', 'Ac|<?PHP echo $yaz; ?>');"><?PHP echo $yaz; ?></a><br /><?PHP
    }
    echo "<br /><br />";
}

echo "</td></tr></table>";
?>

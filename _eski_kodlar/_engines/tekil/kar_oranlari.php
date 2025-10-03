<?php

function BayiKod($yazi) {
    $kod=0;
    switch ($yazi) {
        case "AUTHORIZED":
            $kod=1;
            break;
        case "SILVER":
            $kod=2;
            break;
        case "GOLD":
            $kod=3;
            break;
        case "PLATINUM":
            $kod=4;
            break;
        default:
            $kod=0;
            break;
    }    
    return $kod;
}

$firsat_no=$_GET['firsat_no'];
$trace=$_GET['trace'];

$stmt = $conn->prepare("select MARKA,BAYI_CHKODU,MARKA_BAYI_SEVIYE from aa_erp_kt_firsatlar f where f.FIRSAT_NO=:firsat_no");
$stmt->execute(['firsat_no' => $firsat_no]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$seviye_yazi=$gelen["MARKA_BAYI_SEVIYE"];
$seviye=BayiKod($gelen["MARKA_BAYI_SEVIYE"]);
$ch_kodu=$gelen['BAYI_CHKODU'];
$marka=$gelen['MARKA'];

// CH KODU ICIN OZEL BI ORAN VARMI?
$stmt = $conn->prepare("select * from aa_erp_kt_ayarlar_onaylar_kar k where marka=:marka AND bayi_ch_kodu=:ch_kodu");
$stmt->execute(['marka' => $marka, 'ch_kodu' => $ch_kodu]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($gelen['onay1_oran']>0) {
    //print_r($gelen);
    $gelen['neden']="Bayi için özel oran!";
    echo json_encode($gelen);
    die();
}

// BAYI SEVIYESI ICIN ICIN OZEL BI ORAN VARMI?
$stmt = $conn->prepare("select * from aa_erp_kt_ayarlar_onaylar_kar k where marka=:marka AND seviye=:seviye");
$stmt->execute(['marka' => $marka, 'seviye' => $seviye]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($trace==="1") {
    print_r($gelen);
}

if ($gelen['onay1_oran']>0) {
    //print_r($gelen);
    $gelen['neden']="$seviye_yazi için özel oran!";
    echo json_encode($gelen);
    die();
}

// BAYI SEVIYESI ICIN ICIN OZEL BI ORAN VARMI?
$stmt = $conn->prepare("select * from aa_erp_kt_ayarlar_onaylar_kar k where marka=:marka");
$stmt->execute(['marka' => $marka]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($gelen['onay1_oran']>0) {
    //print_r($gelen);
    $gelen['neden']="Marka için oran!";
    echo json_encode($gelen);
    die();
}

?>

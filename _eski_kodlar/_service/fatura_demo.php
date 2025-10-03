<?php

error_reporting(0);
ini_set("display_errors", false);
set_time_limit(600);
$port = "8081";

include '_func.php';

$trace = isset($_GET['trace']) ? (int) $_GET['trace'] : 0;
$fis_dur = 0;   //0 Irsaliye 1 Fatura

if ($trace === 1) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
}

include '../_conn.php';
include '../_conn_fm.php';

$demo_no = $_GET['demo_no'];

$url = "select * from aa_erp_kt_demolar where id='$demo_no'";
$stmt = $conn->prepare($url);
$stmt->execute();
$d = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
trace($d);

$stmt = $conn2->prepare("select LOGO_kullanici from TF_USERS where kullanici='" . $d['MUSTERI_TEMSILCISI'] . "'");
$stmt->execute();
$logo_kullanici = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['LOGO_kullanici'];

$unvan=mb_substr(trim((string) $d['BAYI']), 0, 100);

if ($d['TESLIMAT_KIME']=='Müşteri') {
    $unvan=$d['BAYININ_MUSTERISI'];
}

$date = date('Y-m-d');
$time = date("H:i:s");
$baskiTarihi = $date . "T" . $time . "Z";

$fatura = array(
    "_teklif_no" => (string) "T10" . $demo_no,
    "siparisNO" => (string) "T10" . $demo_no,
    "irsaliyeTarihi" => (string) $baskiTarihi,
    "faturaTarihi" => (string) $baskiTarihi,
    "cariKod" => (string) $d['BAYI_CHKODU'],
    "projeKodu" => (string) "GENEL",
    "vadeKodu" => (string) "PEŞİN",
    "satisElemanKodu" => (string) $logo_kullanici,
    "dovizTuru" => (string) "TL",
    "dovizKuru" => (double) 0,
    "unvan" => $unvan,
    "kisiBilgi" => trim((string) ""),
    "adres" => mb_substr(trim((string) $d['ADRES'] . $d['ADRES_ILCE'] . $d['ADRES_SEHIR']), 0, 50),
    "musteriSiparisNo" => trim((string) ""),
    "ambarKodu" => (int) 1,
    "_faturami" => (int) 1
);

print_r($fatura);


$stmt = $conn->prepare("SELECT demo_id, SKU, serial_no FROM aa_erp_kt_demolar_skular WHERE demo_id = '$demo_no'");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $row) {
        $urunler[] = array(
            "_x_siparisNO" => "T10" . $row['demo_id'],
            "_x_teklif_no" => "T10" . $row['demo_id'],
            "kod" => $row['SKU'],
            "birim" => "Adet",
            "miktar" => 1,
            "birimFiyat" => 1.0,
            "kdvOran" => 20,
            "seriNo" => $row['serial_no'],
            "lisansSuresi" => "",
            "projeKodu" => "GENEL"
        );
    }

$fatura["products"] = $urunler;

trace($fatura);

// Eger daha once basilmissa; once sil; ------------------------
$dnn=(string) "T10" . $demo_no;
$sqlinsert = "DELETE FROM aa_erp_kt_fatura_i where siparisNO='$dnn'";
$stmt = $conn->prepare($sqlinsert);
$result = $stmt->execute();
$sqlinsert = "DELETE FROM aa_erp_kt_fatura_urunler_i where _x_siparisNO='$dnn'";
$stmt = $conn->prepare($sqlinsert);
$result = $stmt->execute();
// Eger daha once basilmissa; once sil; ------------------------

foreach ($urunler as $satir) {
    try {
        //unset($satir['projeKodu']);
        $str = u_sqlInsert($satir);
        $sqlinsert = "INSERT INTO aa_erp_kt_fatura_urunler_i $str";
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute($satir);
    } catch (PDOException $e) {
        BotMesaj($sqlinsert);
        BotMesaj($e->getMessage());
    }
}

try {
    unset($fatura["products"]);
    $str = u_sqlInsert($fatura);
    $sqlinsert = "INSERT INTO aa_erp_kt_fatura_i $str";
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute($fatura);
} catch (PDOException $e) {
    BotMesaj($sqlinsert);
    BotMesaj($e->getMessage());
}
?>
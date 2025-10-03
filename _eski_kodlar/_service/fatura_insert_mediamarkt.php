<?php

error_reporting(0);
ini_set("display_errors", false);
set_time_limit(600);
$port = "8081";

include '_func.php';

$trace = isset($_GET['trace']) ? (int) $_GET['trace'] : 0;
$fis_dur = isset($_GET['tip']) ? (int) $_GET['tip'] : "0";   //0 Irsaliye 1 Fatura

if ($trace === 1) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
}

include '../_conn.php';
include '../_conn_fm.php';

$siparis_no = $_GET['siparis_no'];

//FLOW Siparis Okuma
$url = "select * from aa_erp_kt_siparisler where SIPARIS_NO=:siparis_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$s = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$fatura_tar = isset($_GET['ft']) ? (string) $_GET['ft'] : "";
$fatura_tarihi = $s['FATURALAMA_TARIHI'];
$fatek1 = "";
$fatek2 = "";
if ($fatura_tar != "" || $fatura_tar != "--") {
    $fatek1 = ",FATURA_TARIHI";
    $fatek2 = ",'$fatura_tarihi'";
}

trace($s);

$teklif_no = $s['X_TEKLIF_NO'];

$url = "select * from aa_erp_kt_siparisler_urunler where X_SIPARIS_NO=:siparis_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$su = $stmt->fetchAll(PDO::FETCH_ASSOC);
trace($su);

$url = "select * from aa_erp_kt_firsatlar where FIRSAT_NO=:firsat_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['firsat_no' => $s['X_FIRSAT_NO']]);
$f = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
trace($f);

$stmt = $conn2->prepare("select LOGO_kullanici from TF_USERS where kullanici='" . $f['MUSTERI_TEMSILCISI'] . "'");
$stmt->execute();
$logo_kullanici = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['LOGO_kullanici'];

$url = "select * from aa_erp_kt_teklifler where TEKLIF_NO=:teklif_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$t = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
trace($t);

$url = "select * from aa_erp_kt_teklifler_urunler where X_TEKLIF_NO=:teklif_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$tu = $stmt->fetchAll(PDO::FETCH_ASSOC);
trace($tu);

//select top 1 usd,eur from aa_erp_kur order by tarih desc
$url = "select top 1 usd,eur from aa_erp_kur order by tarih desc";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute();
$kurlar = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($trace === 1) {
    echo "<table border=1>";
}
$say = 0;
$DOVIZKUR = 0;

$_SIPARISID = str_replace('T', '', $s['X_TEKLIF_NO']);
$_NO = $siparis_no;
$_CARI_KOD = $f['BAYI_CHKODU'];

//select ADRES1+' '+ADRES2+' '+ILCE+' '+SEHIR from aaa_erp_kt_bayiler b where CH_KODU=''
$url = "select ADRES1 + ' ' + ADRES2 + ' ' + ILCE + ' ' + SEHIR from aaa_erp_kt_bayiler b where CH_KODU=:ch_kodu";
$stmt = $conn->prepare($url);
$stmt->execute(['ch_kodu' => $_CARI_KOD]);
$fat_adres = $stmt->fetchAll(PDO::FETCH_COLUMN)[0];

$_MALZEMEKOD = $satir["SKU"];
$_BIRIM = "ADET";
$_MIKTAR = $satir["ADET"];
$_FIYAT = $satir["BIRIM_FIYAT"];
$_SATIS_TEMSILCISI = trim($f["MUSTERI_TEMSILCISI"]);
$_SERI_NO = $satir['LISANS'];
$_VADE = $t['VADE'];
$_Sevk_Adresi = $f['SEVKIYAT_ADRES'] . " " . $f['SEVKIYAT_ILCE'] . "/" . $f['SEVKIYAT_IL'];
$_DOVIZ_TUR = "1";
$_DOVIZKUR = "";
$_SevkiyatKime = $f['SEVKIYAT_KIME'];
$_Unvan = $f['BAYI_ADI'];
IF ($_VADE === "KKART") {
    $_VADE = "PEŞİN";
}
$_Adres1 = mb_substr($_Sevk_Adresi, 0, 50);
$_Adres2 = mb_substr($_Sevk_Adresi, 50, 50);
IF ($_SevkiyatKime == "0") {
    $KisiBilgi = trim($f['BAYI_YETKILI_ISIM']);
    $_SevkiyatKime = "Bayi";
    //Bayi ise Adres basma!
    $_Adres1 = "";
    $_Adres2 = "";
    $_Sevk_Adresi = "";
} else {
    $KisiBilgi = trim($f['MUSTERI_YETKILI_ISIM']);
    $_SevkiyatKime = "Müşteri";
}
// ? SOR! Ticket 320
$KisiBilgi = trim($f['MUSTERI_YETKILI_ISIM']);
$MusteriSiparisNo = $s['MUSTERI_SIPARIS_NO'];
$BayiMusteri = trim($f['MUSTERI_ADI']);
$LisansSuresi = $satir["SURE"];
$Ambar = ""; // Ambar!
//$BayiMusteri=filter_var($BayiMusteri, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
$say++;
$DOVIZTUR = $f['PARA_BIRIMI'];
IF ($DOVIZTUR == "TRY") {
    $DOVIZTUR = "TL";
}
if ($s['OZEL_KUR'] > 0) {
    $DOVIZKUR = $s['OZEL_KUR'];
}
if ($DOVIZKUR < 0 || $DOVIZKUR == "") {
    $DOVIZKUR = 0;
}
$_Unvan = mb_substr($_Unvan, 0, 50);

if ($trace == 1) {
    echo "<tr>";
    echo "<td>" . $_SIPARISID . "</td>";
    echo "<td>" . $_NO . "</td>";
    echo "<td>" . $_CARI_KOD . "</td>";
    echo "<td>" . $_MALZEMEKOD . "</td>";
    echo "<td>ADET</td>";
    echo "<td>" . $_MIKTAR . "</td>";
    echo "<td>" . $_FIYAT . "</td>";
    echo "<td>" . $logo_kullanici . "</td>";
    echo "<td>" . $_SERI_NO . "</td>";
    echo "<td>$fis_dur</td>";
    echo "<td>" . $say . "</td>";
    echo "<td>" . $_VADE . "</td>";
    echo "<td>" . $_Sevk_Adresi . "</td>";
    echo "<td>" . $_Unvan . "</td>";
    echo "<td>" . $_SevkiyatKime . "</td>";
    echo "<td>" . $_Adres1 . "</td>";
    echo "<td>" . $_Adres2 . "</td>";
    echo "<td>" . $MusteriSiparisNo . "</td>";
    echo "<td>" . $BayiMusteri . "</td>";
    echo "<td>" . $Hizmetmi . "</td>";
    echo "<td>" . $LisansSuresi . "</td>";
    echo "<td>" . $Ambar . "</td>";
    echo "</tr></table>!!!";
}

// Eger Okey gelirse Veritabanina Insert

$projeKodu = "GENEL"; //Kanal 4 mesela;

$date = date('Y-m-d');
$time = date("H:i:s");
$baskiTarihi = $date . "T" . $time . "Z";

if ($fatura_tar != "" || $fatura_tar != "--") {
    $dc = date_create($fatura_tar);
    $tdc = date_format($dc, "Y-m-d");
    $baskiTarihi = $tdc . "T" . $time . "Z";
}

$url = "select * from aa_erp_kt_fatura f where f.siparisNO =:siparis_no";
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$fat = $stmt->fetchAll(PDO::FETCH_ASSOC);

///////////////////
//FLOW Gidecek Array Irsaliye
$fatura = array(
    "_teklif_no" => (string) $teklif_no,
    "siparisNO" => (string) $_NO,
    "irsaliyeTarihi" => (string) $baskiTarihi,
    "faturaTarihi" => (string) $baskiTarihi,
    "cariKod" => (string) $_CARI_KOD,
    "projeKodu" => (string) $projeKodu,
    "vadeKodu" => (string) $_VADE,
    "satisElemanKodu" => (string) $logo_kullanici,
    "dovizTuru" => (string) $DOVIZTUR,
    "dovizKuru" => (double) $DOVIZKUR,
    "unvan" => mb_substr(trim((string) $BayiMusteri), 0, 50),
    "kisiBilgi" => trim((string) $KisiBilgi),
    "adres" => trim((string) $_Sevk_Adresi),
    "musteriSiparisNo" => trim((string) $MusteriSiparisNo),
    "ambarKodu" => (int) $Ambar,
    "_faturami" => (int) $fis_dur
);

foreach ($su as $key => $satir) {
    $_MALZEMEKOD = $satir["SKU"];
    $_BIRIM = "ADET";
    $_MIKTAR = $satir["ADET"];
    $_FIYAT = $satir["BIRIM_FIYAT"];
    $_SERI_NO = $satir['LISANS'];
    $_DOVIZ_TUR = "1";
    $_DOVIZKUR = "";
    $malzemeTip = $satir["TIP"];
    $LisansSuresi = $satir["SURE"];
    $DOVIZTUR = $f['PARA_BIRIMI'];
    IF ($DOVIZTUR == "TRY") {
        $DOVIZTUR = "TL";
    }
    if ($s['OZEL_KUR'] > 0) {
        $DOVIZKUR = $s['OZEL_KUR'];
    }
    if ($DOVIZKUR < 0 || $DOVIZKUR == "") {
        $DOVIZKUR = 0;
    }
    if ($malzemeTip == "Komtera") {
        $malzemeTip = 2;
    } else {
        $malzemeTip = 1;
    }
    if ($malzemeTip == 1) {
        //FLOW Gidecek Array Urunler
        $urunler[] = array("_x_siparisNO" => (string) $_NO,
            "_x_teklif_no" => (string) $teklif_no,
            "kod" => (string) $_MALZEMEKOD,
            //"malzemeTip" => (string) $malzemeTip,
            "birim" => $_BIRIM,
            "miktar" => (int) $_MIKTAR,
            "birimFiyat" => (double) $_FIYAT,
            "kdvOran" => 18,
            "seriNo" => (string) $_SERI_NO,
            "lisansSuresi" => (string) $LisansSuresi,
            "projeKodu" => (string) $projeKodu,
        );
    } else {
        $hizmetler[] = array("_x_siparisNO" => (string) $_NO,
            "_x_teklif_no" => (string) $teklif_no,
            "kod" => (string) $_MALZEMEKOD,
            //"malzemeTip" => (string) $malzemeTip,
            "birim" => $_BIRIM,
            "miktar" => (int) $_MIKTAR,
            "birimFiyat" => (double) $_FIYAT,
            "kdvOran" => 18,
            "seriNo" => (string) $_SERI_NO,
            "lisansSuresi" => (string) $LisansSuresi,
            "projeKodu" => (string) $projeKodu,
        );
    }
}
$fatura["products"] = $urunler;

trace($fatura);

echo "---------------------------\n";
echo "---------------------------\n";

trace($fat);

echo "---------------------------\n";
echo "---------------------------\n";

foreach ($urunler as $satir) {
    try {
        unset($satir['projeKodu']);
        $str = u_sqlInsert($satir);
        $sqlinsert = "INSERT INTO aa_erp_kt_fatura_urunler_i $str";
        trace($sqlinsert);
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute($satir);
    } catch (PDOException $e) {
        trace($sqlinsert);
        trace($e->getMessage());
        BotMesaj($sqlinsert);
        BotMesaj($e->getMessage());
    }
}

try {
    unset($fatura["products"]);
    //$fatura['r_LogoId'] = $fisID;
    //$fatura['r_FisNo'] = $fisNO;
    $str = u_sqlInsert($fatura);
    $sqlinsert = "INSERT INTO aa_erp_kt_fatura_i $str";
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute($fatura);
    trace($sqlinsert);
} catch (PDOException $e) {
    trace($sqlinsert);
    trace($e->getMessage());
    BotMesaj($sqlinsert);
    BotMesaj($e->getMessage());
}
?>

<?php

$port = "8081";

include "../_conn.php";
include '_func.php';
include '../mail.php';

error_reporting(0);
ini_set("display_errors", false);
set_time_limit(600);

$say = 0;

$kacfatura = 0;

$date = isset($_GET['ft']) ? $_GET['ft'] : "";
if ($date == "" || $date == "--") {
    $date = date('Y-m-d');
} else {
    $date_object = DateTime::createFromFormat("Y-m-d", $date);
    $new_date_format = $date_object->format("Y-m-d");
    $date = $new_date_format;
}
$time = date("H:i:s");
$baskiTarihi = $date . "T" . $time . "Z";

$sqlstring = "select CHKODU from aa_erp_kt_fatura_kes_sophos group by CHKODU";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$bayiler = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($bayiler as $bay) {
    $bayyaz=$bay['CHKODU'];
    $url = <<<DATA
select sf.id,sf.CHKODU cariKod,sf.vade vadeKodu,MT satisElemanKodu,b.CH_UNVANI unvan,FATURA_NOT kisiBilgi ,b.ADRES1 + ' ' + b.ADRES2 adres,
sf.SKU kod,sf.ADET miktar,BIRIM birimFiyat
from aa_erp_kt_fatura_kes_sophos sf
left join aaa_erp_kt_bayiler b ON b.CH_KODU =sf.CHKODU
where CHKODU='$bayyaz'
DATA;
    $stmt = $conn->prepare($url);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $urunler = array();
    $fatura = "";
    $id = $data[0]['id'];
    $teklifno = (string)"T400" . $id;
    $siparisno = (string)"T400" . $id;
    $fatura = array(
        "_teklif_no" => $teklifno,
        "_faturami" => '1',
        "siparisNO" => $siparisno,
        "faturaTarihi" => (string)$baskiTarihi,
        "cariKod" => (string)$data[0]['cariKod'],
        "projeKodu" => (string)'GENEL',
        "vadeKodu" => (string)$data[0]['vadeKodu'],
        "satisElemanKodu" => $data[0]['satisElemanKodu'],
        "dovizTuru" => (string)"TL",
        "unvan" => (string)$data[0]['kisiBilgi'], //(string)$data[0]['unvan'],
        "kisiBilgi" => "",
        "adres" => "", //trim((string)$data[0]['adres']),
        "ambarKodu" => (int)0
    );
    try {
        $say++;
        unset($fatura['products']);
        $str = u_sqlInsert($fatura);
        $sqlinsert = "INSERT INTO aa_erp_kt_fatura_i $str";
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute($fatura);
        //echo ($sqlinsert) . "\n";
    } catch (PDOException $e) {
        echo "HATA!";
        echo ($sqlinsert) . "\n";
        echo($e->getMessage());
        BotMesaj($sqlinsert . "----" . $e->getMessage());
    }
    for ($t = 0; $t < count($data); $t++) {
        $data[$t]['belge_no'] = "T400" . $id;
        $urunler = array(
            "kod" => (string)$data[$t]['kod'],
            "malzemeTip" => (int)1,
            "birim" => "ADET",
            "miktar" => (int)$data[$t]['miktar'],
            "birimFiyat" => (double)$data[$t]['birimFiyat'],
            "kdvOran" => 20,
            "seriNo" => (string)"",
            "lisansSuresi" => (string)"",
            "projeKodu" => (string)'GENEL'
        );
        try {
            $urunler['_x_teklif_no'] = $teklifno;
            $urunler['_x_siparisNo'] = $siparisno;
            $str = u_sqlInsert($urunler);
            $sqlinsert = "INSERT INTO aa_erp_kt_fatura_urunler_i $str";
            //echo ($sqlinsert) . "\n";
            $stmt = $conn->prepare($sqlinsert);
            $result = $stmt->execute($urunler);
        } catch (PDOException $e) {
            echo "HATA!";
            echo ($sqlinsert) . "\n";
            echo($e->getMessage());
            BotMesaj($sqlinsert . "----" . $e->getMessage());
        }
    }
}
echo $say;

?>
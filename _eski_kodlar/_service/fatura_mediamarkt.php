<?php

/*

1. unvan
2. kisibBilgi
3. Adres
4. Adres
5. Musteri Siparis No

istenen:

1.Satır / Müşteri Adı / mağaza
2.Satır / Yetkili Kişi Adı / detay
3.Satır ve 4. Satır / Sevk Adresi / adres
5.Satır / Sipariş No / belge_no

 */

$port="8081";
include "../_conn.php";
include '_func.php';
include '../mail.php';

error_reporting(E_ALL);
ini_set("display_errors", true);
set_time_limit(60000);
$say=0;
$kacfatura=0;
$date = isset($_GET['ft']) ? $_GET['ft'] : "";

$dl = isset($_GET['dl']) ? $_GET['dl'] : "";

if ($date == "" || $date=="--") {
    $date = date('Y-m-d');
} else {
    $date_object = DateTime::createFromFormat("Y-m-d", $date);
    $new_date_format = $date_object->format("Y-m-d");
    $date=$new_date_format;
}
$time = date("H:i:s");
$baskiTarihi = $date . "T" . $time . "Z";
$sqlstring="select magaza from aa_erp_kt_mediamarkt_faturalama group by magaza";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$magazalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($magazalar as $satir) {
    $magaza=$satir['magaza'];
    $url = "select id,
	   magaza,
(select VADE from aaa_erp_kt_bayiler b where b.CH_KODU='120.03.01.0472') as vade,
-- (select b.CH_UNVANI from aaa_erp_kt_bayiler b where b.CH_KODU='120.03.01.0472') as unvan,
	   concat(ADRES,' ',sehir) as adres,
	   sku,
	   adet,
	   dummy1,
	   dummy2,
	   birim_fiyat,
           belge_no
from aa_erp_kt_mediamarkt_faturalama where magaza='$magaza'
order by id
";
    $stmt = $conn->prepare($url);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fatura Bilgileri!
    $id = $data[0]['id'];
    $magaza = $data[0]['magaza'];
    //$data[0]['belge_no'] = "T200" . $id;

    $teklifno=(string)"T200" . $id;
    $siparisno=(string)"T200" . $id;

    $fatura = array(
        "_teklif_no" => $teklifno,
        "_faturami" => '1',
        "siparisNO" => $siparisno,
        "faturaTarihi" => (string)$baskiTarihi,
        "cariKod" => (string)'120.03.01.0472',
        "projeKodu" => (string)'KANAL4',
        "musteriSiparisNo" => (string)$data[0]['belge_no'],
        "vadeKodu" => (string)$data[0]['vade'],
        "satisElemanKodu" => 'BARIŞ ÇORBACI',
        "dovizTuru" => (string)"TL",
        "unvan" => (string)$data[0]['magaza'],
        "kisiBilgi" => $dl,
        "adres" => trim((string)$data[0]['adres']),
        "ambarKodu" => (int)0
    );

    print_r($fatura);
    //FLOW Fatura SQL Insert
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

    print_r($data);





    for ($t = 0; $t < count($data); $t++) {
        $urunler = array(
            "kod" => (string)$data[$t]['sku'],
            "malzemeTip" => (int)1,
            "birim" => "ADET",
            "miktar" => (int)$data[$t]['adet'],
            "birimFiyat" => (double)$data[$t]['birim_fiyat'],
            "kdvOran" => 20,
            "seriNo" => (string)"",
            "lisansSuresi" => (string)"",
            "projeKodu" => (string)'KANAL4',
            "dummy1" => (string)$data[$t]['dummy1'],
            "dummy2" => (string)$data[$t]['dummy2']
        );
        //$satir=$data[$t];
        try {
            $urunler['_x_teklif_no']=$teklifno;
            $urunler['_x_siparisNo']=$siparisno;

            $str = u_sqlInsert($urunler);
            $sqlinsert = "INSERT INTO aa_erp_kt_fatura_urunler_i $str";
            //echo ($sqlinsert) . "\n";
            $stmt = $conn->prepare($sqlinsert);
            $result = $stmt->execute($urunler);
        } catch (PDOException $e) {
            echo "HATA!";
            echo ($sqlinsert) . "\n";
            echo ($e->getMessage());
            BotMesaj($sqlinsert . "----" . $e->getMessage());
        }
    }

}
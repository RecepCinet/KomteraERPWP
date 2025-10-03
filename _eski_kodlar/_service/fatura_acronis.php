<?php

$port="8081";

include "../_conn.php";
include '_func.php';
include '../mail.php';

error_reporting(0);
ini_set("display_errors", false);
set_time_limit(600);

$say=0;

$kacfatura=0;

$date = isset($_GET['ft']) ? $_GET['ft'] : "";
if ($date == "" || $date=="--") {
    $date = date('Y-m-d');
} else {
    $date_object = DateTime::createFromFormat("Y-m-d", $date);
    $new_date_format = $date_object->format("Y-m-d");
    $date=$new_date_format;
}
$time = date("H:i:s");
$baskiTarihi = $date . "T" . $time . "Z";

$sqlstring="select acronis_bayi magaza from aa_erp_kt_acronis_fatura_kes group by acronis_bayi";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$magazalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($magazalar as $satir) {
    $magaza=$satir['magaza'];
    $url = "select
	temp.id,
	temp.acronis_bayi unvan,
	temp.VADE vade,
	b.CH_UNVANI unvan,
	CONCAT(b.ADRES1,b.ADRES2) adres,
	b.CH_KODU,
	temp.SKU kod,
	'DLR' doviz_turu,
	temp.adet miktar,
        e.musteri_temsilcisi MT,
	temp.birim birimFiyat
from aa_erp_kt_acronis_fatura_kes temp
left join aa_erp_kt_Acronis_CH_Eslesme e ON e.acronis_bayi_adi = temp.acronis_bayi
left join aaa_erp_kt_bayiler b ON e.komtera_bayi_adi  = b.CH_UNVANI 
left join aa_erp_kt_fiyat_listesi f ON f.sku  = temp.sku 
where e.komtera_bayi_adi is not null
AND temp.acronis_bayi='$magaza'
order by temp.acronis_bayi;
";

    $stmt = $conn->prepare($url);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $urunler = array();
    $fatura = "";

    // Fatura Bilgileri!
    $id = $data[0]['id'];
    $magaza = $data[0]['unvan'];
    $data[0]['belge_no'] = "T200" . $id;

    $teklifno=(string)"T200" . $id;
    $siparisno=(string)"T200" . $id;

    $fatura = array(
        "_teklif_no" => $teklifno,
        "_faturami" => '1',
        "siparisNO" => $siparisno,
        "faturaTarihi" => (string)$baskiTarihi,
        "cariKod" => (string)$data[0]['CH_KODU'],
        "projeKodu" => (string)'GENEL',
        "vadeKodu" => (string)$data[0]['vade'],
        "satisElemanKodu" => $data[0]['MT'],
        "dovizTuru" => (string)"USD",
        "unvan" => (string)$data[0]['unvan'],
        "kisiBilgi" => (string)"",
        "adres" => trim((string)$data[0]['adres']),
        "ambarKodu" => (int)0
    );

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
        echo($sqlinsert) . "\n";
        echo($e->getMessage());
        BotMesaj($sqlinsert . "----" . $e->getMessage());
    }

    for ($t = 0; $t < count($data); $t++) {
        $data[$t]['belge_no'] = "T200" . $id;
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

echo $say;

?>
<?php

error_reporting(E_ALL);
ini_set("display_errors", true);

$timezzone="+03:00";

function temizle($metin) {
    $metin=str_replace("\n", " ",$metin);
    $metin=str_replace("\r", " ",$metin);
    $metin=str_replace("  ", " ",$metin);
    $metin=str_replace("  ", " ",$metin);
    $metin=str_replace("  ", " ",$metin);
    $metin=str_replace("  ", " ",$metin);
    $metin=str_replace("  ", " ",$metin);
    $kabulEdilenKarakterler = "a-zA-Z0-9\s\!\@\#\$\%\^\&\*\(\)\-\=\_\+\,\.\/\<\>\?\;\:\'\"[\]\{\}\\\|\üÜöÖıİşŞçÇğĞ";
    $temizMetin = preg_replace("/[^$kabulEdilenKarakterler]/u", '', $metin);
    return $temizMetin;
}

set_time_limit(600);
$port="";
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_service\_func.php';
include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn.php';
$token="";

//LogYazdir("basladi." . date("Y-m-d H:i:s"));

function CurlGonder($url,$arr) {
    global $token;
    TokTok();
    $curl = curl_init();
    //$url = "http://172.16.85.107:$port/api/Dispatch/Sales/Insert";
    $headers = array(
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    );
    $json_data = json_encode($arr,JSON_UNESCAPED_UNICODE);
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 600,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $json_data,
        CURLOPT_HTTPHEADER => $headers
    ));
    LogYazdir($json_data);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        LogYazdir($url. "\n" . print_r($json_data,true) . "\n" . $err);
        return $err;
    } else {
        echo $response;
    }
    LogYazdir($url. "\n" . print_r($json_data,true) . "\n" . $response);
    return $response;
}

function TokTok() {
    global $token;
    if ($token=="") {
        $token = Token();
        if (strlen($token) < 32) {
            LogYazdir("Web Service Token Alinmadi!: " . $token);
            die();
        }
    }
    return $token;
}






$islem=0;

//FLOW: 1 - Irsaliye:
echo "1 - Basilmamis Irsaliyeyi Irsaliye Bas!\n\n";
$url = "select top 1 * from aa_erp_kt_fatura_i f where f.siparisNo is not null AND f._faturami=0 AND f._status_i=0";
$stmt = $conn->prepare($url);
$stmt->execute();
$basilacak_irsaliyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($basilacak_irsaliyeler)>0) {
    $irsaliye=$basilacak_irsaliyeler[0];
    $irsaliye['faturaTarihi']=str_replace(" " , "T", $irsaliye['faturaTarihi'])     . $timezzone;
    $irsaliye['irsaliyeTarihi']=str_replace(" " , "T", $irsaliye['irsaliyeTarihi']) . $timezzone;
    $url2 = "select kod,malzemeTip,birim,miktar,birimFiyat,kdvOran,seriNo,lisansSuresi,projeKodu,dummy1 as aciklama3 from aa_erp_kt_fatura_urunler_i fu where fu._x_siparisNo=:siparis_no -- and hizmet=0";
    $stmt = $conn->prepare($url2);
    $stmt->execute(['siparis_no' => $irsaliye['siparisNo']]);
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $irsaliye['products']=$urunler;
    $id=$irsaliye['id'];
    unset($irsaliye['id']);
    unset($irsaliye['_cd']);
    unset($irsaliye['faturaTarihi']);
    unset($irsaliye['_teklif_no']);
    unset($irsaliye['_faturami']);
    unset($irsaliye['_status_i']);
    unset($irsaliye['_status_f']);
    unset($irsaliye['r_FisNo']);
    unset($irsaliye['r_LogoId']);
    unset($irsaliye['r_result']);
    unset($irsaliye['r_response']);
    print_r($irsaliye);
    $islem=1;
    $sonuc_json=CurlGonder("http://172.16.85.77/api/Dispatch/Sales/Insert",$irsaliye);
    $arr = json_decode($sonuc_json, true);
    print_r($arr);
    $STATUS=1;
    $result = $arr['result'];
    $response = $arr['response'];
    if ($result=="error") {
        $LogoId="";
        $fisNO="";
    } else {
        $res=json_decode($response,true);
        $LogoId=$res['LogoId'];
        $fisNO=$res['FisNo'];
    }

    if ($response=="Logo servisinden token alınamadı." || $response=="Value cannot be null. (Parameter 'source')" || $response=='{"Message":"The request is invalid.","ModelState":{"LoginError":["Logo Object is not connected"]}}') {
        $STATUS=0;
    }
    $qua_arr=array(
        ':id' => $id,
        ':result' => $result,
        ':response' => $response
    );
    print_r($qua_arr);
    $up_st = $conn->prepare("update aa_erp_kt_fatura_i set _status_i='$STATUS',r_LogoId='$LogoId',r_FisNo='$fisNO',r_result=:result,r_response=:response where id=:id");
    $up_st->execute($qua_arr);
}










if ($islem==0) {
//FLOW: 2- Fatura:
    echo "2 - Basilmamis Faturalari Fatura Bas!\n\n";
    $url = "select top 1 * from aa_erp_kt_fatura_i f where f.siparisNo is not null AND f._faturami=1 AND f._status_f=0";
    $stmt = $conn->prepare($url);
    $stmt->execute();
    $basilacak_irsaliyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($basilacak_irsaliyeler) > 0) {
        $fatura = $basilacak_irsaliyeler[0];
        $fatura['faturaTarihi'] = str_replace(" ", "T", $fatura['faturaTarihi'])     . $timezzone;
        $fatura['irsaliyeTarihi'] = str_replace(" ", "T", $fatura['irsaliyeTarihi']) . $timezzone;
        $url2 = "select kod,malzemeTip,birim,miktar,birimFiyat,kdvOran,seriNo,lisansSuresi,projeKodu,dummy1 as aciklama3 from aa_erp_kt_fatura_urunler_i fu where fu._x_siparisNo=:siparis_no -- and hizmet=0";
        $stmt = $conn->prepare($url2);
        $stmt->execute(['siparis_no' => $fatura['siparisNo']]);
        $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $fatura['products'] = $urunler;
        $id = $fatura['id'];
        unset($fatura['id']);
        unset($fatura['_cd']);
        unset($fatura['irsaliyeTarihi']);
        unset($fatura['_teklif_no']);
        unset($fatura['_faturami']);
        unset($fatura['_status_i']);
        unset($fatura['_status_f']);
        unset($fatura['r_FisNo']);
        unset($fatura['r_LogoId']);
        unset($fatura['r_result']);
        unset($fatura['r_response']);
        // Tire gondermesin diye yapilan degisiklik!
        //$fatura['siparisNo'] = $basilacak_irsaliyeler[0]['_teklif_no'];
        print_r($fatura);
        $islem=1;
        $sonuc_json = CurlGonder("http://172.16.85.77/api/Invoice/Sales/Insert", $fatura);
        $arr = json_decode($sonuc_json, true);
        print_r($arr);
        $STATUS = 1;
        $result = $arr['result'];
        $response = $arr['response'];
        if ($result == "error") {
            $LogoId = "";
            $fisNO = "";
        } else {
            $res = json_decode($response, true);
            $LogoId = $res['LogoId'];
            $fisNO = $res['FisNo'];
        }
        if ($response == "Logo servisinden token alınamadı." || $response == "Value cannot be null. (Parameter 'source')" || $response == '{"Message":"The request is invalid.","ModelState":{"LoginError":["Logo Object is not connected"]}}') {
            $STATUS = 0;
            LogYazdir($fatura);
            LogYazdir($response);  //
        }
        $qua_arr = array(
            ':id' => $id,
            ':result' => $result,
            ':response' => $response
        );
        print_r($qua_arr);
        $up_st = $conn->prepare("update aa_erp_kt_fatura_i set r_LogoId='$LogoId',r_FisNo='$fisNO',_status_f='$STATUS',r_result=:result,r_response=:response where id=:id");
        $up_st->execute($qua_arr);
    }

}







if ($islem==0) {
//FLOW: 3- Irsaliye Listesi Gonderme:
    echo "3 - Basilmis Irsaliyeleri Fatura Bas!\n\n";
    $url = "select top 1 * from aa_erp_kt_fatura_i f where f.projeKodu='FATURA' AND r_result is null";
    $stmt = $conn->prepare($url);
    $stmt->execute();
    $irsaliyelistesi = $stmt->fetchAll(PDO::FETCH_ASSOC);

    print_r($irsaliyelistesi);

    if (count($irsaliyelistesi)) {
        $id = $irsaliyelistesi[0]['id'];
        $irs = $irsaliyelistesi[0]['unvan'];

        //hemen kapa:
        $idd = $irsaliyelistesi[0]['id'];
        $up_st = $conn->prepare("update aa_erp_kt_fatura_i set r_result='to' where id='$idd'");
        $up_st->execute();

        $arr_ids = explode(",", $irs);

        $url = "select top 1 * from aa_erp_kt_fatura_i f where [_faturami]='0' AND r_LogoId='$arr_ids[0]'";
        $stmt = $conn->prepare($url);
        $stmt->execute();
        $bilgi = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

        $bilgi['faturaTarihi']=str_replace(" " , "T", $irsaliyelistesi[0]['faturaTarihi'])     . $timezzone;
        $bilgi['irsaliyeTarihi']=str_replace(" " , "T", $irsaliyelistesi[0]['irsaliyeTarihi']) . $timezzone;
        $baskiTarihi=$bilgi['faturaTarihi'];

        $url4 = "select kod,malzemeTip,birim,miktar,birimFiyat,kdvOran,seriNo,lisansSuresi,projeKodu,dummy1 as aciklama3 from aa_erp_kt_fatura_urunler_i fu where fu._x_siparisNo=:siparis_no -- and hizmet=1";
        echo $url4 . "\n";
        $stmt = $conn->prepare($url4);
        $stmt->execute(['siparis_no' => $bilgi['siparisNo']]);
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $bas = array(
            "faturaTarihi" => $baskiTarihi,
            "siparisNo" => $bilgi['_teklif_no'],
            "musteriSiparisNo" => $bilgi['musteriSiparisNo'],
            "adres" => temizle($bilgi['adres']),
            "unvan" => $bilgi['unvan'],
            "kisiBilgi" => temizle($bilgi['kisiBilgi']),
            "irsaliyeIdListesi" => $arr_ids,
            "services" => $services
        );
        print_r($bas);
        $islem=1;
        $sonuc_json = CurlGonder("http://172.16.85.77/api/Dispatch/DispatchBilling", $bas);
        $arr = json_decode($sonuc_json, true);
        print_r($arr);
        $STATUS = 1;
        $result = $arr['result'];
        $response = $arr['response'];
        if ($result == "error") {
            $LogoId = "";
            $fisNO = "";
        } else {
            $res = json_decode($response, true);
            $LogoId = $res['LogoId'];
            $fisNO = $res['FisNo'];
        }
        if ($response == "Logo servisinden token alınamadı." || $response == "Value cannot be null. (Parameter 'source')" || $response == '{"Message":"The request is invalid.","ModelState":{"LoginError":["Logo Object is not connected"]}}') {
            $STATUS = 0;
        }
        $qua_arr = array(
            ':id' => $id,
            ':result' => $result,
            ':response' => $response
        );
        print_r($qua_arr);
        $up_st = $conn->prepare("update aa_erp_kt_fatura_i set r_LogoId='$LogoId',r_FisNo='$fisNO',_status_f='$STATUS',r_result=:result,r_response=:response where id=:id");
        $up_st->execute($qua_arr);
    }
}

echo "!!! Bitti !!!";

// LogYazdir("bitti." . date("Y-m-d H:i:s"));

include '@engine_demo.php';

?>
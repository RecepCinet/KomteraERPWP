<form method="GET" action="">
    Teklif No: (T10111 gibi): <input type="text" name="teklif_no">
    <input type="submit" name="submit" value="Muhasebe Fisi Olustur">
</form>
<?php

error_reporting(E_ALL);
ini_set("display_errors", true);
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

$teklif_no=isset($_GET['teklif_no']) ? $_GET['teklif_no'] : '';

if ($teklif_no=="") {
    die();
}

$url = "select top 1 * from aa_erp_kt_fatura_i f where f._teklif_no='$teklif_no'";
$stmt = $conn->prepare($url);
$stmt->execute();
$basilacak_fatura = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fatura = $basilacak_fatura[0];
    $fatura['faturaTarihi'] = str_replace(" ", "T", $fatura['faturaTarihi']) . "Z";
    $fatura['irsaliyeTarihi'] = str_replace(" ", "T", $fatura['irsaliyeTarihi']) . "Z";
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

    echo "<br/>------------------------------------------<br/>";
    print_r($fatura);
    echo "<br/>------------------------------------------<br/>";

    $islem=1;
    $sonuc_json = CurlGonder("http://172.16.85.77/api/Invoice/Sales/InsertFiche", $fatura);
    $arr = json_decode($sonuc_json, true);
    print_r($arr);

?>
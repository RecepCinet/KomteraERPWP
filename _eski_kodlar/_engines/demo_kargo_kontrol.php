<?php


//$bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=" . urlencode( "DemoKargoKontrol!"));

error_reporting(E_ALL);
ini_set("display_errors",true);

//kucukbakkalkoy
include '../_conn.php';
include '../_demo_mail_function.php';
include '../_conn_fm.php';
        
$sql = "select * from aa_erp_kt_demolar where SIL='0' AND (KARGO_DURUM='Kargo Teslimattadır.' or KARGO_DURUM='Kargo İşlem Görmemiş.') AND CD >= DATEADD(day, -10, GETDATE())";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($data);

for ($t = 0; $t < count($data); $t++) {
    $keys = $data[$t]['KARGO_NO'];
    $id = $data[$t]['id'];
    $string = "http://127.0.0.1/kt_yurtici.php?cmd=gonderi_durum&cargoKey=" . $keys;
    $sor = file_get_contents($string);

    $sindiki_durum = $data[$t]["KARGO_DURUM"];

    //$sor='Kargo İşlem Görmemiş|||||||';
    //$sor='Kargo Teslimattadır.|916388116038|BİLKENT|https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=916388116038||ÜNİVERSİTELER MAHALLESİ 1596 CADDE NO:6 E/3 ÇANKAYA/ ANKARA Ankara||';
    //$sor='Kargo teslim edilmiştir.|916388121678|ÇAMDİBİ|https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code=916388121678|ERSİN CAN|KAZIMDIRIK MH 283 SK.NO:1 D:22 K:2-3 BORNOVA / İZMİR BORNOVA - İZMİR İzmir|20200706|103331';

    $gelen = explode("|", $sor);

    $kargo_durum = $gelen[0];
    $kargo_url = $gelen[3];

    if ($sindiki_durum != $kargo_durum) {


        $str_email = 'select ePosta from TF_USERS where company like \'%Komtera%\' AND kullanici=\'' . $data[$t]["MUSTERI_TEMSILCISI"] . '\'';
        $stmt_email = $conn2->prepare($str_email);
        $stmt_email->execute();
        $user_data = $stmt_email->fetchAll(PDO::FETCH_ASSOC)[0]["ePosta"];

        $arr = Array();
        $arr["mt"] = $data[$t]["MUSTERI_TEMSILCISI"];
        $arr["mt_eposta"] = $data[$t]["MUSTERI_TEMSILCISI"];
        $arr["kime"] = $data[$t]["BAYI_EPOSTA"];
        $arr["kime_isim"] = $data[$t]["BAYI_YETKILI"];
        $arr["kime"] = $data[$t]["BAYI_EPOSTA"];
        $arr["kime_isim"] = $data[$t]["BAYI_YETKILI"];
        $arr["cc"] = $data[$t]["MUSTERI_EPOSTA"];
        $arr["marka"] = $data[$t]["MARKA"];
        $arr["model"] = $data[$t]["ACIKLAMA"];
        $arr["serinumara"] = $data[$t]["SERIAL_NO"];
        $arr["gonderikodu"] = $gelen[1];
        $arr["varissubesi"] = $gelen[2];
        $arr["kargourl"] = $gelen[3];
        $arr["teslimatadresi"] = $gelen[5];

        if ($kargo_durum == "Kargo Teslimattadır.") {
            $upstat = $conn->prepare("update aa_erp_kt_demolar set KARGO_URL=:kargo_url,KARGO_DURUM=:kargo_durum where id=:id");
            $upstat->execute(array(
                ':id' => $id,
                ':kargo_durum' => $kargo_durum,
                ':kargo_url' => $kargo_url
            ));
            $arr["cmd"] = "Durum1";
            demo_mail_gonder($arr);
        }

        if ($kargo_durum == "Kargo teslim edilmiştir.") {
            $upstat = $conn->prepare("update aa_erp_kt_demolar set DEMO_DURUM='2',KARGO_URL=:kargo_url,KARGO_DURUM=:kargo_durum where id=:id");
            $upstat->execute(array(
                ':id' => $id,
                ':kargo_durum' => $kargo_durum,
                ':kargo_url' => $kargo_url
            ));
            $arr["cmd"] = "Durum2";
            $arr['teslimalan']=$gelen[4];
            $arr['teslimatzamanlama']=$gelen[6] . " " . $gelen[7];
            demo_mail_gonder($arr);
        }
    }
}
?>

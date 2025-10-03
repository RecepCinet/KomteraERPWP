<?php
error_reporting(E_ERROR);
ini_set("display_errors", true);
include '../_conn.php';

error_reporting(E_ERROR);
ini_set("display_errors", true);

$string="select s.SIPARIS_NO from aa_erp_kt_siparisler s LEFT JOIN ARYD_FIS_AKTARIM a ON a.[NO]=s.SIPARIS_NO WHERE s.SIPARIS_DURUM<>2 AND a.SONUC=4";
$stmt = $conn->prepare($string);
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($gelen as $key => $value) {
    $siparis_no = $value["SIPARIS_NO"];
    $url="update aa_erp_kt_siparisler set SIPARIS_DURUM='2',FATURALAMA_TARIHI=GETDATE(),SIPARIS_DURUM_ALT=null where SIPARIS_NO='$siparis_no'";
    echo $url . "\n";
    $stmt = $conn->prepare($url);
    $stmt->execute();
}

//
//$string="select s.SIPARIS_NO,year(a.CreateDate) as CD
//from aa_erp_kt_siparisler s LEFT JOIN ARYD_FIS_AKTARIM a ON a.[NO]=s.SIPARIS_NO
//where s.FATURALAMA_TARIHI is null";
//$stmt = $conn->prepare($string);
//$stmt->execute();
//$gelen2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//foreach ($gelen2 as $key => $value) {
//    $siparis_no = $value["SIPARIS_NO"];
//    $ne=$value['CD'];
//    $url="update aa_erp_kt_siparisler set FATURALAMA_TARIHI=$ne where SIPARIS_NO='$siparis_no'";
//    echo $url;
//    $stmt = $conn->prepare($url);
//    $stmt->execute();
//}

// DEMOLAR:
//$bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=Calisti");

//$string="select * from aa_erp_kt_demolar where (IRSALIYE_NO is null or IRSALIYE_NO='...bekleniyor' or IRSALIYE_NO='') AND SIL<>'1'";
//$stmt = $conn->prepare($string);
//$stmt->execute();
//$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//
//print_r($gelen);
//
//$ne_olacak=$gelen[0]['ELDEN_TESLIM_MI'];
//
//$yap=$gelen[0]['DEMO_DURUM'];
//if ($ne_olacak==="1") {
//    $yap=5;
//}
//
//
//
//
//for ($t=0;$t<count($gelen);$t++) {
//    $id=$gelen[$t]['id'];
//    $string2="select top 1 * from ARYD_FIS_AKTARIM where [NO]='T100$id' AND SONUC='4'";
//    echo $string2 . "\n";
//    $stmt = $conn->prepare($string2);
//    $stmt->execute();
//    $gelen2 = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
//
//    print_r($gelen2);
//
//    echo "---------------------\n";
//    echo $gelen2['MESAJ'] . "\n";
//    echo "---------------------\n";
//
//    $fat=substr($gelen2['MESAJ'],21,16);
//
//    $sql_update="update aa_erp_kt_demolar set IRSALIYE_NO='$fat',DEMO_DURUM='$yap' where id='$id'";
//    echo $sql_update;
//    $stmt = $conn->prepare($sql_update);
//    $stmt->execute();
//
//    if (!$stmt) {
//        echo "\nPDO::errorInfo():\n";
//        print_r($stmt->errorInfo());
//    }
//
//}

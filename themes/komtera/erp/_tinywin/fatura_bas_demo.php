<?php
error_reporting(E_ALL);
ini_set("display_errors",true);


/// IPTAL OLACAK! ----------------------------------------------------------------------------------------


$demo_id=$_GET['demo_id'];

include "../_conn.php";
include "../_conn_fm.php";

$url = "select * from " . getTableName('aa_erp_kt_demolar') . " d where id=:id";
$stmt = $conn->prepare($url);
$stmt->execute(['id' => $demo_id]);
$demo = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($demo['MUSTERI_TEMSILCISI']==="recep.cinet") {
    $demo['MUSTERI_TEMSILCISI']="koray.bul";
}

//print_r($demo);

$stmt = $conn2->prepare("select LOGO_kullanici from TF_USERS where kullanici='" . $demo['MUSTERI_TEMSILCISI'] . "'");
$stmt->execute();
$logo_kullanici = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['LOGO_kullanici'];

//print_r($logo_kullanici);

$_SIPARISID="100" . $demo_id;
$siparis_no="T100" . $demo_id;
$_CARI_KOD=$demo['BAYI_CHKODU'];
$_MALZEMEKOD=$demo['SKU'];
$_MIKTAR="1";
$_FIYAT="1";
//$logo_kullanici=""; direk geliyor yukarida!
$_SERI_NO=$demo['SERIAL_NO'];
$fis_dur="2";
$say="1";
$_VADE="PEŞİN";
$_Sevk_Adresi=$demo['ADRES'];
$DOVIZKUR="";
$DOVIZTUR="0";
$_SevkiyatKime="Bayi";
$BayiMusteri=$demo['BAYININ_MUSTERISI'];
$KisiBilgi=$demo['BAYI_YETKILI'];
$_Adres1=mb_substr($_Sevk_Adresi,0,50);
$_Adres2=mb_substr($_Sevk_Adresi,50,50);
$MusteriSiparisNo="";
$Hizmetmi="";
$LisansSuresi="0";
$Ambar="1"; // Demo AMbari!
$fatek1="";
$durumm="0";

$sqlinsert = "INSERT INTO LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " ([SIPARISID],[NO],[CARIKOD],[MALZEMEKOD],[BIRIM],[MIKTAR],[FIYAT],[SATIS_TEMSILCISI],[SERI_NO]
          ,[FIS_DURUMU],[SATIR_ID],[Cari_Vade_Kodu],[Sevk_Adresi],[DOVIZKUR],[DOVIZ_TUR],[SevkiyatKime],[Unvan],[KisiBilgi],[Adres1],[Adres2],[MusteriSiparisNo]
          ,[BayiMusteri],[Hizmetmi],[LisansSuresi],[Ambar],SONUC,IPTAL,IRSALIYE_ID,FATURA_ID$fatek1)
          values ('$_SIPARISID','$siparis_no','$_CARI_KOD','$_MALZEMEKOD','ADET','$_MIKTAR','$_FIYAT'
          ,'$logo_kullanici','$_SERI_NO','$fis_dur','$say','$_VADE','$_Sevk_Adresi','$DOVIZKUR','$DOVIZTUR','$_SevkiyatKime','" . mb_substr($BayiMusteri,0,50) .  "','$KisiBilgi'
          ,'$_Adres1','$_Adres2','$MusteriSiparisNo','','$Hizmetmi','$LisansSuresi','$Ambar','$durumm','0','0','0'$fatek1)";
try {
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute();
    echo __('Başarılı', 'komtera');
} catch (PDOException $e) {
    BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
    die(__('Hata', 'komtera') . "|" . __('Sorun Teknik ekibe aktarılmıştır!', 'komtera'));
}

?>

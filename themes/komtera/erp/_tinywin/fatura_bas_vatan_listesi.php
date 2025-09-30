<?php
error_reporting(0);
ini_set("display_errors",false);

include "../_conn.php";

$url = "select id,
	   magaza,
(select VADE from " . getTableName('aaa_erp_kt_bayiler') . " b where b.CH_KODU='120.03.01.0522') as vade,
	   concat(adres,' ',sehir) as adres,
	   sku,
	   doviz_turu,
	   adet,
	   birim_fiyat
from " . getTableName('aa_erp_kt_vatan_faturalama') . "
";
$stmt = $conn->prepare($url);
$stmt->execute();
$data= $stmt->fetchAll(PDO::FETCH_ASSOC);

//print_r($data);
//
//die();

$say=0;
$fatsay=0;

$fatura_tarihi=$_GET['ft'];
$fatek1="";
$fatek2="";
if ($fatura_tarihi!="") {
    $fatek1=",FATURA_TARIHI";
    $fatek2=",'$fatura_tarihi'";
}

$id=0;
for ($t=0;$t<count($data);$t++) {
    $say++;
    $satir=$data[$t];
    $sonraki_satir=$data[$t+1];
    //if ($id==0) {
    $id=$satir['id'];
    //}
     if ($temp_ch===$satir['magaza']) {
        //$id=$temp_id;
    }
    $_SIPARISID = "522" . $id;
    $siparis_no = "T472" . $id;
    $_CARI_KOD = '120.03.01.0522';
    $_MALZEMEKOD = $satir['sku'];
    $_MIKTAR = $satir['adet'];
    $_FIYAT = $satir['birim_fiyat'];
    $logo_kullanici='BARIŞ ÇORBACI';
    $fis_dur = "1";                  // en son satir 2 olacak
    $magaza = $satir['magaza'];
    //echo $satir['CH_KODU'] . "=" . $sonraki_satir['CH_KODU'] . "\n";
//      if ($satir['magaza']!=$sonraki_satir['magaza']) {
//        $fis_dur = "2";
        $fatsay++;
//        $id=0;
//    }
    $_VADE = $satir['vade'];
    $_Sevk_Adresi = $satir['adres'];
    $DOVIZTUR = "0";
	
	if ($satir['doviz_turu']==='USD') {
		$DOVIZTUR="1";
	}
	if ($satir['doviz_turu']==='EUR') {
		$DOVIZTUR="2";
	}
    $_SevkiyatKime = "Müşteri";
    $KisiBilgi = mb_substr($magaza, 0, 50);
    $_Adres1 = mb_substr($_Sevk_Adresi, 0, 50);
    $_Adres2 = mb_substr($_Sevk_Adresi, 50, 50);
    $durumm = "98";
    $sqlinsert = "INSERT INTO LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " (OZEL_KOD,[SIPARISID],[NO],[CARIKOD],[MALZEMEKOD],[MIKTAR],[FIYAT],[SATIS_TEMSILCISI]
          ,[FIS_DURUMU],[SATIR_ID],[Cari_Vade_Kodu],[Sevk_Adresi],[DOVIZ_TUR],[SevkiyatKime],[Unvan],[Adres1],[Adres2]
          ,SONUC,PROJE_KOD$fatek1)
          values ('vatan','$_SIPARISID','$siparis_no','$_CARI_KOD','$_MALZEMEKOD','$_MIKTAR','$_FIYAT','$logo_kullanici'
          ,'2','1','$_VADE','$_Sevk_Adresi','$DOVIZTUR','$_SevkiyatKime','$KisiBilgi','$_Adres1','$_Adres2'
          ,'$durumm','KANAL 4'$fatek2)";
//    if ($fis_dur==2) {
//        $say=0;
//    }
		  //echo $sqlinsert . "<br />";
    try {
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute();
        echo ".";
    } catch (PDOException $e) { 
        BotMesaj($e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
        die(__('Hata', 'komtera') . "|" . __('Sorun Teknik ekibe aktarılmıştır!', 'komtera'));
    }
    $temp_ch=$magaza;

}
$sqlupdate = "update LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " set SONUC='0' WHERE SONUC='98'"; //98
try {
    $stmt = $conn->prepare($sqlupdate);
    $result = $stmt->execute();
} catch (PDOException $e) {
    BotMesaj($e->getMessage() . "\n" . $sqlupdate  );
    die("NOK|Sorun Teknik ekibe aktarilmistir!");
}
echo $fatsay;
?>
<?php
error_reporting(E_ALL);

include "../_conn.php";

$url = "select temp.id,temp.vade,temp.acronis_bayi,
b.CH_KODU,temp.SKU,temp.adet ADET,temp.birim BIRIM,e.musteri_temsilcisi MT,
b.ADRES1,b.ADRES2,temp.eposta EPOSTA
from aa_erp_kt_acronis_fatura_kes temp
left join aa_erp_kt_Acronis_CH_Eslesme e ON e.acronis_bayi_adi = temp.acronis_bayi
left join aaa_erp_kt_bayiler b ON e.komtera_bayi_adi  = b.CH_UNVANI 
left join aa_erp_kt_fiyat_listesi f ON f.sku  = temp.sku 
where e.komtera_bayi_adi is not null
order by temp.acronis_bayi ";
$stmt = $conn->prepare($url);
$stmt->execute();
$data= $stmt->fetchAll(PDO::FETCH_ASSOC);

$say=0;
$fatsay=0;

for ($t=0;$t<count($data);$t++) {
    $satir=$data[$t];
    $sonraki_satir=$data[$t+1];
    $say++;
    $id=$satir['id'];
    if ($temp_ch===$satir['CH_KODU']) {
        $id=$temp_id;
    }

    $_SIPARISID = "200" . $id;
    $siparis_no = "T200" . $id;
    $_CARI_KOD = $satir['CH_KODU'];
    $_MALZEMEKOD = $satir['SKU'];
    $_MIKTAR = $satir['ADET'];
    $_FIYAT = $satir['BIRIM'];
    $logo_kullanici=$satir['MT'];
    $fis_dur = "1";                  // en son satir 2 olacak

    //echo $satir['CH_KODU'] . "=" . $sonraki_satir['CH_KODU'] . "\n";
    if ($satir['CH_KODU']!=$sonraki_satir['CH_KODU']) {
        $fis_dur = "2";
        $fatsay++;
    }

    $_VADE = $satir['vade'];
    $_Sevk_Adresi = $satir['ADRES1'] . " " . $satir['ADRES2'];
    $DOVIZTUR = "1";
    $_SevkiyatKime = "Bayi";
    $KisiBilgi = $demo['EPOSTA'];
    $_Adres1 = mb_substr($satir['ADRES1'], 0, 50);
    $_Adres2 = mb_substr($satir['ADRES2'], 50, 50);
    $durumm = "8";

    $sqlinsert = "INSERT INTO LKS.dbo.ARYD_FIS_AKTARIM ([SIPARISID],[NO],[CARIKOD],[MALZEMEKOD],[MIKTAR],[FIYAT],[SATIS_TEMSILCISI],[SERI_NO]
          ,[FIS_DURUMU],[SATIR_ID],[Cari_Vade_Kodu],[Sevk_Adresi],[DOVIZKUR],[DOVIZ_TUR],[SevkiyatKime],[KisiBilgi],[Adres1],[Adres2]
          ,SONUC)
          values ('$_SIPARISID','$siparis_no','$_CARI_KOD','$_MALZEMEKOD','$_MIKTAR','$_FIYAT','$logo_kullanici','$_SERI_NO'
          ,'$fis_dur','$say','$_VADE','$_Sevk_Adresi','$DOVIZKUR','$DOVIZTUR','$_SevkiyatKime','$KisiBilgi','$_Adres1','$_Adres2'
          ,'$durumm')";
    try {
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute();
        //echo ".";
    } catch (PDOException $e) { 
        BotMesaj($e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
        die("NOK|Sorun Teknik ekibe aktarilmistir!");
    }

    $temp_ch=$_CARI_KOD;
    $temp_id=$id;

}

$sqlupdate = "update LKS.dbo.ARYD_FIS_AKTARIM set SONUC='0' WHERE SONUC='8'";
try {
    $stmt = $conn->prepare($sqlupdate);
    $result = $stmt->execute();
} catch (PDOException $e) {
    BotMesaj($e->getMessage() . "\n" . $sqlupdate  );
    die("NOK|Sorun Teknik ekibe aktarilmistir!");
}

echo $fatsay;

?>

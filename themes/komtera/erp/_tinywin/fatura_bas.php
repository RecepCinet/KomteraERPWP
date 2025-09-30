<?php

error_reporting(0);
ini_set("display_errors", false);

$trace = isset($_GET['trace']) ? (int) $_GET['trace'] : 0;
$param = isset($_GET['tip']) ? (int) $_GET['tip'] : "1";

function trace($param) {
    global $trace;
    if ($trace === 1) {
        $cik=print_r($param,true);
        $cik= str_replace("\n", "<br />", $cik);
        echo $cik;
    }
}

if ($trace === 1) {
    error_reporting(E_ALL);
    ini_set("display_errors", true);
}

include '../../_conn.php';
include '../../_conn_fm.php';

$siparis_no = $_GET['siparis_no'];

$url = "select * from " . getTableName('aa_erp_kt_siparisler') . " where SIPARIS_NO=:siparis_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$s = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$fatura_tar=$s['FATURALAMA_TARIHI'];
$fatura_tarihi=$s['FATURALAMA_TARIHI'];
$fatek1="";
$fatek2="";
if ($fatura_tar!="") {
    $fatek1=",FATURA_TARIHI";
    $fatek2=",'$fatura_tarihi'";
}
trace($s);

$teklif_no=$s['X_TEKLIF_NO'];

$url = "select * from " . getTableName('aa_erp_kt_siparisler_urunler') . " where X_SIPARIS_NO=:siparis_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['siparis_no' => $siparis_no]);
$su = $stmt->fetchAll(PDO::FETCH_ASSOC);
trace($su);

$url = "select * from " . getTableName('aa_erp_kt_firsatlar') . " where FIRSAT_NO=:firsat_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['firsat_no' => $s['X_FIRSAT_NO']]);
$f = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
trace($f);

$stmt = $conn2->prepare("select LOGO_kullanici from TF_USERS where kullanici='" . $f['MUSTERI_TEMSILCISI'] . "'");
$stmt->execute();
$logo_kullanici = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['LOGO_kullanici'];

$url = "select * from " . getTableName('aa_erp_kt_teklifler') . " where TEKLIF_NO=:teklif_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$t = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
trace($t);

$url = "select * from " . getTableName('aa_erp_kt_teklifler_urunler') . " where X_TEKLIF_NO=:teklif_no";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute(['teklif_no' => $teklif_no]);
$tu = $stmt->fetchAll(PDO::FETCH_ASSOC);
trace($tu);

//select top 1 usd,eur from aa_erp_kur order by tarih desc
$url = "select top 1 usd,eur from aa_erp_kur order by tarih desc";
trace($url);
$stmt = $conn->prepare($url);
$stmt->execute();
$kurlar = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($trace === 1) {
    echo "<table border=1>";
}
$say = 0;
$fis_dur = 1;
$DOVIZKUR=0;
foreach ($su as $key => $satir) {
    $_SIPARISID = str_replace('T', '', $s['X_TEKLIF_NO']);
    $_NO = $s['X_TEKLIF_NO'] . "-1";
    $_CARI_KOD = $f['BAYI_CHKODU'];
    $_MALZEMEKOD = $satir["SKU"];
    $Hizmetmi="0";
            $sqlstring5 = "select TIP from " . getTableName('aa_erp_kt_teklifler_urunler') . " where X_TEKLIF_NO='$teklif_no' AND SKU=:sku";
            $stmt = $conn->prepare($sqlstring5);
            $stmt->execute(['sku' => $_MALZEMEKOD]);
            $hizmetmikontrol = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['TIP'];
            if ($hizmetmikontrol==="Komtera") {
                $Hizmetmi="1";
            }

    $_BIRIM = "ADET";
    $_MIKTAR = $satir["ADET"];
    $_FIYAT = $satir["BIRIM_FIYAT"];
    $_SATIS_TEMSILCISI = $f["MUSTERI_TEMSILCISI"];
    $_SERI_NO = $satir['LISANS'];
    $_VADE = $t['VADE'];
    $_Sevk_Adresi = $f['SEVKIYAT_ADRES'] . " " . $f['SEVKIYAT_ILCE'] . "/" . $f['SEVKIYAT_IL'];
    $_DOVIZ_TUR = "1";
    $_DOVIZKUR = "";
    $_SevkiyatKime = $f['SEVKIYAT_KIME'];
    $_Unvan = $f['BAYI_ADI'];

    
    
    IF ($_VADE==="KKART") {
        $_VADE="PEŞİN";
    }
    
    $_Adres1 = mb_substr($_Sevk_Adresi, 0, 50);
    $_Adres2 = mb_substr($_Sevk_Adresi, 50, 50);
    
    IF ($_SevkiyatKime === "0") {
        $KisiBilgi = $f['BAYI_YETKILI_ISIM'];
        $_SevkiyatKime = "Bayi";
        //Bayi ise Adres basma!
        $_Adres1="";
        $_Adres2="";
        $_Sevk_Adresi="";
    } else {
        $KisiBilgi = $f['MUSTERI_YETKILI_ISIM'];
        $_SevkiyatKime = "Müşteri";
    }
    // ? SOR! Ticket 320
    $KisiBilgi = $f['MUSTERI_YETKILI_ISIM'];
    
    $MusteriSiparisNo = $s['MUSTERI_SIPARIS_NO'];
    $BayiMusteri = $f['MUSTERI_ADI'];

    $LisansSuresi = $satir["SURE"];
    $Ambar = ""; // Ambar!
    
    //$BayiMusteri=filter_var($BayiMusteri, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    
    $say++;
    if ($say === count($su)) {
        $fis_dur = "$param";
    }
    $DOVIZTUR=0;
        if ($s['OZEL_KUR']>0) {
            $DOVIZKUR=$s['OZEL_KUR'];
        }
        if ($f['PARA_BIRIMI']==="TRY") {
            $DOVIZTUR=0;
        }
        if ($f['PARA_BIRIMI']==="USD") {
            $DOVIZTUR=1;
        }
        if ($f['PARA_BIRIMI']==="EUR") {
            $DOVIZTUR=2;
        }
        if ($DOVIZKUR<0 || $DOVIZKUR==="") {
            $DOVIZKUR=0;
        }
        if ($DOVIZTUR<"") {
            $DOVIZTUR=0;
        }
        $_Unvan= mb_substr($_Unvan,0,50);
        
        
        $BayiMusteri= str_replace("'", "''", $BayiMusteri);
        
        
    if ($trace === 1) {
        echo "<tr>";
        echo "<td>" . $_SIPARISID . "</td>";
        echo "<td>" . $_NO . "</td>";
        echo "<td>" . $_CARI_KOD . "</td>";
        echo "<td>" . $_MALZEMEKOD . "</td>";
        echo "<td>ADET</td>";
        echo "<td>" . $_MIKTAR . "</td>";
        echo "<td>" . $_FIYAT . "</td>";
        echo "<td>" . $logo_kullanici . "</td>";
        echo "<td>" . $_SERI_NO . "</td>";
        echo "<td>$fis_dur</td>";
        echo "<td>" . $say . "</td>";
        echo "<td>" . $_VADE . "</td>";
        echo "<td>" . $_Sevk_Adresi . "</td>";
        echo "<td>" . $_Unvan . "</td>";
        echo "<td>" . $_SevkiyatKime . "</td>";
        echo "<td>" . $_Adres1 . "</td>";
        echo "<td>" . $_Adres2 . "</td>";
        echo "<td>" . $MusteriSiparisNo . "</td>";
        echo "<td>" . $BayiMusteri . "</td>";
        echo "<td>" . $Hizmetmi . "</td>";
        echo "<td>" . $LisansSuresi . "</td>";
        echo "<td>" . $Ambar . "</td>";
        echo "</tr>";
        
        

        
        
    } else {        
        $sqlinsert = "INSERT INTO LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " ([SIPARISID],[NO],[CARIKOD],[MALZEMEKOD],[BIRIM],[MIKTAR],[FIYAT],[SATIS_TEMSILCISI],[SERI_NO]
          ,[FIS_DURUMU],[SATIR_ID],[Cari_Vade_Kodu],[Sevk_Adresi],[DOVIZKUR],[DOVIZ_TUR],[SevkiyatKime],[Unvan],[KisiBilgi],[Adres1],[Adres2],[MusteriSiparisNo]
          ,[BayiMusteri],[Hizmetmi],[LisansSuresi],[Ambar],SONUC,IPTAL,IRSALIYE_ID,FATURA_ID$fatek1) values ('$_SIPARISID','$siparis_no','$_CARI_KOD','$_MALZEMEKOD','ADET','$_MIKTAR','$_FIYAT'
          ,'$logo_kullanici','$_SERI_NO','$fis_dur','$say','$_VADE','$_Sevk_Adresi','$DOVIZKUR','$DOVIZTUR','$_SevkiyatKime','" . mb_substr($BayiMusteri,0,50) .  "','$KisiBilgi'
          ,'$_Adres1','$_Adres2','$MusteriSiparisNo','','$Hizmetmi','$LisansSuresi','$Ambar','-2','0','0','0'$fatek2)";
                    try {
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute();
            } catch (PDOException $e) {
                BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
                die(__('Hata', 'komtera') . "|" . __('Sorun Teknik ekibe aktarılmıştır!', 'komtera'));
            }
        }
} // ------------------------------------------ DONGU SONU!
if ($trace === 1) {
    echo "</table>";
    
    //echo "#$DOVIZKUR#";
    $sqlinsert = "INSERT INTO LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " ([SIPARISID],[NO],[CARIKOD],[MALZEMEKOD],[BIRIM],[MIKTAR],[FIYAT],[SATIS_TEMSILCISI],[SERI_NO]
          ,[FIS_DURUMU],[SATIR_ID],[Cari_Vade_Kodu],[Sevk_Adresi],[DOVIZKUR],[DOVIZ_TUR],[SevkiyatKime],[Unvan],[KisiBilgi],[Adres1],[Adres2],[MusteriSiparisNo]
          ,[BayiMusteri],[Hizmetmi],[LisansSuresi],[Ambar],SONUC,IPTAL,IRSALIYE_ID,FATURA_ID$fatek1) values ('$_SIPARISID','$siparis_no','$_CARI_KOD','$_MALZEMEKOD','ADET','$_MIKTAR','$_FIYAT'
          ,'$logo_kullanici','$_SERI_NO','$fis_dur','$say','$_VADE','$_Sevk_Adresi','$DOVIZKUR','$DOVIZTUR','$_SevkiyatKime','" . mb_substr($BayiMusteri,0,50) .  "','$KisiBilgi'
          ,'$_Adres1','$_Adres2','$MusteriSiparisNo','','$Hizmetmi','$LisansSuresi','$Ambar','-2','0','0','0'$fatek2)";
    echo $sqlinsert;
} else {
    $sqlupdate = "update LKS.dbo." . getTableName('ARYD_FIS_AKTARIM') . " set SONUC='0' WHERE [NO]='$siparis_no'";
          try {
        $stmt = $conn->prepare($sqlupdate);
        $result = $stmt->execute();
            echo __('Başarılı', 'komtera');
                } catch (PDOException $e) {
                    BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user'] );
                    die(__('Hata', 'komtera') . "|" . __('Sorun Teknik ekibe aktarılmıştır!', 'komtera'));
                }
}

?>

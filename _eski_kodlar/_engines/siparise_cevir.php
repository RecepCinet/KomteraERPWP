<?php

error_reporting(0);
ini_set('display_errors', false);

include '../_conn.php';
$OUT="OK";
$teklif_no = $_GET['teklif_no'];
$firsat_no = $_GET['firsat_no'];
$po = $_GET['po'];
$dev = (basename($_SERVER['SCRIPT_NAME']) === 'siparise_cevir_dev.php') ? 1 : 0;

try {
    $stmt = $conn->prepare("select * from aa_erp_kt_siparisler where X_TEKLIF_NO=:teklif_no order by PARCA desc");
    $stmt->execute(['teklif_no' => $teklif_no]);
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["PARCA"];
} catch (PDOException $e) {
    //
}
//echo $gelen;
if ($gelen === "") {
    $gelen = 0;
}

$stmt = $conn->prepare("select VADE from aa_erp_kt_teklifler t WHERE TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_no]);
$vade_ne = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["VADE"];

$stmt = $conn->prepare("select BAYI_CHKODU from aa_erp_kt_firsatlar where FIRSAT_NO=(select X_FIRSAT_NO from aa_erp_kt_teklifler t WHERE TEKLIF_NO=:teklif_no)");
$stmt->execute(['teklif_no' => $teklif_no]);
$ch_kodu = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["BAYI_CHKODU"];

//select dikkat_listesi,kara_liste from aa_erp_kt_bayiler_kara_liste where ch_kodu ='120.03.01.0626'
try {
    $stmt = $conn->prepare("select dikkat_listesi,kara_liste from aa_erp_kt_bayiler_kara_liste where ch_kodu=:ch_kodu");
    $stmt->execute(['ch_kodu' => $ch_kodu]);
    $KL = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
} catch (PDOException $e) {
    //
}
$dikkat_listesi="";
$kara_liste="";

if ($KL) {
    $dikkat_listesi=$KL['dikkat_listesi'];
    $kara_liste=$KL['kara_liste'];
}

$ek1="";
$ek2="";

if ($vade_ne==="PEŞİN" || $vade_ne==="KKART") {
    $ek1=",SIPARIS_DURUM";
    $ek2=",'-1'";
}

$ek3="";
$ek4="";

//TODO: TEST lazim!
if ($dikkat_listesi==="1" || $kara_liste==="1") {
    $ek1=",SIPARIS_DURUM";
    $ek2=",'-1'";
    $ek3=",SIPARIS_DURUM_ALT";
    if ($dikkat_listesi==="1") {
        $ek4=",'42'";
    }
    if ($kara_liste==="1") {
        $ek4=",'11'";
    }
}

//TODO: Kredi!
//172.16.84.214/_service/rapor_kuru_toplam.php?teklif_no=T984445
$kredi_kontrol=file_get_contents("http://127.0.0.1/_service/rapor_kuru_toplam.php?teklif_no=" . $teklif_no);
if ($kredi_kontrol==1) {
    $ek1=",SIPARIS_DURUM";
    $ek2=",'-1'";
    $ek3=",SIPARIS_DURUM_ALT";
    $ek4=",'31'";
    $OUT="RISK";
}

$stmt = $conn->prepare("select MARKA from aa_erp_kt_firsatlar where FIRSAT_NO=:firsat_no");
$stmt->execute(['firsat_no' => $firsat_no]);
$markaa = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['MARKA'];

$gelen++;
$sqlinsert = "insert into aa_erp_kt_siparisler (MUSTERI_SIPARIS_NO,X_FIRSAT_NO,X_TEKLIF_NO,PARCA,SIPARIS_NO$ek1$ek3) values ('$po','$firsat_no','$teklif_no','$gelen','$teklif_no-$gelen'$ek2$ek4)";
//echo $sqlinsert . PHP_EOL;
$stmt = $conn->prepare($sqlinsert);
if ($dev==0) {
    $stmt->execute();
}

$parca = $gelen;

$gel=file_get_contents("http://172.16.84.214/sechard/licence_topla.php?teklif_no=" . $teklif_no);

$satir=explode("\n", $gel);
$aciklamaaa=$satir[1];

$sql="select u.SKU,u.ACIKLAMA,u.SIRA,u.ADET,u.B_SATIS_FIYATI,u.TIP,u.SURE,(select top 1 SERI_LOT FROM aaa_erp_kt_stoklar s where s.sku=u.SKU) AS TRACK_TYPE from aa_erp_kt_teklifler_urunler u where u.X_TEKLIF_NO='$teklif_no' order by u.SIRA";
if ($markaa=="SECHARD") {
    $sql = <<<DATA
select 'SCHRD' SKU,'$aciklamaaa' ACIKLAMA,'1' SIRA,'1' ADET,sum(u.B_SATIS_FIYATI*u.ADET) B_SATIS_FIYATI,'Licence' TIP,'0' SURE,'0' TRACK_TYPE from aa_erp_kt_teklifler_urunler u where u.TIP='Licence' and u.X_TEKLIF_NO='$teklif_no' group by TIP
UNION ALL 
select u.SKU,u.ACIKLAMA,u.SIRA,u.ADET,u.B_SATIS_FIYATI,u.TIP,u.SURE,(select top 1 SERI_LOT FROM aaa_erp_kt_stoklar s where s.sku=u.SKU) AS TRACK_TYPE from aa_erp_kt_teklifler_urunler u where u.TIP<>'Licence' and u.X_TEKLIF_NO='$teklif_no'
DATA;
}
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sql . "\n" . $_GET['user']);
    die("NOK|Sorun Teknik ekibe aktarilmistir!");
}

//print_r($gelen);
for ($t = 0; $t < count($gelen); $t++) {
    $sira = $gelen[$t]["SIRA"];
    $sku = $gelen[$t]["SKU"];
    $aciklama = $gelen[$t]["ACIKLAMA"];
    $adet = (int)$gelen[$t]["ADET"];
    $satis_fiyati = $gelen[$t]["B_SATIS_FIYATI"];
    $tip=$gelen[$t]["TIP"];
    $sure=$gelen[$t]["SURE"];
    $tt=$gelen[$t]["TRACK_TYPE"];
    if ($adet>1 && $tt==="2" && $tip==="Hardware") {
        for ($i=0;$i<$adet;$i++) {
            $sqlinsert = "insert into aa_erp_kt_siparisler_urunler (X_SIPARIS_NO,SIRA,SKU,ACIKLAMA,ADET,BIRIM_FIYAT,TIP,SURE)
            values ('$teklif_no-$parca','$sira','$sku','$aciklama','1','$satis_fiyati','$tip','$sure')";
            //echo $sqlinsert . PHP_EOL;
            try {
                $stmt = $conn->prepare($sqlinsert);
                if ($dev==0) {
                    $stmt->execute();
                }
            } catch (PDOException $e) {
                BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
                die("NOK|Sorun Teknik ekibe aktarilmistir!");
            }
        }
    } else {
        $sqlinsert = "insert into aa_erp_kt_siparisler_urunler (X_SIPARIS_NO,SIRA,SKU,ACIKLAMA,ADET,BIRIM_FIYAT,TIP,SURE)
        values ('$teklif_no-$parca','$sira','$sku','$aciklama','$adet','$satis_fiyati','$tip','$sure')";
        //echo $sqlinsert. PHP_EOL;
        try {
            $stmt = $conn->prepare($sqlinsert);
            if ($dev==0) {
                $stmt->execute();
            }
        } catch (PDOException $e) {
            BotMesaj("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
            echo ("Siparis no: " . $siparis_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
            die("NOK|Sorun Teknik ekibe aktarilmistir!");
        }
    }
}
echo $OUT;
//view-source:http://172.16.84.214/_engines/siparise_cevir_dev.php?firsat_no=F238157&teklif_no=T684288&po=
file_get_contents("http://127.0.0.1/_engines/siparis_mail.php?teklif_no=$teklif_no&cmd=durum1");
?>
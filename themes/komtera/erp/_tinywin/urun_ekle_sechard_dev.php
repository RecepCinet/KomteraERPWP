<?php

$bayi_seviye = 1;
$sqls = "select UserLimit,sku,cozum,lisansSuresi,listeFiyati,listeFiyatiUpLift,a_iskonto1,a_iskonto1_r,s_iskonto1,s_iskonto1_r from " . getTableName('aaa_erp_kt_sechard_list') . " s where sku='$sku'";
$stmt = $conn->query($sqls);
$datas = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
echo $sqls . "\n\n";
$lisans_suresi = $datas['lisansSuresi'];
$cozum = $datas['cozum'];
$user_limit = $datas['UserLimit'];
$sqlt = "select top 1 UserLimit,sku,cozum,lisansSuresi,listeFiyati,listeFiyatiUpLift,a_iskonto1,a_iskonto1_r,s_iskonto1,s_iskonto1_r
from " . getTableName('aaa_erp_kt_sechard_list') . " s
where cozum='$cozum' and lisansSuresi ='$lisans_suresi' and UserLimit >$user_limit";
$stmt = $conn->query($sqlt);
$datat = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
echo $sqlt . "\n\n";
print_r($datas);
print_r($datat);
$UserLimit = $datas['UserLimit'];
$listeFiyati = $datas['listeFiyati'];
$listeFiyatiUpLift = $datas['listeFiyatiUpLift'];
$o_maliyet = $listeFiyatiUpLift;
if ($register == "1") {
    $a_maliyet = $datas['a_iskonto1_r'] / $UserLimit;
    $s_maliyet = $datas['s_iskonto1_r'] / $UserLimit;
} else {
    $a_maliyet = $datas['a_iskonto1'] / $UserLimit;
    $s_maliyet = $datas['s_iskonto1'] / $UserLimit;
}
$f_adet2 = $f_adet - $UserLimit;
$UserLimit2 = $datat['UserLimit'];
$listeFiyati2 = $datat['listeFiyati'];
$listeFiyatiUpLift2 = $datat['listeFiyatiUpLift'];
$o_maliyet2 = $listeFiyatiUpLift2;
if ($register == "1") {
    $a_maliyet2_temp = $datat['a_iskonto1_r'] / $f_adet2;
    $s_maliyet2_temp = $datat['s_iskonto1_r'] / $f_adet2;
} else {
    $a_maliyet2_temp = $datat['a_iskonto1'] / $f_adet2;
    $s_maliyet2_temp = $datat['s_iskonto1'] / $f_adet2;
}
$a_maliyet2 = ( $a_maliyet2_temp - $a_maliyet ) * ( $f_adet2 / ( $UserLimit2 - $UserLimit ) );
$s_maliyet2 = ( $s_maliyet2_temp - $s_maliyet ) * ( $f_adet2 / ( $UserLimit2 - $UserLimit ) );
$h_iskonto = ( 1 - ($a_maliyet / $s_maliyet) ) * 100;
$h_iskonto2 = ( 1 - ($a_maliyet2 / $s_maliyet2) ) * 100;
$listeFiyatiUpLift2 = ($listeFiyatiUpLift2 - $listeFiyatiUpLift) * ( $f_adet2 / ( $UserLimit2 - $UserLimit ) );
// if ($datas['UserLimit']=="" || $datas['UserLimit']==0) {
//     $a_maliyet=0;
//     $h_maliyet=0;
//     $s_maliyet=0;
//     $h_iskonto=0;
//     $UserLimit=1;
// }
echo $listeFiyati . "\n";
echo $listeFiyatiUpLift . "\n";
echo $a_maliyet . "\n";
echo $s_maliyet . "\n\n";
echo $h_iskonto . "\n\n";
echo $listeFiyati2 . "\n";
echo $listeFiyatiUpLift2 . "\n";
echo $a_maliyet2 . "\n";
echo $s_maliyet2 . "\n\n";
echo $h_iskonto2 . "\n\n";
$f_adet1 = $UserLimit;
$birim_uplift = $listeFiyatiUpLift / $f_adet1;
$h_iskonto = (1 - ($s_maliyet / $birim_uplift)) * 100;
echo "##$UserLimit##";
if ($UserLimit == "0") {
    $h_iskonto = 0;
    $birim_uplift = 0;
    $a_maliyet = 0;
    $f_adet1 = 1;
    $bol = 0.1;
    if ($datas['cozum'] != 'Management Console') {
        $bol = 0.2;
    }
    $s_maliyet = $ttsl * $bol;
    $a_maliyet = $mmsl * $bol;
}
if ($datas['UserLimit'] == "" || $datas['UserLimit'] == 0 || $f_adet == $datas['UserLimit']) {
    $sqlinsert = "INSERT INTO LKS.dbo." . getTableName('aa_erp_kt_teklifler_urunler') . "
    (slot,X_TEKLIF_NO,SKU,ACIKLAMA,TIP,SURE,ADET, B_LISTE_FIYATI,B_MALIYET,O_MALIYET, ISKONTO, B_SATIS_FIYATI, SIRA)
    VALUES
    ('$track_type','$teklif_no','$sku','$urunAciklama','$tur','$lisansSuresi','$f_adet1','$birim_uplift','$b_maliyet','$a_maliyet','$h_iskonto','$s_maliyet','$sira' )";
    echo $sqlinsert . "\n\n";
    try {
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute();
        echo '0';
    } catch (PDOException $e) {
        echo '1';
        BotMesaj("Teklif no: " . $teklif_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
    }
    die();
}
$birim_uplift2 = $listeFiyatiUpLift2 / $f_adet2;
$h_iskonto2 = (1 - ($s_maliyet2 / $birim_uplift2)) * 100;
// $sira++;
if (1 == 2) {
    $sqlinsert = "INSERT INTO LKS.dbo." . getTableName('aa_erp_kt_teklifler_urunler') . "
    (slot,X_TEKLIF_NO,SKU,ACIKLAMA,TIP,SURE,ADET, B_LISTE_FIYATI,B_MALIYET,O_MALIYET, ISKONTO, B_SATIS_FIYATI, SIRA)
    VALUES
    ('$track_type','$teklif_no','$sku','$urunAciklama','$tur','$lisansSuresi','$f_adet2','$birim_uplift2','$b_maliyet2','$a_maliyet2','$h_iskonto2','$s_maliyet2','$sira' )";
    try {
        $stmt = $conn->prepare($sqlinsert);
        $result = $stmt->execute();
        echo '0';
    } catch (PDOException $e) {
        echo '1';
        BotMesaj("Teklif no: " . $teklif_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
    }
}

$toplam_adet = $f_adet1 + $f_adet2;
$toplam_uplift = ($listeFiyatiUpLift + $listeFiyatiUpLift2) / $toplam_adet;
$toplam_maliyet = (($b_maliyet * $f_adet1) + ($b_maliyet2 * $f_adet2)) / $f_adet;
$toplam_a_maliyet = (($a_maliyet * $f_adet1) + ($a_maliyet2 * $f_adet2)) / $f_adet;
$toplam_satis_fiyati = (($s_maliyet * $f_adet1) + ($s_maliyet2 * $f_adet2)) / $f_adet;
$toplam_iskonto = (1 - ($toplam_satis_fiyati / $toplam_uplift)) * 100;
//  Satir Topla
$sqlinsert = "INSERT INTO LKS.dbo." . getTableName('aa_erp_kt_teklifler_urunler') . "
    (slot,X_TEKLIF_NO,SKU,ACIKLAMA,TIP,SURE,ADET, B_LISTE_FIYATI,B_MALIYET,O_MALIYET, ISKONTO, B_SATIS_FIYATI, SIRA)
    VALUES
    ('$track_type','$teklif_no','$sku','$urunAciklama','$tur','$lisansSuresi','$toplam_adet','$toplam_uplift','$toplam_maliyet','$toplam_a_maliyet','$toplam_iskonto','$toplam_satis_fiyati','$sira' )";
print_r($sqlinsert);
try {
    $stmt = $conn->prepare($sqlinsert);
    $result = $stmt->execute();
    echo '0';
} catch (PDOException $e) {
    echo '1';
    BotMesaj("Teklif no: " . $teklif_no . "\n" . $e->getMessage() . "\n" . $sqlinsert . "\n" . $_GET['user']);
}

die();
?>

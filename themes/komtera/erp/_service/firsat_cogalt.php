<?php

error_reporting(0);
ini_set("display_errors", false);

include '../../_conn.php';

$firsat_no=$_GET['firsat_no'];

function duplicate_row($table, $PAR, $id, $fields) {
    global $conn;

    try {
        $query = "SELECT * FROM $table WHERE $PAR:id";
        //echo $query;
        $stmt = $conn->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $query = "INSERT INTO $table (" . implode(", ", $fields) . ") VALUES (:" . implode(", :", $fields) . ")";
        $stmt = $conn->prepare($query);
        foreach ($fields as $key) {
            $stmt->bindValue(":" . $key, $row[$key]);
        }
        $stmt->execute();
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$arr=Array(
"ARSIVLENMIS",
"BAGLI_FIRSAT_NO",
"BASLANGIC_TARIHI",
"BITIS_TARIHI",
"DURUM",
"ETKINLIK",
"FIRSAT_ACIKLAMA",
"FIRSAT_NO",
"GELIS_KANALI",
"KAYIDI_ACAN",
"MARKA",
"MARKA_MANAGER",
"MARKA_MANAGER_EPOSTA",
"MUSTERI_ADI",
"MUSTERI_TEMSILCISI",
"MUSTERI_YETKILI_EPOSTA",
"MUSTERI_YETKILI_ISIM",
"MUSTERI_YETKILI_TEL",
"OLASILIK",
"PARA_BIRIMI",
"PROJE_ADI",
"REVIZE_TARIHI",
"BAYI_ADI",
"BAYI_CHKODU",
"BAYI_YETKILI_ISIM",
"BAYI_YETKILI_TEL",
"BAYI_YETKILI_EPOSTA",
"BAYI_ADRES",
"BAYI_SEHIR",
"BAYI_ILCE",
"MARKA_BAYI_SEVIYE",
"VADE"
);

$sqlstring="select BAGLI_FIRSAT_NO from " . getTableName('aa_erp_kt_firsatlar') . " where FIRSAT_NO='$firsat_no'";
$stmt = $conn->query($sqlstring);
$bagli_firsat_no = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['BAGLI_FIRSAT_NO'];

if ($bagli_firsat_no=="") {
    $bagli_firsat_no=$firsat_no;
}

// FLOW Firsati Cogalt
$dup_id = duplicate_row(getTableName('aa_erp_kt_firsatlar'), "FIRSAT_NO=" , $firsat_no, $arr);
$query = "UPDATE " . getTableName('aa_erp_kt_firsatlar') . " set FIRSAT_ANA=null,BAGLI_FIRSAT_NO='$bagli_firsat_no' where FIRSAT_NO='$firsat_no' ";
$stmt = $conn->prepare($query);
$stmt->execute();
$query = "UPDATE " . getTableName('aa_erp_kt_firsatlar') . " set FIRSAT_ANA='1',FIRSAT_NO='F$dup_id',BAGLI_FIRSAT_NO='$bagli_firsat_no' where id = '$dup_id' ";
$stmt = $conn->prepare($query);
$stmt->execute();

$outstring=$dup_id;


// FLOW Ana Teklifi Cogalt

$arr=Array(
"KAMPANYA",
"KILIT",
"KOMISYON_F1",
"KOMISYON_F1_ACIKLAMA",
"KOMISYON_F2",
"KOMISYON_F2_ACIKLAMA",
"KOMISYON_H",
"KOMISYON_H_ACIKLAMA",
"KOMISYON_ODENDI",
"KOMISYON_ODENDI_ACIKLAMA",
"KOMISYON_TIP1",
"KOMISYON_TIP2",
"NOTLAR",
"SATIS_TIPI",
"SATIS_TUTARI",
"t_maliyet",
"t_satis",
"TEKLIF_EK_NOT",
"TEKLIF_LISTELI",
"TEKLIF_NO",
"TEKLIF_SURE",
"TEKLIF_TIPI",
"VADE",
"X_FIRSAT_NO",
);

$sqlstring="select FIRSAT_NO from " . getTableName('aa_erp_kt_firsatlar') . " where BAGLI_FIRSAT_NO='$firsat_no' order by id desc";
$stmt = $conn->query($sqlstring);
$firsat = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['FIRSAT_NO'];

// Tum teklifleri cogalt (sadece ana teklif degil)
$sqlstring="select * from " . getTableName('aa_erp_kt_teklifler') . " where X_FIRSAT_NO='$firsat_no'";
$stmt = $conn->query($sqlstring);
$teklifler = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($teklifler as $teklif) {
    $dup_t_id = duplicate_row(getTableName('aa_erp_kt_teklifler'), "TEKLIF_NO=" , $teklif['TEKLIF_NO'], $arr);

    $query = "UPDATE " . getTableName('aa_erp_kt_teklifler') . " set X_FIRSAT_NO='$firsat',TEKLIF_NO='T$dup_t_id' where id = '$dup_t_id' ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $sqlstring="select * from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.X_TEKLIF_NO = '" . $teklif['TEKLIF_NO'] . "'";
    $stmt = $conn->query($sqlstring);
    $urunler = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $arr_urun=Array(
    "ACIKLAMA",
    "ADET",
    "B_LISTE_FIYATI",
    "B_MALIYET",
    "B_SATIS_FIYATI",
    "ISKONTO",
    "O_MALIYET",
    "SATIS_TIPI",
    "SIRA",
    "SKU",
    "SURE",
    "TIP",
    "X_TEKLIF_NO"
    );

    foreach ($urunler as $urun) {
        $urun_id = duplicate_row(getTableName('aa_erp_kt_teklifler_urunler'), "X_TEKLIF_NO=",$teklif['TEKLIF_NO'],$arr_urun);
        $query = "UPDATE " . getTableName('aa_erp_kt_teklifler_urunler') . " set X_TEKLIF_NO='T$dup_t_id' where id = '$urun_id' ";
        $stmt = $conn->prepare($query);
        $stmt->execute();
    }
}

echo "F" . $outstring;

?>

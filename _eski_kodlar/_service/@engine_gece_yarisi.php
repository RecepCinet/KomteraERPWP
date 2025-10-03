<?php

// 05:00e ayarla:
include '../_conn.php';

//$bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=" .
//    urlencode("Komisyon Raporu Calisti!"));

try {
    $sql = "truncate table aaa_erp_kt_temp_komisyon_raporu";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo "Data silindi successfully.<br />";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $sql = "
    INSERT INTO aaa_erp_kt_temp_komisyon_raporu (
        YARATILIS_TARIHI,
        MARKA,
        FATTAR,
        FATNO,
        TEKLIF_NO,
        BAYI_ADI,
        PARA_BIRIMI,
        TOPTUT,
        KOMISYON_TOPLAM,
        KOMISYON_F1,
        KOMISYON_F2,
        KOMISYON_H,
        KOMISYON_F1_ACIKLAMA,
        KOMISYON_F2_ACIKLAMA,
        KOMISYON_H_ACIKLAMA,
        MUSTERI_ADI
    )
SELECT
    T.YARATILIS_TARIHI,
    F.MARKA,
    inv.DATE_ AS FATTAR,
    inv.FICHENO AS FATNO,
    T.TEKLIF_NO,
    F.BAYI_ADI,
    F.PARA_BIRIMI,
    (SELECT SUM(ADET*BIRIM_FIYAT) FROM aa_erp_kt_siparisler_urunler su WHERE su.X_SIPARIS_NO LIKE T.TEKLIF_NO+'%') AS  ,
    (ISNULL(T.KOMISYON_F1,0) + ISNULL(T.KOMISYON_F2,0) + ISNULL(T.KOMISYON_H,0)) AS KOMISYON_TOPLAM,
    T.KOMISYON_F1,
    T.KOMISYON_F2,
    T.KOMISYON_H,
    T.KOMISYON_F1_ACIKLAMA,
    T.KOMISYON_F2_ACIKLAMA,
    T.KOMISYON_H_ACIKLAMA,
    F.MUSTERI_ADI
FROM
    aa_erp_kt_teklifler T
    LEFT JOIN aa_erp_kt_firsatlar F ON T.X_FIRSAT_NO=F.FIRSAT_NO
    LEFT JOIN LG_318_01_INVOICE inv ON inv.DOCODE LIKE CONCAT(T.TEKLIF_NO,'%')
WHERE
    (T.KOMISYON_F1 > 0 OR T.KOMISYON_F2 > 0)
    AND (SELECT TOP 1 SIPARIS_DURUM FROM aa_erp_kt_siparisler s4 WHERE T.TEKLIF_NO=s4.X_TEKLIF_NO) = '2'
    AND T.TEKLIF_NO IN (SELECT TNO FROM aaaa_kapali_ve_tam_siparisler WHERE PARCA = OLAN)
    AND inv.DATE_ = (SELECT MAX(inv2.DATE_) FROM LG_318_01_INVOICE inv2 WHERE inv2.CANCELLED=0 AND inv2.DOCODE LIKE CONCAT(T.TEKLIF_NO,'%'))
    AND NOT EXISTS (SELECT 1 FROM aaa_erp_kt_temp_komisyon_raporu WHERE TEKLIF_NO = T.TEKLIF_NO)
UNION ALL
SELECT
    T.YARATILIS_TARIHI,
    F.MARKA,
    inv.DATE_ AS FATTAR,
    inv.FICHENO AS FATNO,
    T.TEKLIF_NO,
    F.BAYI_ADI,
    F.PARA_BIRIMI,
    (SELECT SUM(ADET*BIRIM_FIYAT) FROM aa_erp_kt_siparisler_urunler su WHERE su.X_SIPARIS_NO LIKE T.TEKLIF_NO+'%') AS TOPTUT,
    (ISNULL(T.KOMISYON_F1,0) + ISNULL(T.KOMISYON_F2,0) + ISNULL(T.KOMISYON_H,0)) AS KOMISYON_TOPLAM,
    T.KOMISYON_F1,
    T.KOMISYON_F2,
    T.KOMISYON_H,
    T.KOMISYON_F1_ACIKLAMA,
    T.KOMISYON_F2_ACIKLAMA,
    T.KOMISYON_H_ACIKLAMA,
    F.MUSTERI_ADI
FROM
    aa_erp_kt_teklifler T
    LEFT JOIN aa_erp_kt_firsatlar F ON T.X_FIRSAT_NO=F.FIRSAT_NO
    LEFT JOIN LG_319_01_INVOICE inv ON inv.DOCODE LIKE CONCAT(T.TEKLIF_NO,'%')
WHERE
    (T.KOMISYON_F1 > 0 OR T.KOMISYON_F2 > 0)
    AND (SELECT TOP 1 SIPARIS_DURUM FROM aa_erp_kt_siparisler s4 WHERE T.TEKLIF_NO=s4.X_TEKLIF_NO) = '2'
    AND T.TEKLIF_NO IN (SELECT TNO FROM aaaa_kapali_ve_tam_siparisler WHERE PARCA = OLAN)
    AND inv.DATE_ = (SELECT MAX(inv2.DATE_) FROM LG_319_01_INVOICE inv2 WHERE inv2.CANCELLED=0 AND inv2.DOCODE LIKE CONCAT(T.TEKLIF_NO,'%'))
    AND NOT EXISTS (SELECT 1 FROM aaa_erp_kt_temp_komisyon_raporu WHERE TEKLIF_NO = T.TEKLIF_NO)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    echo "Data insert edildi.";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $sql = "SELECT * FROM aaa_erp_kt_temp_komisyon_raporu ORDER BY TEKLIF_NO";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Kayıtlar çekildi.<br />";
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}

try {
    $previousTeklifNo = null;
    foreach ($rows as $row) {
        if ($row['TEKLIF_NO'] == $previousTeklifNo) {
            // Yinelenen kaydı sil
            $sqlDelete = "DELETE FROM aaa_erp_kt_temp_komisyon_raporu WHERE id = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->execute(['id' => $row['id']]);
            echo "Yinelenen kayıt silindi: TEKLIF_NO " . $row['TEKLIF_NO'] . "<br />";
        }
        $previousTeklifNo = $row['TEKLIF_NO'];
    }
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}


//$bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=" .
//    urlencode("Komisyon Raporu Bitti!"));
?>
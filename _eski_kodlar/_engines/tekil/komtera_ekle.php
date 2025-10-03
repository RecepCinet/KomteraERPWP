<?php

        $sku = $_GET['sku'];
        $aciklama = base64_decode($_GET['aciklama']);
        $teklif_no = $_GET['teklif_no'];
        $sure = $_GET['sure'];
        $sira = 1;
        $sql_sira = "select top 1 CASE WHEN SIRA is null THEN 1 ELSE SIRA+1 END as SIRA
        from aa_erp_kt_teklifler_urunler
        where x_TEKLIF_NO='$teklif_no' order by SIRA DESC  
        ";
        $stmt2 = $conn->query($sql_sira);
        $data2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $cvp2 = $data2[0];
        $temp_sira = $cvp2['SIRA'];
        if ($temp_sira > 0) {
            $sira = $temp_sira;
        }
        $yazstring = "insert into aa_erp_kt_teklifler_urunler (X_TEKLIF_NO,SKU,ACIKLAMA,SURE,SIRA,TIP) values ('$teklif_no','$sku','$aciklama','$sure','$sira','Komtera')";
        echo $yazstring;
        $stmt = $conn->prepare($yazstring);
        $stmt->execute();
        //$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        //$json= json_encode($gelen[0]);
        //echo $gelen['val'];
        
        ?>

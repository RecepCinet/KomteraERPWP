<style>
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid aliceblue;
  color: #333333;
}
</style>
<?PHP
$ek="";
if ($cryp==="recep.cinet" || $cryp==="gokhan.ilgit") {
    $ek="";
} else {
    $ek = "and MUSTERI_TEMSILCISI='$cryp'";
}
$kactane=15;
//--DATEADD(day, 7, REVIZE_TARIHI) KAPANACAK
$string = "select TOP $kactane FIRSAT_NO,BITIS_TARIHI,DATEDIFF(day,GETDATE(),BITIS_TARIHI) KALAN_GUN,BAYI_ADI,MUSTERI_ADI from aa_erp_kt_firsatlar f
where DATEADD(day, 1, BITIS_TARIHI)>=GETDATE() and FIRSAT_NO is not null and SIL<>1 $ek
and DURUM=0
order by BITIS_TARIHI";
//echo $string;
$stmt = $conn->prepare($string);
$stmt->execute();
$datafull = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<br />
<table style="width: 100%;" cellpadding="3" border="0" class="hideextra">
    <thead>
    <th colspan="5" style="background-color: #f2f2f2;text-align: left;width: 100px;height: 28px">Yakında Kapanacak Fırsatlar (Yetersiz Takip Yapılacak)</th>
</thead>
<tr>
    <td width="70"><b>Fırsat NO</td>
    <td width="80"><b>Bitis Tarihi</td>
    <td width="80"><b>Kalan Gün</td>
    <td width="300"><b>Bayi Adı</td>
    <td><b>Müşteri Adı</td>
</tr>
<?PHP
foreach ($datafull as $satir) {
    $renk="";
    if ((int)$satir['KALAN_GUN']==1) {
        $renk="#FFDDDD";
    }
        if ((int)$satir['KALAN_GUN']==2) {
        $renk="#FFDD99";
    }
    if ((int)$satir['KALAN_GUN']==0) {
        $renk="#FF9999";
    }
?>
<tr bgcolor="<?PHP echo $renk; ?>">
    <td width="80" align="center"><a href="#" onclick="FileMaker.PerformScriptWithOption('Firsat', 'Ac' + '|' + <?php echo "'" . $satir['FIRSAT_NO'] . "'"; ?>)"><?PHP echo $satir['FIRSAT_NO']; ?></a></td>
    <td width="80" align="center"><?PHP echo $satir['BITIS_TARIHI']; ?></td>
    <td width="80" align="center"><?PHP echo $satir['KALAN_GUN']; ?></td>
    <td><?PHP echo mb_substr($satir['BAYI_ADI'],0,40); ?></td>
    <td><?PHP echo mb_substr($satir['MUSTERI_ADI'],0,40); ?></td>
    </tr>
<?PHP
    }
?>
</table>

<?php

$string="update aa_erp_kt_firsatlar
set KAYBEDILME_NEDENI = 'Yetersiz Takip',DURUM = '-1'
where DATEADD(day, 1, BITIS_TARIHI)<=GETDATE() and SIL<>1 and DURUM=0";
$stmt = $conn->prepare($string);
$stmt->execute();

?>
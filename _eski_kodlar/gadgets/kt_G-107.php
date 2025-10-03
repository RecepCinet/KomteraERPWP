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
$yil=date("Y");

$string = "Select markalar from TF_USERS where kullanici='" . $user[0]['kullanici'] . "' ";
$stmt = $conn2->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['markalar'];
$data="'" . str_replace("\r","','", $data) . "'";

//
$data .=",'KOMTERA'";

//echo $data;

//error_reporting(E_ALL);
//ini_set("display_errors",true);

$inst=$data;

$string="select sum(q1) as Q1,sum(q2) as Q2,sum(q3) as Q3,sum(q4) as Q4 from aa_erp_kt_mt_hedefler where marka in ($data)";
$stmt = $conn->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$q1_hedef=$data['Q1'];
$q2_hedef=$data['Q2'];
$q3_hedef=$data['Q3'];
$q4_hedef=$data['Q4'];

$string="SELECT
    SUM(Ocak+Subat+Mart) AS Q1,
    SUM(Nisan+Mayis+Haziran) AS Q2,
    SUM(Temmuz+Agustos+Eylul) AS Q3,
    SUM(Ekim+Kasim+Aralik) AS Q4
FROM aaaa_erp_kt_komisyon_raporu_komisyonsuz_marka
WHERE MARKA IN ($inst)";
//echo $string;
$stmt = $conn->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$q1_satis=$data['Q1'];
$q2_satis=$q1_satis+$data['Q2'];
$q3_satis=$q2_satis+$data['Q3'];
$q4_satis=$q3_satis+$data['Q4'];

$q1_kalan=$q1_hedef-$q1_satis;
$q2_kalan=$q2_hedef-$q2_satis;
$q3_kalan=$q3_hedef-$q3_satis;
$q4_kalan=$q4_hedef-$q4_satis;


$ay=(int)Date("m");

if ($ay===1 || $ay===2 || $ay===3) {
    $q2_kalan="";
    $q3_kalan="";
    $q4_kalan="";
    $q2_satis="";
    $q3_satis="";
    $q4_satis="";
}
if ($ay===4 || $ay===5 || $ay===6) {
    $q1_kalan="";
    $q3_kalan="";
    $q4_kalan="";
    $q3_satis="";
    $q4_satis="";
}
if ($ay===7 || $ay===8 || $ay===9) {
    $q1_kalan="";
    $q2_kalan="";
    $q4_kalan="";
    $q4_satis="";
}
if ($ay===10 || $ay===11 || $ay===12) {
    $q1_kalan="";
    $q2_kalan="";
    $q3_kalan="";
}

//Acik Siparisler:
$sqlstring="SELECT f.marka,
CASE
WHEN MONTH(s.CD)>=1 AND MONTH(s.CD)<=3 THEN 1
WHEN MONTH(s.CD)>=4 AND MONTH(s.CD)<=6 THEN 2
WHEN MONTH(s.CD)>=7 AND MONTH(s.CD)<=9 THEN 3
WHEN MONTH(s.CD)>=10 AND MONTH(s.CD)<=12 THEN 4
ELSE 0
END AS Q,
CASE
WHEN f.PARA_BIRIMI = 'USD' THEN (su.ADET * su.BIRIM_FIYAT)
WHEN f.PARA_BIRIMI = 'TRY' THEN (su.ADET * su.BIRIM_FIYAT) / (select top 1 USD from aa_erp_kur k order by tarih desc)
WHEN f.PARA_BIRIMI = 'EUR' THEN (su.ADET * su.BIRIM_FIYAT) / (select top 1 USD/EUR from aa_erp_kur k order by tarih desc)
ELSE 0
END AS DLR_TUTAR
FROM  dbo.aa_erp_kt_siparisler_urunler AS su LEFT OUTER JOIN
         dbo.aa_erp_kt_siparisler AS s ON s.SIPARIS_NO = su.X_SIPARIS_NO LEFT OUTER JOIN
         dbo.aa_erp_kt_firsatlar AS f ON f.FIRSAT_NO = s.X_FIRSAT_NO
WHERE (s.SIPARIS_DURUM = 0 OR s.SIPARIS_DURUM = 1) and MARKA IN ($inst)";
$stmt = $conn->query($sqlstring);
$acsip = $stmt->fetchAll(PDO::FETCH_ASSOC);

$q1_toplam=0;
$q2_toplam=0;
$q3_toplam=0;
$q4_toplam=0;
foreach ($acsip as $satir) {
    if ($satir['Q']==="1") {
        $q1_toplam += (int)$satir['DLR_TUTAR'];
    }
    if ($satir['Q']==="2") {
        $q2_toplam += (int)$satir['DLR_TUTAR'];
    }
    if ($satir['Q']==="3") {
        $q3_toplam += (int)$satir['DLR_TUTAR'];
    }
    if ($satir['Q']==="4") {
        $q4_toplam += (int)$satir['DLR_TUTAR'];
    }
}
?>
<table style="width: 50%;" cellpadding="3" border="1" align="left">
    <thead>
    <th style="background-color: #f2f2f2;text-align: right;height: 28px;">Satış Hedefleri</th>
    <th style="background-color: #f2f2f2;text-align: right;">Q1</th>
    <th style="background-color: #f2f2f2;text-align: right;">Q2</th>
    <th style="background-color: #f2f2f2;text-align: right;">Q3</th>
    <th style="background-color: #f2f2f2;text-align: right;">Q4</th>
    </thead>
    <tr>
        <td style="background-color: #FFFFFF;text-align: right;">Hedefler</td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q1_hedef,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q2_hedef,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q3_hedef,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q4_hedef,0,",","."); ?></td>
    </tr>
    <tr>
        <td style="background-color: #FFFFFF;text-align: right;">Satış</td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q1_satis,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q2_satis,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q3_satis,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q4_satis,0,",","."); ?></td>
    </tr>
    <tr>
        <td style="background-color: #FFFFFF;text-align: right;">Kalan</td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q1_kalan,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q2_kalan,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q3_kalan,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q4_kalan,0,",","."); ?></td>
    </tr>
    <tr>
        <td style="background-color: #FFFFFF;text-align: right;">Acik Siparis</td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q1_toplam,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q2_toplam,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q3_toplam,0,",","."); ?></td>
        <td style="background-color: #FFFFFF;text-align: right;width: 70px;"><?PHP echo number_format($q4_toplam,0,",","."); ?></td>
    </tr>
</table>

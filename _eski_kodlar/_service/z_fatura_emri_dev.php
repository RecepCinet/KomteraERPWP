<?php
include '_func.php';
include '../_conn.php';
$idler=$_GET['idler'];
$arr_ids=explode(",",$idler);

$fatura_tar = isset($_GET['ft']) ? (string) $_GET['ft'] : "";

if ($fatura_tar != "") {
    $dc = date_create($fatura_tar);
    $date = $dc->format('Y-m-d');
    $time = date("H:i:s");
    $baskiTarihi = $date . "T" . $time . "Z";
} else {
    $date = date('Y-m-d');
    $time = date("H:i:s");
    $baskiTarihi = $date . "T" . $time . "Z";
}

$ff=$arr_ids[0];

$stmt = $conn->prepare("select _teklif_no,cariKod from aa_erp_kt_fatura_i where r_LogoId='$ff'");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

$teklif_no=$data['_teklif_no'];
$ch_kodu=$data['cariKod'];

if ($fatura_tar=="") {
    $sql="insert into aa_erp_kt_fatura_i (_faturami,unvan,projeKodu,_teklif_no,cariKod) values ('1','$idler','FATURA','$teklif_no','$ch_kodu')";
} else {
    $sql="insert into aa_erp_kt_fatura_i (faturaTarihi,irsaliyeTarihi,_faturami,unvan,projeKodu,_teklif_no,cariKod,faturaTarihi,irsaliyeTarihi) values ($baskiTarihi,$baskiTarihi,'1','$idler','FATURA','$teklif_no','$ch_kodu')";
}
echo $sql;
$stmt = $conn->prepare($sql);
$stmt->execute();

?>
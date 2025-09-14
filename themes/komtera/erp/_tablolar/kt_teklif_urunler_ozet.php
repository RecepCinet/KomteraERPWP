<?PHP
error_reporting(E_ALL);
ini_set('display_erros', true);
include '../../_conn.php';
$teklif_id= $_GET['teklif_id'];
// where SIL<>'1' AND $dates ORDER BY BASLANGIC_TARIHI
$sql = "SELECT 	id,
        X_TEKLIF_NO,
	SKU,
	ACIKLAMA,
	TIP,
	SURE,
	ADET,
	B_LISTE_FIYATI,
	B_MALIYET,
	ISKONTO,
	B_SATIS_FIYATI,
	B_MALIYET*ADET AS T_MALIYET,
	B_SATIS_FIYATI*ADET AS T_SATIS_FIYATI,
	KARLILIK,
	TRACK_TYPE,
	SIRA
        FROM LKS.dbo.aa_erp_kt_teklifler_urunler where X_TEKLIF_NO='$teklif_id'";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

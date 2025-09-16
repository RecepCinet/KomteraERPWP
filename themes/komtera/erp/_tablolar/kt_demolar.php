<?PHP
error_reporting(E_ALL);
ini_set('display_errors', true);
include '../../_conn.php';

$sql = "Select * from aa_erp_kt_demolar_view where SIL<>'1' ORDER BY id desc";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
// aa_erp_kt_demolar_view
//WHEN d.DEMO_DURUM = '1' THEN 'Demo Sevk Bekleniyor'
//WHEN d.DEMO_DURUM = '2' THEN 'Demo Sevk Edildi'
//WHEN d.DEMO_DURUM = '3' THEN 'Demo Geri Teslim Alındı'
//WHEN d.DEMO_DURUM = '5' THEN 'Elden Teslim Bekleniyor'
//WHEN d.DEMO_DURUM = '6' THEN 'Elden Teslim Edildi'
//WHEN d.DEMO_DURUM = '8' THEN 'Demo Kontrol Edildi/Kapatıldı'
//WHEN d.DEMO_DURUM = '9' THEN 'Demo Cihaz Satıldı'
?>

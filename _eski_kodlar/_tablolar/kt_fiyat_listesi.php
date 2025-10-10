<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

session_start();

include '../_conn.php';

error_reporting(E_ALL);
ini_set('display_erros', true);

$marka= $_GET['marka'];

$fields="sku,
urunAciklama,
marka,
tur,
cozum,
lisansSuresi,
wgCategory,
wgUpcCode,
paraBirimi,
listeFiyati,
listeFiyatiUpLift,
a_iskonto4,
a_iskonto3,
a_iskonto2,
a_iskonto1,
listeFiyati * ( 1 - a_iskonto4 ) as a_rakam4,
listeFiyati * ( 1 - a_iskonto3 ) as a_rakam3,
listeFiyati * ( 1 - a_iskonto2 ) as a_rakam2,
listeFiyati * ( 1 - a_iskonto1 ) as a_rakam1,
s_iskonto4,
s_iskonto3,
s_iskonto2,
s_iskonto1,
listeFiyatiUpLift * ( 1 - s_iskonto4 ) as s_rakam4,
listeFiyatiUpLift * ( 1 - s_iskonto3 ) as s_rakam3,
listeFiyatiUpLift * ( 1 - s_iskonto2 ) as s_rakam2,
listeFiyatiUpLift * ( 1 - s_iskonto1 ) as s_rakam1,
a_iskonto4_r,
a_iskonto3_r,
a_iskonto2_r,
a_iskonto1_r,
listeFiyati * ( 1 - a_iskonto4_r ) as a_rakam4_r,
listeFiyati * ( 1 - a_iskonto3_r ) as a_rakam3_r,
listeFiyati * ( 1 - a_iskonto2_r ) as a_rakam2_r,
listeFiyati * ( 1 - a_iskonto1_r ) as a_rakam1_r,
s_iskonto4_r,
s_iskonto3_r,
s_iskonto2_r,
s_iskonto1_r,
listeFiyatiUpLift * ( 1 - s_iskonto4_r ) as s_rakam4_r,
listeFiyatiUpLift * ( 1 - s_iskonto3_r ) as s_rakam3_r,
listeFiyatiUpLift * ( 1 - s_iskonto2_r ) as s_rakam2_r,
listeFiyatiUpLift * ( 1 - s_iskonto1_r ) as s_rakam1_r
";

//$eks="CHARINDEX(marka, '" . $_SESSION['user']['markalar'] . "')>0 AND";

$sql = "select $fields from aa_erp_kt_fiyat_listesi WHERE marka='$marka'";


error_reporting(E_ALL);
ini_set('display_erros', true);
ini_set('memory_limit', '512M');
set_time_limit(9000);

try {
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}

$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

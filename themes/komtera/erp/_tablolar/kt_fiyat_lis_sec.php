<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

include '../../_conn.php';
$marka= $_GET['marka'];
$seclis= $_GET['seclis'];


$bit=50000;

$sechard=")";

if ($marka=="SECHARD") {
    if ($seclis>1) {
        $bas=1;
        $bit=50;
    }
    if ($seclis>50) {
        $bas=50;
        $bit=50;
    }
    if ($seclis>100) {
        $bas=100;
        $bit=100;
    }
    if ($seclis>250) {
        $bas=250;
        $bit=250;
    }
    if ($seclis>500) {
        $bas=500;
        $bit=500;
    }
    if ($seclis>1000) {
        $bas=1000;
        $bit=1000;
    }
    if ($seclis>2500) {
        $bas=2500;
        $bit=2500;
    }
    if ($seclis>5000) {
        $bas=5000;
        $bit=5000;
    }
    if ($seclis>10000) {
        $bas=10000;
        $bit=10000;
    }
    if ($seclis>25000) {
        $bas=25000;
        $bit=25000;
    }
    if ($seclis>50000) {
        $seclis=1;
        $bit=1;
    }
    
    
    if ($seclis>1) {
        $sechard="and (TRY_CONVERT(int, f.wgCategory)>=$bas AND TRY_CONVERT(int, f.wgCategory)<=$bit) or f.wgCategory='0'";
    }
$sechard="and TRY_CONVERT(int, f.wgCategory)>=$bas AND TRY_CONVERT(int, f.wgCategory)<=$bit) or f.wgCategory='0'";
        
        if ($seclis==1 ||
            $seclis==50 ||
            $seclis==100 ||
            $seclis==250 ||
            $seclis==500 ||
            $seclis==1000 ||
            $seclis==2500 ||
            $seclis==5000 ||
            $seclis==10000 ||
            $seclis==25000 ||
            $seclis==50000 ||
            $seclis==1 )
        {
            $sechard="and f.wgCategory='$seclis') or f.wgCategory='0'";
        
        }
        
    
}

$sql = "select wgCategory,(CASE WHEN s.GERCEK_STOK>0 THEN 'STOK VAR' ELSE 'STOK YOK' END) as STOK_DURUM,(CASE WHEN s.GERCEK_STOK>0 THEN CONVERT(varchar(10), s.GERCEK_STOK)  ELSE '-' END) as STOK_ADET,f.tur,f.cozum,f.lisansSuresi,f.sku,f.urunAciklama,f.listeFiyati,f.paraBirimi
from " . getTableName('aa_erp_kt_fiyat_listesi') . " f LEFT JOIN " . getTableName('aaa_erp_kt_stoklar_satis') . " s
ON s.SKU = f.sku
WHERE ( f.marka='$marka'
$sechard
order by f.tur,f.cozum
";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>

<?PHP
error_reporting(E_ALL);
ini_set("display_errors", true);

$siparis_no=$_GET['siparis_no'];

function getDBH() {
    $serverName = "172.16.85.76";
    try {
        $options = array(
            "CharacterSet" => "UTF-8"
        );
        $conn = new PDO("sqlsrv:server=$serverName; Database=LKS", "crm", "!!!Crm!!!", $options);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "!---MS SQL Baglanti Sorunu!---<br />" . PHP_EOL;
        die(print_r($e->getMessage()));
    }
    return $conn;
}

$dbh = getDBH();
$sqlu = "update " . getTableName('aa_erp_kt_siparisler') . " set FATURA_BASILDI=1,SIPARIS_DURUM='2',SIPARIS_DURUM_ALT=null WHERE SIPARIS_NO IN (
select [NO] FROM " . getTableName('ARYD_FIS_AKTARIM') . "
WHERE SONUC=4 AND [NO] IN (select s.SIPARIS_NO from " . getTableName('aa_erp_kt_siparisler') . " s WHERE SIPARIS_DURUM='0')
GROUP BY [NO]
)";
$stmt = $dbh->prepare($sqlu);
$result = $stmt->execute();


function boolToInt($val){
    if($val=='true'){
        return 1;
    }
    else if($val =='false'){
        return 0;
    }
}
function intToBool($val){
    if($val==1){
        return true;
    }
    else if($val ==0){
        return false;
    }
}

function updateSingle($pdo, $r){
    
    $sec_adet=(int)$r['SEC_ADET'];
    $adet=(int)$r['ADET'];
    
    if ($sec_adet>$adet) {
        $sec_adet=$adet;
    }
    
    $sql = "update " . getTableName('aa_erp_kt_siparisler_urunler') . " set SEC= ?,SEC_ADET = ? where id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute( array($r['SEC'],$sec_adet, $r['id']) );
    if($result == false) {
        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
    }
}

function deleteSingle($pdo, $r)
{
    $sql = "delete from " . getTableName('aa_erp_kt_siparisler_urunler') . " where id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array( $r['id']));
    if($result == false) {
        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
    }
}

function addList($addList)
{
    $pdo = getDBH();
    foreach ($addList as &$r)
    {
        $r['id'] = addSingle($pdo, $r);
    }
    return $addList;
}

function updateList($updateList)
{
    $pdo = getDBH();
    foreach ($updateList as $r)
    {
        updateSingle($pdo, $r);
    }
}

function deleteList($deleteList)
{
    $pdo = getDBH();
    foreach ($deleteList as $r)
    {
        deleteSingle($pdo, $r);
    }
}

if( isset($_GET["pq_add"]))
{
    $response = "{\"recId\": \"" . addSingle(getDBH(), $_GET ). "\"}";
}
else if( isset($_GET["pq_update"]))
{
    updateSingle(getDBH(), $_GET);
    $response =  "{\"result\": \"success\"}";
}
else if( isset($_GET["pq_delete"]))
{
    deleteSingle(getDBH(), $_GET);
    $response =  "{\"result\": \"success\"}";
}
else if( isset($_GET["pq_batch"]))
{
    $dlist = json_decode($_POST['list'],true);
    
    if(isset($dlist["updateList"])){
        updateList($dlist["updateList"]);
    }
    if(isset($dlist["addList"])){
        $dlist["addList"] = addList($dlist["addList"]);
    }
    if(isset($dlist["deleteList"])){
        deleteList($dlist["deleteList"]);
    }

    $response =  json_encode($dlist);
}
else if( isset($_GET["pq_curpage"]) )//paging.
{
    $pq_curPage = $_GET["pq_curpage"];
    $pq_rPP=$_GET["pq_rpp"];
    $sql = "Select count(*) from " . getTableName('aa_erp_kt_siparisler_urunler') . "";
    $dbh = getDBH();
    $stmt = $dbh->query($sql);
    $total_Records = $stmt->fetchColumn();
    $skip = ($pq_rPP * ($pq_curPage - 1));
    if ($skip >= $total_Records)
    {
        $pq_curPage = ceil($total_Records / $pq_rPP);
        $skip = ($pq_rPP * ($pq_curPage - 1));
    }
    $sql = "Select * from " . getTableName('aa_erp_kt_siparisler_urunler') . " order by id limit OFFSET $skip ROWS FETCH NEXT $pq_rPP ROWS ONLY";
    $stmt = $dbh->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $i=> &$row)
    {
        $row['Discontinued']= intToBool($row['Discontinued']);
    }
    $sb = "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($products)."}";
    $response =  $sb;
}
else{
   $sql = "
select
su.id,
(select top 1 CASE WHEN tu.B_MALIYET>0 THEN tu.B_MALIYET ELSE tu.O_MALIYET END from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.X_TEKLIF_NO=sip.X_TEKLIF_NO  AnD SKU = su.SKU) AS MALIYET,
(select top 1 CASE WHEN tu.B_MALIYET>0 THEN tu.B_MALIYET*ADET ELSE tu.O_MALIYET*ADET END from " . getTableName('aa_erp_kt_teklifler_urunler') . " tu where tu.X_TEKLIF_NO=sip.X_TEKLIF_NO AnD SKU = su.SKU) AS T_MALIYET,
su.SIRA,
sip.X_TEKLIF_NO,
su.SKU,
su.ACIKLAMA,
su.ADET,
su.BIRIM_FIYAT,
su.TIP,
su.SURE,
(su.ADET*su.BIRIM_FIYAT) AS TOPLAM,
su.LISANS,
(select top 1 ADET from " . getTableName('aaa_erp_kt_stoklar') . " aeks where DEPO_KODU=0 AND SKU=su.SKU order by ADET desc) AS STOK,
(select top 1 SONUC from " . getTableName('ARYD_FIS_AKTARIM') . " WHERE [NO]=su.X_SIPARIS_NO) AS LSONUC,
(select top 1 MESAJ from " . getTableName('ARYD_FIS_AKTARIM') . " WHERE [NO]=su.X_SIPARIS_NO) AS LMESAJ,
su.SEC,
(select top 1 SERI_LOT from " . getTableName('aaa_erp_kt_stoklar') . " aeks where SKU=su.SKU) AS SLOT,
su.SEC_ADET
from " . getTableName('aa_erp_kt_siparisler_urunler') . " su
INNER JOIN " . getTableName('aa_erp_kt_siparisler') . " sip ON sip.SIPARIS_NO = su.X_SIPARIS_NO
--LEFT OUTER JOIN " . getTableName('aa_erp_kt_teklifler_urunler') . " tu ON tu.X_TEKLIF_NO = sip.X_TEKLIF_NO
--LEFT OUTER JOIN " . getTableName('aaa_erp_kt_stoklar_satis') . " ss ON su.SKU = ss.SKU
where X_SIPARIS_NO='$siparis_no'";
    $dbh = getDBH();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response =  "{\"data\":".json_encode($products)."}";
}
echo $response;
?>

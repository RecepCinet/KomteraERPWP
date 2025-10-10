<?PHP

// Temporarily enable error reporting for debugging
error_reporting(E_ALL);
ini_set("display_errors", true);

// Include table helper for getTableName function
require_once dirname(__DIR__, 2) . '/inc/table_helper.php';

// Include DB connection
require_once dirname(__DIR__) . '/_conn.php';

function getDBH() {
    global $conn;

    // If connection already exists from _conn.php, use it
    if (isset($conn) && $conn instanceof PDO) {
        return $conn;
    }

    // Otherwise create new connection
    $serverName = "172.16.85.76,1433";
    $database = "LKS";
    $username = "crm";
    $password = "!!!Crm!!!";

    $dsn = "sqlsrv:Server=$serverName;Database=$database;Encrypt=1;TrustServerCertificate=1";
    try {
        $conn = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $conn;
    } catch (PDOException $e) {
        error_log("DB Connection Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed', 'message' => $e->getMessage()]);
        exit;
    }
}

//for mapping of boolean values to TINYINT column in db.
function boolToInt($val) {
    //return $val;
    if ($val == 'true') {
        return 1;
    } else if ($val == 'false') {
        return 0;
    }
}

//for mapping of number to booleans.
function intToBool($val) {
    if ($val == 1) {
        return true;
    } else if ($val == 0) {
        return false;
    }
}

//add single record in db.
//function addSingle($pdo, $r){
//    //$discontinued = boolToInt($r['Discontinued']);
//
//    $sql = "insert into aa_erp_tickets (company, version, modul, type) values (?, ?, ?, ?, ?)";
//
//    $stmt = $pdo->prepare($sql);
//    $result = $stmt->execute(array($r['company'], $r['version'], $r['modul'], $r['type']));
//
//    if($result == false) {
//        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
//    }
//    return $pdo->lastInsertId();
//}
//update single record in db.
function updateSingle($pdo, $r, $in) {
    //$discontinued = boolToInt($r['Discontinued']);


    $oo = json_decode($_POST['list'], true)['oldList'][$in];

    //$oo = json_decode($json,true)['oldList'][0];
    //$r = json_decode($json,true)['updateList'][0];

    $maliyet = $r['B_MALIYET'];
    $o_maliyet = $r['B_LISTE_FIYATI']; //$oo['O_MALIYET']; -------------------------------------------------------------------------------------------------------
    $iskonto = $r['ISKONTO'];
    $satis_fiyati = $r['B_SATIS_FIYATI'];

    $mevcut_lisans = $r['MEVCUT_LISANS'];

    $satis_fiyati = str_replace(",", ".", $satis_fiyati);
    $maliyet = str_replace(",", ".", $maliyet);
    $iskonto = str_replace(",", ".", $iskonto);
    $satis_fiyati = str_replace(",", ".", $satis_fiyati);

    //echo $maliyet . " | " . $iskonto . " | " . $satis_fiyati . "\n";

    if (array_key_exists('B_SATIS_FIYATI', $oo)) {
        $iskonto = ( ( $o_maliyet - $satis_fiyati ) / ($o_maliyet === 0 ? 1 : $o_maliyet) ) * 100;
    } else if (array_key_exists('ISKONTO', $oo)) { // Satis Fiyatini Hesapla
        $satis_fiyati = ( 1 - ($iskonto / 100)) * $o_maliyet;
    } else if (array_key_exists('B_MALIYET', $oo)) { // ?
        //$iskonto = 
    } else if (array_key_exists('ADET', $oo)) { // ?
        // ???
    }
    $karlilik = ( ( $satis_fiyati - $maliyet ) / ($satis_fiyati === 0 ? 1 : $satis_fiyati) ) * 100;
    //echo $maliyet . " | " . $iskonto . " | " . $satis_fiyati . " | " . $karlilik . "\n";
    //echo $karlilik;
    if ($iskonto < 0) {
        $iskonto = "";
    }

    $stmt = $pdo->prepare("select KILIT from " . getTableName('aa_erp_kt_teklifler') . " where TEKLIF_NO=(select top 1 X_TEKLIF_NO from " . getTableName('aa_erp_kt_teklifler_urunler') . " where id=?)");
    $stmt->execute(array($r['id']));
    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

    //print_r($gelen);

    IF ($gelen['KILIT'] === 1) {
        die("KILIT");
    }

    $mcek1 = "";
    $mcek2 = array($r['ADET'], $r['id']);

//    $stmt = $pdo->prepare("select * from aa_erp_kt_mcafee_sku_sure where SKU=:sku");
//    $stmt->execute(['sku' => $r['SKU']]);
//    $gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);
//    if  ( count($gelen) != "" ) {
    $mcek1 = "SURE=?,";
    $mcek2 = array($r['SURE'], $r['ADET'], $r['id']);
//    }  

    $sat_tip = $r['SATIS_TIPI'];

    $sql = "update " . getTableName('aa_erp_kt_teklifler_urunler') . " set $mcek1 ADET= ?,MEVCUT_LISANS='$mevcut_lisans',SATIS_TIPI='$sat_tip',B_SATIS_FIYATI='$satis_fiyati', B_MALIYET = '$maliyet', ISKONTO = '$iskonto' where id = ?";
    echo $sql;
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($mcek2);
    if ($result == false) {
        throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sql);
    }
}

//delete single record from db.
function deleteSingle($pdo, $r) {
    $sql = "delete from " . getTableName('aa_erp_kt_teklifler_urunler') . " where id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array($r['id']));
    if ($result == false) {
        throw new Exception(print_r($stmt->errorInfo(), 1) . PHP_EOL . $sql);
    }
}

//add multiple records in db.
function addList($addList) {
    $pdo = getDBH();
    foreach ($addList as &$r) {
        $r['id'] = addSingle($pdo, $r);
    }
    return $addList;
}

//update multiple records in db.
function updateList($updateList) {
    $say = 0;
    $pdo = getDBH();
    foreach ($updateList as $r) {
        updateSingle($pdo, $r, $say);
        $say++;
    }
}

//delete multiple records in db.
function deleteList($deleteList) {
    $pdo = getDBH();
    foreach ($deleteList as $r) {
        deleteSingle($pdo, $r);
    }
}

//end of functions.

if (isset($_GET["pq_add"])) {
    $response = "{\"recId\": \"" . addSingle(getDBH(), $_GET) . "\"}";
} else if (isset($_GET["pq_update"])) {
    updateSingle(getDBH(), $_GET);
    $response = "{\"result\": \"success\"}";
} else if (isset($_GET["pq_delete"])) {
    deleteSingle(getDBH(), $_GET);
    $response = "{\"result\": \"success\"}";
} else if (isset($_GET["pq_batch"])) {
    $dlist = json_decode($_POST['list'], true);

    if (isset($dlist["updateList"])) {
        updateList($dlist["updateList"]);
    }
    if (isset($dlist["addList"])) {
        $dlist["addList"] = addList($dlist["addList"]);
    }
    if (isset($dlist["deleteList"])) {
        deleteList($dlist["deleteList"]);
    }

    $response = json_encode($dlist);
} else if (isset($_GET["pq_curpage"])) {//paging.
    $pq_curPage = $_GET["pq_curpage"];
    $pq_rPP = $_GET["pq_rpp"];
    $sql = "Select count(*) from " . getTableName('aa_erp_kt_teklifler_urunler') . "";
    $dbh = getDBH();
    $stmt = $dbh->query($sql);
    $total_Records = $stmt->fetchColumn();
    $skip = ($pq_rPP * ($pq_curPage - 1));
    if ($skip >= $total_Records) {
        $pq_curPage = ceil($total_Records / $pq_rPP);
        $skip = ($pq_rPP * ($pq_curPage - 1));
    }
    $sql = "Select * from " . getTableName('aa_erp_kt_teklifler_urunler') . " order by id limit OFFSET $skip ROWS FETCH NEXT $pq_rPP ROWS ONLY";
    $stmt = $dbh->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as $i => &$row) {
        $row['Discontinued'] = intToBool($row['Discontinued']);
    }
    $sb = "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($products) . "}";
    $response = $sb;
} else {
    $teklif_id = $_GET['teklif_no'] ?? $_GET['teklif_id'] ?? '';
    error_log("kt_teklif_urunler.php - teklif_id: " . $teklif_id);
    error_log("kt_teklif_urunler.php - GET: " . print_r($_GET, true));

    // Test getTableName function
    $testTableName = getTableName('aa_erp_kt_teklifler_urunler');
    error_log("kt_teklif_urunler.php - Table name test: " . $testTableName);
    error_log("kt_teklif_urunler.php - HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET'));

    $sql = "SELECT 	id,
        X_TEKLIF_NO,
	SKU,
	ACIKLAMA,
	TIP,
	SURE,
	ADET,
	B_LISTE_FIYATI,
	MEVCUT_LISANS,
	B_MALIYET,
        O_MALIYET,
	ISKONTO,
	B_SATIS_FIYATI,
CASE
WHEN B_MALIYET>0 THEN B_MALIYET*ADET
ELSE O_MALIYET*ADET
END AS T_MALIYET,
	B_SATIS_FIYATI*ADET AS T_SATIS_FIYATI,
CASE
WHEN B_MALIYET>0 THEN ( ( B_SATIS_FIYATI - B_MALIYET ) / NULLIF(B_SATIS_FIYATI,0) ) * 100
ELSE ( ( B_SATIS_FIYATI - O_MALIYET ) / NULLIF(B_SATIS_FIYATI,0) ) * 100
END AS KARLILIK,
        (select top 1 1 FROM " . getTableName('aa_erp_kt_mcafee_sku_sure') . " s where s.sku=u.SKU) AS MCSURE,
	SIRA,
        SATIS_TIPI
        FROM LKS.dbo." . getTableName('aa_erp_kt_teklifler_urunler') . " u where X_TEKLIF_NO='$teklif_id' order by SIRA";
    $dbh = getDBH();
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

//    foreach ($products as $i=> &$row)
//    {
//        $row['Discontinued']= intToBool($row['Discontinued']);
//    }

    $response = "{\"data\":" . json_encode($products) . "}";
}
echo $response;
?>

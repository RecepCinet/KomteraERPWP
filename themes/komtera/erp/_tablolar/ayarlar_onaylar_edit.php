<?php

function getDBH(){
        $serverName = "172.16.85.76";
        $options = array(
            PDO::MYSQL_ATTR_FOUND_ROWS => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );
        $conn = new PDO("sqlsrv:server=$serverName ; Database=LKS", "crm", "!!!Crm!!!", $options);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}
//for mapping of boolean values to TINYINT column in db.
function boolToInt($val){
    //return $val;
    if($val=='true'){
        return 1;
    }
    else if($val =='false'){
        return 0;
    }
}
//for mapping of number to booleans.
function intToBool($val){
    if($val==1){
        return true;
    }
    else if($val ==0){
        return false;
    }
}
//add single record in db.
function addSingle($pdo, $r){
    //$discontinued = boolToInt($r['Discontinued']);

    $sql = "insert into aa_erp_kt_ayarlar_onaylar (modul) values ('')";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute();

    if($result == false) {
        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
    }
    return $pdo->lastInsertId();
}
//update single record in db.
function updateSingle($pdo, $r){
    //$discontinued = boolToInt($r['Discontinued']);

    $sql = "update aa_erp_kt_ayarlar_onaylar set modul= ?, kural = ?, marka = ?, parametre = ?, deger = ?, kisi = ?, islem = ?, eposta = ? where id = ?";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array($r['modul'],$r['kural'], $r['marka'], $r['parametre'], $r['deger'], $r['kisi'],$r['islem'],$r['eposta'], $r['id']));

    if($result == false) {
        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
    }
}
//delete single record from db.
function deleteSingle($pdo, $r)
{
    $sql = "delete from aa_erp_kt_ayarlar_onaylar where id = ?";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(array( $r['id']));

    if($result == false) {
        throw new Exception(print_r($stmt->errorInfo(),1).PHP_EOL.$sql);
    }
}
//add multiple records in db.
function addList($addList)
{
    $pdo = getDBH();
    foreach ($addList as &$r)
    {
        $r['id'] = addSingle($pdo, $r);
    }
    return $addList;
}
//update multiple records in db.
function updateList($updateList)
{
    $pdo = getDBH();
    foreach ($updateList as $r)
    {
        updateSingle($pdo, $r);
    }
}
//delete multiple records in db.
function deleteList($deleteList)
{
    $pdo = getDBH();
    foreach ($deleteList as $r)
    {
        deleteSingle($pdo, $r);
    }
}
//end of functions.

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

    $sql = "Select count(*) from aa_erp_kt_ayarlar_onaylar";

    $dbh = getDBH();
    $stmt = $dbh->query($sql);
    $total_Records = $stmt->fetchColumn();

    $skip = ($pq_rPP * ($pq_curPage - 1));

    if ($skip >= $total_Records)
    {
        $pq_curPage = ceil($total_Records / $pq_rPP);
        $skip = ($pq_rPP * ($pq_curPage - 1));
    }

    $sql = "Select * from aa_erp_kt_ayarlar_onaylar order by id limit OFFSET $skip ROWS FETCH NEXT $pq_rPP ROWS ONLY";
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
    $sql = "Select * from aa_erp_kt_ayarlar_onaylar order by id";

    $dbh = getDBH();

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

//    foreach ($products as $i=> &$row)
//    {
//        $row['Discontinued']= intToBool($row['Discontinued']);
//    }

    $response =  "{\"data\":".json_encode($products)."}";
}
echo $response;
?>

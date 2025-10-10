<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);

if (!isset($_SESSION)) { session_start(); }
$user=$_SESSION['user'];

$com= explode(chr(13), $user['company']);
$stri="";
for ($t=0;$t<count($com);$t++) {
    if ($t>0) {
        $stri .= ",";
    }
    $stri .= "'" . $com[$t] . "'";
}
$company=explode(chr(10),$_GET['company']);
$ek="where Company in ($stri) ";

include '../_conn.php';
$sql = "Select * from aa_erp_tickets $ek";
//echo $sql;
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>
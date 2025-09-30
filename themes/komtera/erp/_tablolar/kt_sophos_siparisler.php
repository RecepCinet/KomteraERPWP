<?PHP

error_reporting(E_ALL);
ini_set('display_erros', true);
include '../../_conn.php';
$sql = "SELECT ID, cd,
CAST(REPLACE(
        CAST(REPLACE(
                CAST(REPLACE(CAST(xmldata AS NVARCHAR(MAX)), CHAR(10), ' ') AS NVARCHAR(MAX)),
        '<', '[') AS NVARCHAR(MAX)),
'>', ']') AS TEXT) as XMLD ,
belge_no , cevap , in_out
FROM " . getTableName('aa_erp_kt_edi') . "";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$response = "{\"data\":" . json_encode($data) . "}";
if (isset($_GET['callback'])) {
    echo $_GET['callback'] . '(' . $response . ')';
} else {
    echo $response;
}
?>
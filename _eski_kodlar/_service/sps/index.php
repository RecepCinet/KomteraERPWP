<?php
// http://httpnatest.sterlingcommerce.com/http?HTTPUsername=KOMToIBM&HTTPPassword=Welcome2ibm


//username=IBMToKOM&password=Welcome2Komtera
//IBMToKOM&password=Welcome2Komtera

error_reporting(E_ALL);
ini_set("display_errors", true);

$user=$_GET['username'];
$pass=$_GET['password'];

if ($user=="IBMToKOM" && $pass=="Welcome2Komtera") {
    //ok
} else {
    die ("NOK,username or password error!");
}

include '../../_conn.php';

$xmlData = file_get_contents("php://input");

error_reporting(E_ALL);
ini_set("display_errors", true);

try {
    $stmt = $conn->prepare("insert into aa_erp_kt_edi (xmldata,in_out) values (:xml,'in')");
    $stmt->execute([':xml'=>$xmlData]);
    if ($xmlData=="") {
        die ("NOK, XML data not found!");
    } else {
        echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?><Response><Code>200</Code>
<Status>OK</Status>
</Response>';
    }
} catch(PDOException $e) {
    echo "NOK, Error: " . $e->getMessage();
}

// XML verisini parse et
// $xml = simplexml_load_string($xmlData);
// Örnek olarak, bir elementin değerini yazdır
// echo $xml->elementAdi;  // 'elementAdi', parse ettiğiniz XML içindeki bir elementin adı olmalı.

?>
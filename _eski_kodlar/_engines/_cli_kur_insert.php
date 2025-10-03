<?PHP
error_reporting(E_ALL);
//ini_set("display_errors",true);

//include 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\_conn.php';

$ch = curl_init();
$url = 'https://api.yapikredi.com.tr/api/investmentrates/v1/currencyRates';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
$response = curl_exec($ch);
curl_close($ch);

var_dump($response);

?>

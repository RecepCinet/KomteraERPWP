<?PHP
$usd_buying = "";
$euro_buying = "";
$ek = "https://www.tcmb.gov.tr/kurlar/today.xml";
$gelen = file_get_contents($ek);
if ($gelen) {
    $connect_web = simplexml_load_file($ek);
    $usd_buying = $connect_web->Currency[0]->ForexBuying;
    $usd_selling = $connect_web->Currency[0]->ForexSelling;
    $euro_buying = $connect_web->Currency[3]->ForexBuying;
    $euro_selling = $connect_web->Currency[3]->ForexSelling;
    //echo "USD Alış $usd_buying USD Satış $usd_selling EUR Alış $euro_buying EUR Satış $euro_selling";
    include '../_conn.php';
    echo $euro_buying . " - ";
    echo $usd_buying;
    if ($euro_buying!="" && $usd_buying!="") { 
    $sqlinsert="insert into aa_erp_kur (usd,eur) values ('$usd_buying','$euro_buying')";
    $stmt = $conn->prepare($sqlinsert);
    $stmt->execute();
    }
}
?>

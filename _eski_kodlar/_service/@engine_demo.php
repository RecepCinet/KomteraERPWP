<?php

//$bot = file_get_contents("https://api.telegram.org/bot664718848:AAFzXjnlzkcwAHPE9ihDZ_FCEBL1eEiu2Wc/sendMessage?chat_id=1535934&text=" . urlencode( "DemKargo!"));
//$demo_no
//include '../_conn.php'; sonradan include diye bu kaldirildi!
error_reporting(E_ALL);
ini_set("display_errors", true);

$statement = $conn->prepare("Select * from aa_erp_kt_demolar demo where demo.IRSALIYE_NO='...bekleniyor'");
$statement->execute();
$arr = $statement->fetchAll(PDO::FETCH_ASSOC);

print_r($arr);

foreach ($arr as $satir) {
    $sip="T10" . $satir['id'];
    $sql = "select top 1 FICHENO from LG_319_01_STFICHE where DOCODE='$sip' order by LOGICALREF desc";
    echo $sql . "\n";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($data);
    if ($data) {
        $demoyu_yap='';
        if ( $satir['ELDEN_TESLIM_MI'] == '1' ) {
            // Demo Elden Teslim Edildi
            $demoyu_yap='5';
        } else {
            // Demo Sevk Edildi
            $demoyu_yap='2';
            //
            $kargo=file_get_contents("http://172.16.84.214/kt_yurtici.php?cmd=gonderi_olustur&id=" . $satir['id'] );
        }
        $irs=$data[0]['FICHENO'];
        $update="update aa_erp_kt_demolar set KARGO_OLUSTUMU='1',KARGO_DURUM='$kargo',DEMO_DURUM='$demoyu_yap',IRSALIYE_NO='$irs' where id='" . $satir['id'] . "'";
        $stmt = $conn->prepare($update);
        $stmt->execute();
    }
}
?>
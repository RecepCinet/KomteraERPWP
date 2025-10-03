<?PHP
// Gunluk Kurlar
$string="select usd,eur from aa_erp_kur order by tarih desc";
$stmt = $conn->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$usd=$data['usd'];
$eur=$data['eur'];
?>
<table style="width: 50%;" cellpadding="3" align="left">
    <thead>
        <th style="background-color: #f2f2f2;text-align: left;height: 28px;">Günlük Kurlar</th>
    </thead>
    <tr>
        <td style="background-color: #000000;color: greenyellow;text-align: center;vertical-align: middle;"><small><small><small><small><br /></small></small></small></small><h1>$ <?PHP echo $usd . " &nbsp;&nbsp;&nbsp;&nbsp; € " . $eur; ?></h1></td>
    </tr>
</table>
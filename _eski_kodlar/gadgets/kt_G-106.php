<style>
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid aliceblue;
  color: #333333;
}
</style>
<script>
    function Sec(id) {
        FileMaker.PerformScriptWithOption("Siparis", "Ac|" + id);
    }
</script>
<?PHP
// Siparis icin Gelen SKULar
$string = "
SELECT n.SIPARIS_NO
FROM aaa_erp_kt_siparis_icin_gelen_skular_new n
LEFT JOIN aa_erp_kt_fatura_i fi ON n.SIPARIS_NO = fi.siparisNo
WHERE fi.siparisNo IS NULL
AND n.SIPARIS_NO NOT IN ('T77434-1', 'T85942-1', 'T89330-1', 'T117692-1', 'T129014-1')
GROUP BY n.SIPARIS_NO;
";
$stmt = $conn->prepare($string);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table style="width: 100%;" cellpadding="3">
    <thead>
    <th style="background-color: #f2f2f2;text-align: left;height: 28px;">Sipariş için gelen özel SKUlar</th>
    </thead>
    <tr>
        <td style="background-color: #FFFFFF;text-align: left;">
    <?PHP
    foreach ($data as $key => $satir) {
       // $tutar=$satir['DVZ_TUTAR'];
    ?>
        <a href="#" onclick="Sec('<?PHP echo $satir['SIPARIS_NO']; ?>')"><?PHP echo $satir['SIPARIS_NO']; ?></a>
    <?PHP
        }
    ?>
        </td>
    </tr>
</table>
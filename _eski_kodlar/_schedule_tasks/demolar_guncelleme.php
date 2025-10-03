<style>
    table {
        border-collapse: collapse;
    }
</style>
<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
$sql_string="INSERT INTO aa_erp_kt_demolar SELECT LOGICALREF,SKU,ACIKLAMA,MARKA,SERIAL_NO FROM aaa_erp_kt_demo_serial WHERE LOGICALREF NOT IN (SELECT LOGICALREF FROM aa_erp_kt_demolar)";
$conn->query($sql_string);

include '../_conn.php';
$FARKSTRING = "SELECT top 3 * FROM aa_erp_kt_demolar WHERE LOGICALREF NOT IN (SELECT LOGICALREF FROM aaa_erp_kt_demo_serial)";
$stmt = $conn->query($FARKSTRING);
$arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

function array_to_table($arr) {
    $out = "";
    $out_h = '<table align="center" width=100% bgcolor="#FFFFFF" border="1px solid">';
    for ($t = 0; $t < count($arr); $t++) {
        $satir = $arr[$t];
        if ($t == 0) {
            $out .= "<tr>";
            foreach ($satir as $key => $value) {
                if ($key != 'id' && $key != 'LOGICALREF') {
                    $out .= "<td><b>" . $key . "</b></td>";
                }
            }
            $out .= "</tr>";
        }
        $out .= "<tr>";
        foreach ($satir as $key => $value) {
            if ($key != 'id' && $key != 'LOGICALREF') {
                $value_type = gettype($value);
                switch ($value_type) {
                    case 'string':
                        $out .= "<td>$value</td>";
                        break;
                    case 'integer':
                        $out .= "<td>$value</td>";
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        $out .= "</tr>";
    }
    $out_f = "</table>";
    if ($out != "") {
        $out = $out_h . $out . $out_f;
    }
    return $out;
}
$varmi = array_to_table($arr);

if ($varmi != "") {
    //Mail Gonder:
    echo "Mail";
}
?>

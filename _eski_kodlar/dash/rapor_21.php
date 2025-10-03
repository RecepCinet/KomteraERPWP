<script>
    function Getir() {
        document.marka.submit();
    }
</script>

<?php
error_reporting(E_ERROR);

// Marka listelerini çekme
$aylar = "SELECT marka FROM ERP_SATIS_ANALIZ_20XX WHERE marka IS NOT NULL GROUP BY marka";
$sql = odbc_exec($conn, $aylar);
$ay = ["" => ""];
while ($rs = odbc_fetch_array($sql)) {
    $ay[] = $rs['marka'];
    if ($rs['marka'] == "MCAFEE") $ay[] = "MC-RETAIL";
}

$aylar2 = "SELECT marka FROM ERP_SATIS_ANALIZ_319_20XX WHERE Yil='2024' AND marka IS NOT NULL GROUP BY marka";
$sql2 = odbc_exec($conn, $aylar2);
$ay2 = ["" => ""];
while ($rs = odbc_fetch_array($sql2)) {
    $ay2[] = $rs['marka'];
}

$markalar = $_GET['mka'] ?? [];
$yillar = ["2017", "2018", "2019", "2020", "2021", "2022", "2023", "2024", "2025"];

// Veri çekme fonksiyonu
function getData($conn, $yil, $markalar) {
    $standard = ($yil >= 2021) ? "ERP_SATIS_ANALIZ_20XX WHERE yil='$yil'" : "ERP_SATIS_ANALIZ_$yil WHERE 1=1";
    if ($yil == 2024 || $yil == 2025) $standard = "ERP_SATIS_ANALIZ_319_20XX WHERE yil='$yil'";

    $condition = !empty($markalar) ? " AND (" . implode(" OR ", array_map(fn($m) => "MARKA='$m'", $markalar)) . ")" : "";

    $sqlstring = "SELECT AYRAKAM, SUM([USD_TUTAR]) AS TUTAR FROM $standard $condition GROUP BY AYRAKAM ORDER BY AYRAKAM";
    $sqlstring = str_replace("MARKA='MCAFEE-RETAIL'", "MARKA='MCAFEE' AND MTLATIN='BARIS CORBACI'", $sqlstring);
    $sqlstring = str_replace("MARKA='MCAFEE'", "MARKA='MCAFEE' AND MTLATIN<>'BARIS CORBACI'", $sqlstring);
    if ($yil == '2023') $sqlstring = str_replace("AND MTLATIN<>'BARIS CORBACI'", "", $sqlstring);

    $sql = odbc_exec($conn, $sqlstring);
    $data = [];
    while ($rs = odbc_fetch_array($sql)) {
        $data[$rs['AYRAKAM']] = $rs['TUTAR'];
    }
    return $data;
}

// Toplamları hesaplama fonksiyonu
function calculateTotals($data, $yil) {
    $toplam = $toplam_ayakadar = $buay = 0;
    $ayne = (int) date("m");
    for ($t = 1; $t <= 12; $t++) {
        $tutar = $data[$yil][$t] ?? 0;
        $toplam += $tutar;
        if ($t <= $ayne) $toplam_ayakadar += $tutar;
        if ($t == $ayne) $buay = $tutar;
    }
    return [$toplam, $toplam_ayakadar, $buay];
}

// Verileri toplama
$data = $totals = [];
foreach ($yillar as $yil) {
    $data[$yil] = getData($conn, $yil, $markalar);
    $totals[$yil] = calculateTotals($data, $yil);
}

// Checkbox formu
function printCheckboxForm($name, $items, $markalar, $menu, $altmenu) {
    echo "<FORM name='$name' id='$name' action=''>";
    echo "<input id='hepsiSec$name' type='checkbox'> Hepsini Seç<br />";
    foreach ($items as $item) {
        if ($item) {
            $checked = in_array($item, $markalar) ? "checked" : "";
            echo "<input id='hepsiSec$name' type='checkbox' name='mka[]' value='$item' $checked> $item<br />";
        }
    }
    echo "<br /><input type='submit' value='Getir'>";
    echo "<input type='hidden' name='menu' value='$menu'>";
    echo "<input type='hidden' name='altmenu' value='$altmenu'>";
    echo "</FORM>";
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    window.addEventListener("load", function() {
        ['eski', 'marka'].forEach(name => {
            document.getElementById(`hepsiSec${name}`).addEventListener("click", function() {
                document.querySelectorAll(`#hepsiSec${name}`).forEach(cb => cb.checked = this.checked);
            });
        });
    });
</script>

<div style="overflow: hidden;">
    <table border='0' align="left">
        <tr>
            <td style="width: 300px;" valign='top'>
                <div class="kaydir"><?php printCheckboxForm("eski", $ay, $markalar, $menu, $altmenu); ?></div>
            </td>
            <td>
                <div class="kaydir"><?php printCheckboxForm("marka", $ay2, $markalar, $menu, $altmenu); ?></div>
            </td>
        </tr>
    </table>
</div>

<?php
// Tablo yazdırma fonksiyonu
function printTableRow($yil, $data, $totals, $prev_totals = null) {
    echo "<tr><td bgcolor='#eee8aa'><b>$yil</b></td>";
    for ($t = 1; $t <= 12; $t++) {
        echo "<td align='right'>" . number_format($data[$yil][$t] ?? 0, 0, ",", ".") . "</td>";
    }
    echo "<td bgcolor='#eee8aa' align='right'><b>" . number_format($totals[$yil][1], 0, ",", ".") . "</b></td>";
    echo "<td bgcolor='#eee8aa' align='right'><b>" . number_format($totals[$yil][0], 0, ",", ".") . "</b></td>";
    if ($prev_totals) {
        $ayne = (int) date("m");
        $buay_degisim = ($prev_totals[2] != 0) ? ($totals[$yil][2] / $prev_totals[2]) * 100 : 0;
        $ytd_degisim = ($prev_totals[1] != 0) ? ($totals[$yil][1] / $prev_totals[1]) * 100 : 0;
        echo "<td bgcolor='#adff2f' align='right'>%" . number_format($buay_degisim, 2, ",", ".") . "</td>";
        echo "<td bgcolor='#adff2f' align='right'>%" . number_format($ytd_degisim, 2, ",", ".") . "</td>";
    }
    echo "</tr>";
}

// Tabloyu yazdırma
echo "<table id='table1'><tr><td bgcolor='#eee8aa'><b>Yıl</b></td><td bgcolor='#eee8aa'>Ocak</td><td bgcolor='#eee8aa'>Şubat</td><td bgcolor='#eee8aa'>Mart</td><td bgcolor='#eee8aa'>Nisan</td><td bgcolor='#eee8aa'>Mayıs</td><td bgcolor='#eee8aa'>Haziran</td><td bgcolor='#eee8aa'>Temmuz</td><td bgcolor='#eee8aa'>Ağustos</td><td bgcolor='#eee8aa'>Eylül</td><td bgcolor='#eee8aa'>Ekim</td><td bgcolor='#eee8aa'>Kasım</td><td bgcolor='#eee8aa'>Aralık</td><td bgcolor='#eee8aa'><b>(YTD)</b></td><td bgcolor='#eee8aa'><b>Toplam</b></td><td bgcolor='#adff2f'>Değişim (Ay)</td><td bgcolor='#adff2f'>Değişim (YTD)</td></tr>";

foreach ($yillar as $index => $yil) {
    printTableRow($yil, $data, $totals, $index > 0 ? $totals[$yillar[$index - 1]] : null);
}

echo "</table>";

$dur=1;

?>
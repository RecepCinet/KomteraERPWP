<script>
    function selectElementContents(el) {
        var body = document.body, range, sel;
        if (document.createRange && window.getSelection) {
            range = document.createRange();
            sel = window.getSelection();
            sel.removeAllRanges();
            try {
                range.selectNodeContents(el);
                sel.addRange(range);
            } catch (e) {
                range.selectNode(el);
                sel.addRange(range);
            }
            document.execCommand("copy");
        } else if (body.createTextRange) {
            range = body.createTextRange();
            range.moveToElementText(el);
            range.select();
            range.execCommand("Copy");
        }
    }
</script>
<script>
    // Rapor açma fonksiyonu
    function raporAc(raporAdi) {
        // Kullanıcıdan yıl girişi alıyoruz
        let yil = prompt('Hangi yıl için rapor almak istiyorsunuz? (Örn: 2024)', '2025');

        // Eğer kullanıcı bir yıl girdiyse ve iptal etmediyse
        if (yil !== null && yil.trim() !== '') {
            // rapor.php dosyasına rapor adı ve yıl parametresiyle yönlendirme
            window.location.href = raporAdi + '.php?yil=' + encodeURIComponent(yil);
        } else {
            // Kullanıcı iptal ederse veya boş bırakırsa uyarı
        }
    }
</script>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        border: 1px solid black;
        padding: 10px;
        vertical-align: top;
    }
    .scrollable {
        max-width: 200px;  /* Maksimum genişlik belirle */
        max-height: 100px; /* Maksimum yükseklik belirle */
        overflow: auto;    /* Taşan içerik için scroll ekle */
        white-space: nowrap; /* İçeriğin satır atlamasını engelle */
    }
</style>
<?PHP
error_reporting(E_ALL);
ini_set('display_errors', true);
//ini_set("memory_limit",9000000); Deneme 2

function latinkucuk($mesaj, $nlss="") {
        $mesaj = str_replace("ı", "i", $mesaj);
        $mesaj = str_replace("ğ", "g", $mesaj);
        $mesaj = str_replace("ü", "u", $mesaj);
        $mesaj = str_replace("ö", "o", $mesaj);
        $mesaj = str_replace("ç", "c", $mesaj);
        $mesaj = str_replace("ş", "s", $mesaj);
        $mesaj = str_replace("İ", "I", $mesaj);
        $mesaj = str_replace("Ğ", "G", $mesaj);
        $mesaj = str_replace("Ü", "U", $mesaj);
        $mesaj = str_replace("Ö", "O", $mesaj);
        $mesaj = str_replace("Ç", "C", $mesaj);
        $mesaj = str_replace("Ş", "S", $mesaj);
        $mesaj = strtolower($mesaj);
    return $mesaj;
}

?>
<html>
    <head>
        <meta charset="utf-8" />
        <style type="text/css">
            table,body,tr,td {
                font-family: verdana;
                font-size: 9pt;
            }
            table {
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid gray;
                padding: 8px;
            }
            .baslik {
                background-color: #f8d549;
                height: 30px;
            }
            .toplams {
                background-color: #CCCCCC;
                height: 30px;
            }
            a:link {
                color: black;
                text-decoration: none;
            }
            a:visited {
                color: black;
                text-decoration: none;
            }
            a:hover {
                color: black;
                background-color: yellow;
                text-decoration: none;
            }
            a:active {
                color: black;
                text-decoration: none;
            }
        </style>
<?php
session_start();
include '_func.php';
$menu = GET('menu');
$altmenu = GET('altmenu');
$rbaslik = GET('rbaslik');

$logout = GET('logout');

if (GET('logout') == "1") {
    $_SESSION['enter']=0;
    echo "ne alaka!";
    header("Location: http://172.16.84.214/dash/");
}
if (SESS('enter') != 1) {
    $menu = "login";
}
if ($menu == "") {
    $menu = "rapor";
    $altmenu = "53";
    $rbaslik = "2025 Satış Raporu - Marka";
}
?>
        <?php
        if (SESS('enter') == 1) {
            ?>
            <title></title>
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <link rel="stylesheet" href="/resources/demos/style.css">
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script>
                $(function () {
                    $("#datepicker").datepicker({
                        monthNames: ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"],
                        dayNamesMin: ["Pa", "Pt", "Sl", "Ça", "Pe", "Cu", "Ct"],
                        firstDay: 1
                    });
                });
            </script>
        </head>

        <body>
            <table style="width: 100%;" border=0>
                <tr>
                    <td align="left" valign="top" width="260px;">
    <?php
    $temp = <<<RAPOR
53|2025 Satış Raporu - Marka
54|2025 Satış Raporu - Müşteri Temsilcisi
55|2025 Satış Raporu - Komisyon
-
43|Açık Siparişler
44|Siparişler (-15 gün)
-
21|Grafik 1 Aylar ve Yıllar
22|Grafik 2 Yıl Toplamları
-
50|2024 Satış Raporu - Marka
51|2024 Satış Raporu - Müşteri Temsilcisi
52|2024 Satış Raporu - Komisyon
-
47|2023 Satış Raporu - Marka
48|2023 Satış Raporu - Müşteri Temsilcisi
49|2023 Satış Raporu - Komisyon
-
33|2022 Satış Raporu - Marka
34|2022 Satış Raporu - Müşteri Temsilcisi
35|2022 Satış Raporu - Komisyon
-
23|2021 Satış Raporu - Marka
24|2021 Satış Raporu - Müşteri Temsilcisi 
-
13|2020 Satış Raporu - Marka
14|2020 Satış Raporu - Müşteri Temsilcisi        
-
01|2019 Satış Raporu - Marka
02|2019 Satış Raporu - Müşteri Temsilcisi
-
11|2018 Satış Raporu
12|2018 Satış Raporu - Müşteri Temsilcisi                               
RAPOR;
    ?><?PHP
    $rapor = explode("\r\n", $temp);
    for ($t = 0; $t < count($rapor); $t++) {
        if ($rapor[$t] != "-") {
            $siplit = explode("|", $rapor[$t]);
            if ($rapor[$t] == $rbaslik) {
                echo "<b>";
            }
            ?><a class="dropdown-item" href="index.php?menu=rapor&altmenu=<?php echo $siplit[0]; ?>&rbaslik=<?php echo $siplit[1]; ?>"><?php echo $siplit[1] . " <small><small>" .$siplit[0] . "</small></small>"; ?></a><br /><?php
                                if ($rapor[$t] == $rbaslik) {
                                    echo "</b>";
                                }
                            } else {
                                echo "<hr>";
                            }
                        }
                    }
                    ?>
                    <?PHP
                    if (SESS('enter') == 1) {
                        ?>
                        <hr>
                        <b>Yıllara Göre Raporlar</b><br />
                        <li><a href="#" onclick="raporAc('rapor_201')">Fon Raporu (Yil) <small>201</small></a></li>
                        <li><a href="#" onclick="raporAc('rapor_204')">Gi-Komtera Urun Raporu <small>204</small></a></li>

                        <hr>
<!--                        <a href="rapor_201.php" target="_blank">Fonlar_Raporu</a><br />-->
                        <a href="rapor_202.php" target="_blank">Bayi Yetkilileri</a><br />
                        <a href="rapor_203.php" target="_blank">AcikFirsatlarSatisTipi</a><br />
                        <hr>
<!--                        <a href="log.php" target="_blank">LOG</a><br />-->
<!--                        <a href="aktivite.php" target="_blank">Aktivite</a><br />-->
<!--                        <a href="destek_tickets.php" target="_blank">Destek Tickets</a>-->
                        <br /><br /><br />
                        <a href="index.php?logout=1">Logout</a>
                        <?PHP
                    }
                    ?>
                </td>
                <td valign="top" class="scrollable">
                    <?php
                    echo "<h2>" . $rbaslik . "</h2>";
                    $dbn = "fm";
                    if ($altmenu == "1" || $altmenu == "2") {
                        $dbn = "mssql";
                    }
                    error_reporting(0);
                    $conn = odbc_connect('Logo64_LIVE', 'crm', '!!!Crm!!!');
                    if (!$conn) {
                        die("LOGO bağlantısında sorun var!");
                    }
    try {
        $conn2 = new PDO("odbc:KOMTERA2021_64", "Admin", "KlyA2gw1");
        $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "!";
        echo "--- FileMaker Data Baglanti Sorunu!---<br />" . PHP_EOL;
        die(print_r($e->getMessage()));
    }
                    error_reporting(E_ALL);
                    $dur=0;
                    include $menu . ".php";
                    if ($dur==0) {
                        $tis = Get('tarih');
                        $yilsec = Get('yillar');
                        if ($yilsec == "") {
                            $yilsec = "2024";
                        }
                        if ($tis == "") {
                            $tis = (int)date("m");
                        }
                        if ($tis <> "") {
                            $sql = str_replace("XXXXX", ' AND YEAR(f."tarihyaz_liste")=' . $yilsec . ' AND MONTH(f."tarihyaz_liste") <= ' . $tis, $sql);
                        } else {
                            $sql = str_replace("XXXXX", "", $sql);
                        }
                        if (GET('trace') == "1") {
                            echo "<br /><br /><br /><code>" . str_replace("\n", "<br />", $sql) . "</code>";
                        }
                        //echo str_replace('\n', '<br />', $sql);
                        if (strpos($sql, "ERP_SATIS_ANALIZ") > 0 or strpos($sql, "aaaa_erp_kt_komisyon_raporu") > 0) {
                            $rs = odbc_exec($conn, $sql);
                            $rs2 = odbc_exec($conn, $sql);
                        } else {
                            $rs = odbc_exec($conn2, $sql);
                            $rs2 = odbc_exec($conn2, $sql);
                        }
                        if (!$rs) {
                            exit("Error in SQL");
                        }
                        //echo str_replace("\n", "<br />", $sql);
                        if ($altmenu == "5" || $altmenu == "9") {
//echo str_replace("\n", "<br />", $sql);
                            $aylar = explode(",", "Ocak,Şubat,Mart,Nisan,Mayıs,Haziran,Temmuz,Ağustos,Eylül,Ekim,Kasım,Aralık");

                            if ($yilsec == '') {
                                $yilsec = '2020';
                            }
                            ?>
                            <form method="post">
                                <select name="yillar">
                                    <option value='2024'<?PHP
                                    if ($yilsec == '2024') {
                                        echo 'selected';
                                    }
                                    ?>>2024
                                    </option>
                                </select>
                                <select name="tarih">
                                    <?PHP
                                    for ($t = 0; $t < count($aylar); $t++) {
                                        $sel = "";
                                        if ((string)($t + 1) == (string)$tis) {
                                            $sel = " selected";
                                        }
                                        ?>
                                        <option value="<?PHP echo $t + 1; ?>"<?PHP echo $sel; ?>><?PHP echo $aylar[$t]; ?></option>
                                        <?PHP
                                    }
                                    ?>
                                </select>
                                <input type="submit" value="Filtrele">
                            </form>
                            <?php
                        }
                        $baslik = "<table id='table1' width=100%; border=1><tr>";
                        $icerik = "";
                        $kac = 0;
                        while (odbc_fetch_row($rs)) {
                            $kac++;
                        }
                        $rs = $rs2;
                        $say = 0;
                        while (odbc_fetch_row($rs)) {
                            $say++;
                            $satir = "<tr>";
                            for ($i = 1; $i <= odbc_num_fields($rs); $i++) {
                                if ($say == 1) {
                                    $baslik .= "<th class='baslik' align=center><b>" . odbc_field_name($rs, $i) . "</b></th>";
                                }
                                $b = (odbc_result($rs, odbc_field_name($rs, $i)));
                                if (isset($b)) {
                                    if (is_numeric($b)) {
                                        $b = number_format($b, 0, ",", ".");
                                    }
                                }
                                $ek1 = "";
                                $ek2 = "";
                                $renk = "";
                                if ($i == odbc_num_fields($rs) || $i == 1 || $kac == $say) {
                                    $ek1 = "<b>";
                                    $ek2 = "</b>";
                                }
                                if ($i == 1) {
                                    $renk = 'baslik';
                                    $ilk = $b;
                                }
                                if ($i == odbc_num_fields($rs) || $kac == $say) {
                                    $renk = 'toplams';
                                }
                                $align = 'left';
                                if ($i > 1) {
                                    $align = 'right';
                                }
                                $satir .= "<td class='$renk' align=$align>" . $ek1 . $b . $ek2 . "</td>";
                            }
                            $satir .= "</tr>";
                            $icerik .= $satir;
                        }
                        $baslik .= "</tr>";
                        $icerik .= "</table>";
                        //echo "<h3>" . $rbaslik . "</h3></br>";
                        echo $baslik;
                        echo $icerik;
                    }
                            odbc_close($conn);
                            ?>
                    <br /><br />
                    <input type="button" value="Tabloyu Seç Kopyala" onclick="selectElementContents( document.getElementById('table1') );">
                </td>
            </tr>
        </table>
    </body>
</html>
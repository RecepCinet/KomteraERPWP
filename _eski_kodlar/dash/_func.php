<?php
//$conn = mysqli_connect("84.51.51.220", "Utopia", "KlyA2gw1", "AktivasyonDurum");
//if (mysqli_connect_errno()) {
//    printf("Connect failed: %s\n", mysqli_connect_error());
//    exit();
//}
//mysqli_set_charset($conn, 'utf8');



function GET($key, $default = null) {
    $cikis = "";
    $cikis = isset($_GET[$key]) ? $_GET[$key] : $default;
    if ($cikis == "") {
        $cikis = isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    return $cikis;
}

function SESS($key, $default = null) {
    $cikis = "";
    $cikis = isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    return $cikis;
}

function uform1($p1, $p2, $p3) {
    global $conn;

    $sqlstring = "select gelen_telefon_modeli from liste group by gelen_telefon_modeli";
    $sql = mysqli_query($conn, $sqlstring);

    $mod = "";
    $ekle = "";
    $ayni = "";
    $sel = "";
    $temp = "";
    $msel = "";
    while ($rs = mysqli_fetch_array($sql)) {
        $parc = explode(" ", $rs['gelen_telefon_modeli']);
        $model = $parc[0] . " " . $parc[1];
        if ($temp <> $model) {
            if ($model == $p3) {
                $msel = " selected";
            }
            $mod .= "<option$msel>" . $model . "</option>";
        }
        $temp = $model;
    }
    ?>
    <div class="container">
        <form class="form-inline" method="POST">
            Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
            <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
            Model&nbsp;<select class="select2-arrow" name="model"><option value="">Hepsi</option><?PHP echo $mod; ?></select>
            &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
        </form>
    </div><br /><br />
    <?PHP
}

function uform2($p1, $p2, $p3) {
    global $conn;

    $sqlstring = "select dora_cikis_yeri,count(id) kaunt from liste where dora_cikis_yeri is not null group by dora_cikis_yeri order by kaunt desc,dora_cikis_yeri";
    $sql = mysqli_query($conn, $sqlstring);

    $mod = "";
    $ekle = "";
    $ayni = "";
    $sel = "";
    $msel = "";
    $koydum=0;
    while ($rs = mysqli_fetch_array($sql)) {
        $msel = "";
        $model = $rs['dora_cikis_yeri'];
        if ($koydum==0 && $rs['kaunt']<50) {
            $koydum=1;
            $mod .= "<option>-</option>";
        }
        //echo $model . "=" . $p3 . "<br />";
        if ($model == $p3) {
            $msel = " selected";
        }
        $mod .= "<option value='" . $model ."' $msel>" . $model . " (" . $rs['kaunt'] . ")</option>";
    }
    ?>
    <div class="container">
        <form class="form-inline" method="POST">
            Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
            <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
            Bayi&nbsp;<select class="small" name="model"><option value="">Hepsi</option><?PHP echo $mod; ?></select>
            &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
        </form>
    </div><br /><br />
    <?PHP
}




function uform3($p1, $p2) {
    global $conn;

    ?>
    <div class="container">
        <form class="form-inline" method="POST">
            Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
            <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
        </form>
    </div><br /><br />
    <?PHP
}





function uform4($p1, $p2, $p3) {
    global $conn;
    
    if ($p3=="") {
        $p3=1;
    }
    ?>
    <div class="container">
        <form class="form-inline" method="POST">
            Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
            <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;İstenen Stok Miktarı&nbsp;<input type="text" name="istenen" class="input-mini" value="<?PHP echo $p3; ?>" >
            &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
        </form>
    </div><br /><br />
    <?PHP
}







function uform6($p1, $p2, $n2, $n3, $n5, $n6, $n8, $noktamaks) {
    global $conn;
    if ($noktamaks == "") {
        $noktamaks = 0;
    }
    if ($n2=="") {
        $n2=5;
    }
    if ($n3=="") {
        $n3=5;
    }
    if ($n5=="") {
        $n5=5;
    }
    if ($n6=="") {
        $n6=5;
    }
    if ($n8=="") {
        $n8=5;
    }
    
    ?>
        <div class="container">
            <form class="form" method="POST">
                Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
                <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
                Sadece: <input type="text" name="nokta_sayisi" value="<?PHP echo $noktamaks; ?>" style="width: 50px;"> dan fazla nokta sayısı olanları getir<br /><br />
                Düşünülen Stok Miktarları&nbsp;&nbsp;N2&nbsp;<input type="text" name="n2" class="input-mini" value="<?PHP echo $n2; ?>"  style="width: 50px;">&nbsp;
                N3&nbsp;<input type="text" name="n3" class="input-mini" value="<?PHP echo $n3; ?>"  style="width: 50px;">&nbsp;
                N5&nbsp;<input type="text" name="n5" class="input-mini" value="<?PHP echo $n5; ?>"  style="width: 50px;">&nbsp;
                N6&nbsp;<input type="text" name="n6" class="input-mini" value="<?PHP echo $n6; ?>"  style="width: 50px;">&nbsp;
                N8&nbsp;<input type="text" name="n8" class="input-mini" value="<?PHP echo $n8; ?>"  style="width: 50px;">&nbsp;
                &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
            </form>
        </div><br /><br />
    <?PHP
}








function uform7($p1, $p2, $n2, $noktamaks) {
    global $conn;
    if ($noktamaks == "") {
        $noktamaks = 0;
    }
    if ($n2=="") {
        $n2=5;
    }
    
    ?>
        <div class="container">
            <form class="form" method="POST">
                Tarih&nbsp;<input type="date" name="tarih1" class="input-small" value="<?PHP echo $p1; ?>" placeholder="gg.aa.yyy">&nbsp;ile&nbsp;
                <input type="date" name="tarih2" value="<?PHP echo $p2; ?>" placeholder="gg.aa.yyy">&nbsp;arası&nbsp;&nbsp;&nbsp;
                Düşünülen Stok Miktarları&nbsp;&nbsp;<input type="text" name="n2" class="input-mini" value="<?PHP echo $n2; ?>"  style="width: 50px;">&nbsp;
                &nbsp;&nbsp;&nbsp;<button class="button button-primary">Getir</button>
            </form>
        </div><br /><br />
    <?PHP
}










function ele($param) {
    $cik = $param;
    $cik = str_replace(" TA-1020 SS TR", "", $cik);
    $cik = str_replace(" TA-1008 NV TR", "", $cik);
    $cik = str_replace(" TA-1024 SS TR", "", $cik);
    $cik = str_replace(" TA-1033 SS TR", "", $cik);
    $cik = str_replace(" TA-1012 SS TR", "", $cik);
    $cik = str_replace(" SS", "", $cik);

    return $cik;
}
?>

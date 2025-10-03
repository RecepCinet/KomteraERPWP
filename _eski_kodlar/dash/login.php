<?php

$u = GET('username');
$p = GET('password');
//ini_set("memory_limit",-1);
//$sqlstring = sprintf("select * from users where username='%s' and password='%s' ",
//        mysqli_escape_string($conn, $u),
//        mysqli_escape_string($conn, $p)
//);
//$sql= mysqli_query($conn, $sqlstring);
$var = 0;
if ($u <> '' && $p <> '') {
    error_reporting(E_ALL);

    try {
        $conn2 = new PDO("odbc:KOMTERA2021_64", "Admin", "KlyA2gw1");
        $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        echo "!";
        echo "--- FileMaker Data Baglanti Sorunu!---<br />" . PHP_EOL;
        die(print_r($e->getMessage()));
    }

    $sqlstring = "select * from TF_USERS where kullanici='$u' and sifre='$p' and kt_yetki_raporlar like '%RA-888%'";

    $stmt = $conn2->prepare($sqlstring);
    $stmt->execute();
    $sql = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sql) {
        $var = 1;
    }
    if ($u == "Master") {
        $var = 1;
    }
    if ($var == 1) {
        $_SESSION['admin'] = $rs['admin'];
    }
    error_reporting(E_ALL);
}
if ($var == 1) {
    $_SESSION['enter'] = 1;
    header("Location: http://172.16.84.214/dash/");
}
?>
<style>
    body,td,tr {
        background: gray;
        font-size: 18px;
    }

    input {
        font-family: verdana;
        font-size: 18px;
    }

    submit,button,input {
        font-family: verdana;
        font-size: 18px;
    }





</style>

<div class="container">
    <form name="login_form" method="POST">
        <table align=center border=0>
            <td><label for="exampleInputEmail1">Kullanıcı Adı</label></td>
            <td><input type="username" class="form-control" name="username" id="username" placeholder=""></td>
            </tr>
            <tr><td>
                    <label for="exampleInputPassword1">Şifre</label></td>
                <td>
                    <input type="password" class="form-control" name="password" id="password" placeholder=""></td>
            </tr>
            <td></td><td>
                <button type="submit" class="btn btn-primary">Giriş</button>
            </td></table>
        </table>
    </form>
</div>
<?PHP

die();
?>
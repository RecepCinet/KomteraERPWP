<?php

error_reporting(E_ERROR);
ini_set("display_errors",true);
set_time_limit(600);

    require '../PHPMailer/PHPMailerAutoload.php';
    
function u_sqlInsert($array) {
    $out="(";
    $out2="";
    $con="";
    $say=0;
    foreach ($array as $key=>$val) {
            $say++;
            if ($say>1) {
                $out .= ",";
                $out2 .= ",";
            }
            $out .= $key;
            $out2 .= ":" . $key;
    }
    $out .= ") values (";
    $out .= $out2;
    $out .= $con;
    $out .= ")";
    return $out;
}

function trace($param) {
    global $trace;
    if ($trace === 1) {
        $cik = print_r($param, true);
        $cik = str_replace("\n-----------\n", "<br />\n----------<br />\n", $cik);
        echo $cik;
    }
}

function Token() {
    global $port;
    $postdata = http_build_query(
        array(
            'Username' => 'Komtera',
            'Password' => 'Komtera2022--**'
        )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents("http://172.16.85.77/api/Token", false, $context);
    //echo $result;
    $arr= json_decode($result,true);
    $token=$arr['accessToken'];
    return $token;
}

function MailGonder($to, $subject, $body) {
    $body= str_replace("\n","<br />", $body);

    $mail = new PHPMailer;
    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';                   // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'bilgi@komtera.com';                // SMTP username
    $mail->CharSet = 'UTF-8';
    $mail->Password = '2F&g1D4-5!-ad7S!';                 // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->setFrom("bilgi@komtera.com", "bilgi@komtera.com");
    $mail->addAddress($to, $to);
    $mail->addAddress("recepcinet@lidyum.com.tr","recepcinet@lidyum.com.tr");
    //$mail->addReplyTo($mt_mail, $mt);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body . "<br /><br /><br />";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if (!$mail->send()) {
        echo 'NOK|HATA oluştu!, mail gönderilemedi!';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        BotMesaj($mail->ErrorInfo);
    } else {
        echo 'OK';
    }
}

function LogYazdir($message) {
    $date = date('Y-m-d H:i:s');
    $directory = 'd:\Web Publishing\web-server-support\test\fmi-test\_ERP2021\log\\';
    $filename = $directory . date('Y_m_d') . '.txt';

    if (is_array($message)) {
        $message = print_r($message, true);
    }

    if (!file_exists($filename)) {
        // Eğer klasör yoksa, oluşturur
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        touch($filename); // Dosya yoksa oluşturur
    }
    $logMessage = $date . " - " . $message . "\n";
    file_put_contents($filename, $logMessage, FILE_APPEND);
}

function MailTrace($subject, $body) {
    $url = "http".(!empty($_SERVER['HTTPS'])?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $body= str_replace("\n","<br />", $body);
    require '../PHPMailer/PHPMailerAutoload.php';
    $mail = new PHPMailer;
    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.office365.com';                   // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'bilgi@komtera.com';                // SMTP username
    $mail->CharSet = 'UTF-8';
    $mail->Password = '2F&g1D4-5!-ad7S!';                 // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to
    $mail->setFrom("bilgi@komtera.com", "bilgi@komtera.com");
    $mail->addAddress("recepcinet@lidyum.com.tr","recepcinet@lidyum.com.tr");
    //$mail->addReplyTo($mt_mail, $mt);
    $mail->isHTML(false);
    $mail->Subject = "!TRACE-" . $subject;
    $mail->Body = $url . "<br /><br />" . $body . "<br /><br /><br />";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if (!$mail->send()) {
        echo 'NOK|HATA oluştu!, mail gönderilemedi!';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        BotMesaj($mail->ErrorInfo);
    } else {
        echo 'OK';
    }
}

?>

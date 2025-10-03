<?php
// Deneme 10

error_reporting(0);
ini_set("display_errors",false);
function Token() {
    $postdata = http_build_query(
        array(
            'Username' => 'KomteraVk',
            'Password' => 'KomteraVk--**'
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
    $result = file_get_contents('http://172.16.85.107/api/Token', false, $context);
    //echo $result;
    $arr= json_decode($result,true);
    $token=$arr['accessToken'];
    return $token;
}
?>
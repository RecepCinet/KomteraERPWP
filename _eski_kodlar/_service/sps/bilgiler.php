<?php
error_reporting(E_ALL);
ini_set("display_errors", true);

$xml="dd";
$response="dd";

file_put_contents('d:\log.txt', Date("Y-m-d H:i:s") . PHP_EOL, FILE_APPEND);
file_put_contents('d:\log.txt', $xml . PHP_EOL, FILE_APPEND);
file_put_contents('d:\log.txt', $response . PHP_EOL, FILE_APPEND);

?>
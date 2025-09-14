<?php

$params = $_GET;

//print_r($params);
//
//die();

$tablo=$params['t'];
$folder=$params['f'];

if (!isset($params['f'])) {
    $folder='tablolar';
}

include 'pq.php';
include '_' . $folder . '/kt_' . $tablo . '.html';
include '_' . $folder . '/kt_' . $tablo . '_js.php';

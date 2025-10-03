<?php

error_reporting(0);
ini_set("display_errors", false);

$cmd = $_GET['cmd'];
include '../_conn.php';

include 'tekil/' . $cmd . ".php";

?>

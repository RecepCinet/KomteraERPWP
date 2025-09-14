<?php

$serverName = "172.16.85.76,1433";
$database = "LKS";
$username = "crm";
$password = "!!!Crm!!!";

$dsn = "sqlsrv:Server=$serverName;Database=$database;Encrypt=1;TrustServerCertificate=1";
try {
    $conn = new PDO($dsn, "$username", "$password", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo $e->getMessage();
}

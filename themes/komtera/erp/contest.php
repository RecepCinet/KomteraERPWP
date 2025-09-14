<?php
$dsn = "sqlsrv:Server=172.16.85.76,1433;Database=LKS;Encrypt=1;TrustServerCertificate=1";
try {
    $pdo = new PDO($dsn, "crm", "!!!Crm!!!", [
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
    echo "OK\n";
    print_r($pdo->query("SELECT TOP 1 SYSDATETIME() AS now")->fetch());
} catch (PDOException $e) { echo $e->getMessage(); }

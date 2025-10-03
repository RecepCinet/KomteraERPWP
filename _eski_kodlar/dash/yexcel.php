<?php

if (isset($_POST["excel"]) && isset($_POST["extension"]))
{
    $extension = $_POST["extension"];
    if ($extension == "csv" || $extension == "xml")
    {
        session_start();
        $_SESSION['excel'] = $_POST['excel'];
        $filename = "pqGrid." . $extension;
        echo $filename;
    }
}
else if(isset($_GET["filename"]))
{
    $filename = $_GET["filename"];
    if ($filename == "pqGrid.csv" || $filename == "pqGrid.xml")
    {
        session_start();
        if (isset($_SESSION['excel'])) {
            $excel = $_SESSION['excel'];
            $_SESSION['excel']=null;
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Content-Type: text/plain');
            header('Content-Length: ' . strlen($excel));
            header('Connection: close');
            echo $excel;
            exit;
        }
    }
}

?>
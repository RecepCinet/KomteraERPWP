<script>
function refreshDV() {
    grid.refreshDataAndView();
    //alert("yes");
}

    function ExcelKaydet() {
    blob = grid.exportData({
        url: "export.php",
        format: 'xls',
        nopqdata: true, //applicable for JSON export.                        
        render: false
    });
    if (typeof blob === "string") {
        blob = new Blob([blob]);
    }
    saveAs(blob, new Date().toISOString() + ".xls");
    }
</script>
<?PHP
$theme="gray";
if ($tablo=="kt_demo_serial" || $tablo=="kt_stoklar" || $tablo=="kt_stoklar_satis" || $tablo=="kt_bayiler") {
    $theme="steelblue";
}
if ($tablo=="tickets") {
    $theme="steelblue";
}
if ($tablo=="kt_teklif_urunler_ozet") {
    $theme="white";
}
if ($tablo=="kt_teklif_urunler") {
    $theme="gray";
}
if ($tablo=="kt_firsatlar_kaz") {
    $theme="gray";
}
if ($tablo=="kt_firsatlar_kay") {
    $theme="red";
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<html>
<head>
<link rel="stylesheet" href="pqgrid.min.css" />
<link rel="stylesheet" href="pqgrid.ui.min.css" />
<link rel='stylesheet' href='themes/<?PHP echo $theme; ?>/pqgrid.css' />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="pqgrid.min.js"></script>
<script src="localize/pq-localize-tr.js"></script>
<script src="pqTouch/pqtouch.min.js"></script>
<script src="jsZip-2.5.0/jszip.min.js"></script>
<script src="js/base64.min.js"></script>
<script src="js/FileSaver.js"></script>
<?PHP
include "_tablolar/" . $tablo . "_js.php";
?>
</head>
<body>

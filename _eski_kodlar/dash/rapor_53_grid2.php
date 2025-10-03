<meta name="viewport" content="width=device-width, initial-scale=1.0">
<html>
<head>
    <link rel="stylesheet" href="../pqgrid.min.css"/>
    <link rel="stylesheet" href="../pqgrid.ui.min.css"/>
    <link rel='stylesheet' href='../themes/red/pqgrid.css'/>
    <link rel="stylesheet"
          href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="../pqgrid.min.js"></script>
    <script src="../localize/pq-localize-tr.js"></script>
    <script src="../pqTouch/pqtouch.min.js"></script>
    <script src="../jsZip-2.5.0/jszip.min.js"></script>
    <script src="../js/base64.min.js"></script>
    <script src="../js/FileSaver.js"></script>
</head>
<body>

<style>
    div.pq-grid {
        box-shadow: 4px 4px 10px 0px rgba(50, 50, 50, 0.75);
        margin-bottom: 12px;
        font-family: Arial;
        font-size: 12px;
    }

    div.pq-toolbar button {
        margin: 0px 5px;
    }

    button.delete_btn {
        margin: -3px 0px;
        height: 30px;
    }

    .pq-row-delete {
        text-decoration: line-through;
    }

    .pq-row-delete > .pq-grid-cell {
        background-color: pink;
    }
</style>
<div id="rapor_53"></div>
<?PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sqlstring = "SELECT
    s1.MARKA,
    s1.Ocak - COALESCE(s2.Ocak, 0) AS Ocak,
    s1.Subat - COALESCE(s2.Subat, 0) AS Subat,
    s1.Mart - COALESCE(s2.Mart, 0) AS Mart,
    s1.Nisan - COALESCE(s2.Nisan, 0) AS Nisan,
    s1.Mayis - COALESCE(s2.Mayis, 0) AS Mayis,
    s1.Haziran - COALESCE(s2.Haziran, 0) AS Haziran,
    s1.Temmuz - COALESCE(s2.Temmuz, 0) AS Temmuz,
    s1.Agustos - COALESCE(s2.Agustos, 0) AS Agustos,
    s1.Eylul - COALESCE(s2.Eylul, 0) AS Eylul,
    s1.Ekim - COALESCE(s2.Ekim, 0) AS Ekim,
    s1.Kasim - COALESCE(s2.Kasim, 0) AS Kasim,
    s1.Aralik - COALESCE(s2.Aralik, 0) AS Aralik,
    s1.TOPLAM - COALESCE(s2.TOPLAM, 0) AS TOPLAM
FROM
    (SELECT
         MARKA,
         SUM(CASE WHEN [AYRAKAM] = '01' THEN [USD_TUTAR] ELSE 0 END) AS Ocak,
         SUM(CASE WHEN [AYRAKAM] = '02' THEN [USD_TUTAR] ELSE 0 END) AS Subat,
         SUM(CASE WHEN [AYRAKAM] = '03' THEN [USD_TUTAR] ELSE 0 END) AS Mart,
         SUM(CASE WHEN [AYRAKAM] = '04' THEN [USD_TUTAR] ELSE 0 END) AS Nisan,
         SUM(CASE WHEN [AYRAKAM] = '05' THEN [USD_TUTAR] ELSE 0 END) AS Mayis,
         SUM(CASE WHEN [AYRAKAM] = '06' THEN [USD_TUTAR] ELSE 0 END) AS Haziran,
         SUM(CASE WHEN [AYRAKAM] = '07' THEN [USD_TUTAR] ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN [AYRAKAM] = '08' THEN [USD_TUTAR] ELSE 0 END) AS Agustos,
         SUM(CASE WHEN [AYRAKAM] = '09' THEN [USD_TUTAR] ELSE 0 END) AS Eylul,
         SUM(CASE WHEN [AYRAKAM] = '10' THEN [USD_TUTAR] ELSE 0 END) AS Ekim,
         SUM(CASE WHEN [AYRAKAM] = '11' THEN [USD_TUTAR] ELSE 0 END) AS Kasim,
         SUM(CASE WHEN [AYRAKAM] = '12' THEN [USD_TUTAR] ELSE 0 END) AS Aralik,
         SUM([USD_TUTAR]) AS TOPLAM
     FROM ERP_SATIS_ANALIZ_319_20XX
     WHERE Yil = '2025'
     GROUP BY MARKA) AS s1
        LEFT JOIN
    (SELECT
         MARKA,
         SUM(CASE WHEN MONTH(FATTAR) = 1 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ocak,
         SUM(CASE WHEN MONTH(FATTAR) = 2 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Subat,
         SUM(CASE WHEN MONTH(FATTAR) = 3 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mart,
         SUM(CASE WHEN MONTH(FATTAR) = 4 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Nisan,
         SUM(CASE WHEN MONTH(FATTAR) = 5 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mayis,
         SUM(CASE WHEN MONTH(FATTAR) = 6 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Haziran,
         SUM(CASE WHEN MONTH(FATTAR) = 7 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN MONTH(FATTAR) = 8 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Agustos,
         SUM(CASE WHEN MONTH(FATTAR) = 9 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Eylul,
         SUM(CASE WHEN MONTH(FATTAR) = 10 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ekim,
         SUM(CASE WHEN MONTH(FATTAR) = 11 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Kasim,
         SUM(CASE WHEN MONTH(FATTAR) = 12 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Aralik,
         SUM(COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0)) AS TOPLAM
     FROM aaaa_erp_kt_komisyon_raporu_ham
     WHERE YEAR(FATTAR) = 2025 AND FATTAR IS NOT NULL
     GROUP BY MARKA) AS s2
    ON s1.MARKA = s2.MARKA
ORDER BY s1.MARKA
";

$conn = odbc_connect('Logo64_LIVE', 'crm', '!!!Crm!!!');
if (!$conn) {
    die("LOGO bağlantısında sorun var!");
}
$result = odbc_exec($conn, $sqlstring);

$data = [];
while ($row = odbc_fetch_array($result)) {
    $data[] = $row;
}

?>
<script>
    var grid;
    $(function () {
        var colM = [
            {
                title: "MARKA",minWidth: 110,dataIndx: "MARKA"
            },
            <?PHP
            $aylar=Array("Ocak","Subat","Mart","Nisan","Mayis","Haziran","Temmuz","Agustos","Eylul","Ekim","Kasim","Aralik");
            foreach ($aylar as $ay) {
            ?>
            {
                title: "<?PHP echo $ay; ?>", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "<?PHP echo $ay; ?>"
            },
            <?PHP
            }
            ?>
            {
                title: "Toplam", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "TOPLAM"
            },
        ];
        var data = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
        var obj = {
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Excele At",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet();
                        }
                    }
                ]
            },
            showHeader: true,
            groupModel: {
                on: true,
                merge: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            showTitle: true,
            selectionModel: {type: 'cell'},
            editable: false,
            //groupModel: {on: true, dataIndx: ["MARKA"] },
            showToolbar: true,
            summaryTitle: "",
            showTop: true,
            //width: "95%",
            height: 550,
            colModel: colM,
            resizable: true,
            title: "Komisyonsuz",
            showBottom: false,
            scrollModel: {autoFit: false},
            dataModel: {data: data}
        };
        grid = pq.grid("div#rapor_53", obj);

    });
</script>
<script>
    function refreshDV() {
        grid.refreshDataAndView();
        //alert("yes");
    }

    function ExcelKaydet() {
        blob = grid.exportData({
            url: "../export.php",
            format: 'xls',
            nopqdata: false,
            render: true
        });
        if (typeof blob === "string") {
            blob = new Blob([blob]);
        }
        saveAs(blob, new Date().toISOString() + ".xls");
    }
</script>
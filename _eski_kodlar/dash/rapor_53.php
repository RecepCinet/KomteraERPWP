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
<div id="rapor_53" style="margin: 0px; height: 400px; width: 577px; position: relative; left: 0px; top: 0px; z-index: 0;"></div>
<?PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sqlstring = "select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_319_20XX
WHERE Yil='2025'
group by MARKA
ORDER BY MARKA
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
            title: "Komisyonlu",
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

<br /><br />

<div id="rapor_53_2"></div>
<?PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sqlstring = "SELECT 
    COALESCE(s.MARKA, k.MARKA) AS MARKA,
    (s.Ocak - COALESCE(k.Ocak, 0)) AS 'Ocak',
    (s.Subat - COALESCE(k.Subat, 0)) AS 'Subat',
    (s.Mart - COALESCE(k.Mart, 0)) AS 'Mart',
    (s.Nisan - COALESCE(k.Nisan, 0)) AS 'Nisan',
    (s.Mayis - COALESCE(k.Mayis, 0)) AS 'Mayis',
    (s.Haziran - COALESCE(k.Haziran, 0)) AS 'Haziran',
    (s.Temmuz - COALESCE(k.Temmuz, 0)) AS 'Temmuz',
    (s.Agustos - COALESCE(k.Agustos, 0)) AS 'Agustos',
    (s.Eylul - COALESCE(k.Eylul, 0)) AS 'Eylul',
    (s.Ekim - COALESCE(k.Ekim, 0)) AS 'Ekim',
    (s.Kasim - COALESCE(k.Kasim, 0)) AS 'Kasim',
    (s.Aralik - COALESCE(k.Aralik, 0)) AS 'Aralik',
    (s.TOPLAM - COALESCE(k.TOPLAM, 0)) AS 'TOPLAM'
FROM
    (SELECT MARKA,
           sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
           sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
           sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
           sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
           sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
           sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
           sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
           sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
           sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
           sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
           sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
           sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
           sum([USD_TUTAR]) 'TOPLAM'
    FROM ERP_SATIS_ANALIZ_319_20XX
    WHERE Yil = 2025
    GROUP BY MARKA) s
FULL OUTER JOIN
    (SELECT MARKA,
           sum(case when month(FATTAR) = 1 then DLR_FonGeliri else 0 end) 'Ocak',
           sum(case when month(FATTAR) = 2 then DLR_FonGeliri else 0 end) 'Subat',
           sum(case when month(FATTAR) = 3 then DLR_FonGeliri else 0 end) 'Mart',
           sum(case when month(FATTAR) = 4 then DLR_FonGeliri else 0 end) 'Nisan',
           sum(case when month(FATTAR) = 5 then DLR_FonGeliri else 0 end) 'Mayis',
           sum(case when month(FATTAR) = 6 then DLR_FonGeliri else 0 end) 'Haziran',
           sum(case when month(FATTAR) = 7 then DLR_FonGeliri else 0 end) 'Temmuz',
           sum(case when month(FATTAR) = 8 then DLR_FonGeliri else 0 end) 'Agustos',
           sum(case when month(FATTAR) = 9 then DLR_FonGeliri else 0 end) 'Eylul',
           sum(case when month(FATTAR) = 10 then DLR_FonGeliri else 0 end) 'Ekim',
           sum(case when month(FATTAR) = 11 then DLR_FonGeliri else 0 end) 'Kasim',
           sum(case when month(FATTAR) = 12 then DLR_FonGeliri else 0 end) 'Aralik',
           sum(DLR_FonGeliri) 'TOPLAM'
    FROM aaaa_erp_kt_komisyon_raporu
    WHERE year(FATTAR) = 2025
    GROUP BY MARKA) k
ON s.MARKA = k.MARKA
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
        grid = pq.grid("div#rapor_53_2", obj);

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


<?PHP
$dur=1;
?>
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
                title: "MARKA",minWidth: 140,dataIndx: "MARKA"
            },
            <?PHP
            $aylar=Array("Ocak","Subat","Mart","Nisan","Mayis","Haziran","Temmuz","Agustos","Eylul","Ekim","Kasim","Aralik");
            foreach ($aylar as $ay) {
            ?>
            {
                title: "<?PHP echo $ay; ?>", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 90, sortable: true, dataIndx: "<?PHP echo $ay; ?>"
            },
            <?PHP
            }
            ?>
        ];
        var data = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
        var obj = {
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Excel'e Al",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet();
                        }
                    }
                ]
            },
            showHeader: true,
            showTitle: true,
            selectionModel: {type: 'cell'},
            editable: false,
            //groupModel: {on: true, dataIndx: ["MARKA"] },
            showToolbar: true,
            showTop: true,
            width: "95%",
            height: 500,
            colModel: colM,
            resizable: true,
            title: "2025 Satış Raporu - Marka",
            showBottom: false,
            scrollModel: {autoFit: true},
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
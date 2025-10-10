<?PHP

$_SCRIPT=$_GET['_script'];
$_CMD=$_GET['_cmd'];

?>
<script>
    var grid;

    $(function () {
        var colM = [
            {title: "", editable: false, filterable: false, minWidth: 60, sortable: false, dataIndx: "CH_UNVANI", 
                render: function (ui) {
                    if (ui.rowData.BAYI==="Aktif") {
                        return "<a href='#' class='demo_yeni'>Seç</a>";
                    } else {
                        return "";
                    }
                },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_yeni")
                    //.button({ icons: { primary: 'ui-icon-zoomin'} })
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "<?PHP echo $_SCRIPT; ?>", "<?PHP echo $_CMD; ?>" + "\n" + ui.rowData.CH_KODU );
                    });
            }
            },
            {title: "CH Ünvanı", editable: false, minWidth: 480, sortable: false, dataIndx: "CH_UNVANI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "CH_KODU", editable: false, minWidth: 130, sortable: false, dataIndx: "CH_KODU", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Bayi Durum", editable: false, minWidth: 100, sortable: false, dataIndx: "BAYI"},
            {title: "SORT", hidden: true, editable: false, minWidth: 100, sortable: true, dataIndx: "SORT"}
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_bayi_sec.php?dbname=LKS",
            getData: function (dataJSON) {
                var data = dataJSON.data;
                return {curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data};
            }
        };
        var obj = {
            menuIcon: false,
            trackModel: {on: true},
            collapsible: {on: false, toggle: false},
            reactive: true,
            scrollModel: {autoFit: true},
            editor: {select: true},
            sortModel: {
                type: 'remote',
                single: true,
                sorter: [{ dataIndx: 'SORT', dir: 'up' }],
                space: true,
                multiKey: false
            },
            roundCorners: false,
            rowBorders: true,
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: false,
            showTop: false,
            width: 1200, height: 400,
            dataModel: dataModelSS,
            colModel: colM,
            postRenderInterval: -1,
            rowInit: function (ui) {
            if (ui.rowData.BAYI==="Pasif" ) {
                return { 
                    style: { "color": "lightgray" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
            change: function (evt, ui) {
                //saveChanges can also be called from change event. 
            },
            destroy: function () {
                //clear the interval upon destroy.
                clearInterval(interval);
            },
            load: function (evt, ui) {
                var grid = this,
                        data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({data: data});
            },
            filterModel: {
                on: true,
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'remote',
                menuIcon: true
            },
            editable: true,
            pageModel: {
                format: "#,###",
                type: "remote",
                rPP: 12,
                strRpp: "{0}",
                rPPOptions: [12]
            },
            rowHt: 22,
            wrap: false, hwrap: false,
            numberCell: {show: false, resizable: true, width: 30, title: "#"},
            resizable: true,
//            create: function () {
//                this.loadState({refresh: false});
//            },
        };
        grid = pq.grid("div#grid_bayi_sec", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })

    });
</script>

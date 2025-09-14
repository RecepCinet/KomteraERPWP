<?PHP

$_SCRIPT=$_GET['_script'];
$_CMD=$_GET['_cmd'];

?>
<script>
    var grid;
    $(function () {
        var colM = [
            {title: "", editable: false, filterable: false, minWidth: 60, sortable: false, dataIndx: "id", 
                render: function (ui) {
                    return "<a href='#' class='demo_yeni'><?php echo __('select','komtera'); ?></a>";
                },
                postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_yeni")
                    //.button({ icons: { primary: 'ui-icon-zoomin'} })
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "<?PHP echo $_SCRIPT; ?>", "<?PHP echo $_CMD; ?>" + "\n" + ui.rowData.id ) ;
                    });
                }
            },
            {title: "", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('customer','komtera'); ?>", editable: false, minWidth: 710, sortable: false, dataIndx: "musteri", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_musteri_sec.php?dbname=LKS",
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
        grid = pq.grid("div#grid_musteri_sec", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })

    });
</script>

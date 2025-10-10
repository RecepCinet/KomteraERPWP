<script>
function repeatString(str, num) {
    out = '';
    for (var i = 0; i < num; i++) {
        out += str;
    }
    return out;
}

</script>
    <?PHP
$teklif_id = $_GET['teklif_id'];
?>
<script>
    var grid;
    $(function () {
        var colM = [
            {title: "i", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id"},
            //{title: "Sira",formatter: "number", hidden: false, editable: true, minWidth: 50, sortable: true, dataIndx: "SIRA"},
            {title: "SKU", hidden: false, editable: true, minWidth: 100, sortable: false, dataIndx: "SKU"},
            {title: "Açıklama", hidden: false, editable: true, minWidth: 350, sortable: false, dataIndx: "ACIKLAMA"},
            {title: "Bir Fiy",summary: {type: "sum", edit: true}, align: "right", format: "#,###.00", hidden: false, editable: false, minWidth: 70, sortable: false, dataIndx: "B_SATIS_FIYATI"},
            {title: "Adet", align: "center", hidden: false, editable: false, minWidth: 45, sortable: false, dataIndx: "ADET"},
            {title: "Top Sat Fiy",summary: {type: "sum", edit: true}, align: "right", format: "#,###.00", hidden: false, editable: false, minWidth: 90, sortable: false, dataIndx: "T_SATIS_FIYATI"},
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_teklif_urunler_ozet.php?dbname=LKS&teklif_id=<?PHP echo $teklif_id; ?>",
            getData: function (response) {
                return {data: response.data};
            }
        };
        var obj = {
            menuIcon: false,
            trackModel: {on: false},
            collapsible: {on: false, toggle: false},
            reactive: false,
            scrollModel: {autoFit: true},
            editor: {select: false},
            sortModel: {
                type: 'local',
                single: true,
                sorter: [ {dataIndx: 'SIRA', dir: 'up', formatter: "number"} ],
                space: true,
                multiKey: false
            },
            
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: false,
            showTitle: false,
            roundCorners: false,
            columnBorders: false,
            rowBorders: false,
            numberCell: { show: false },
//            groupModel: {on: true,
//                showSummary: [false],
//                grandSummary: true,
//                collapsed: [false, false],
//                title: '{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0}',
//            }, // , dataIndx: ["DURUM"]
            showToolbar: false,
            showTop: false,
            width: 1200, height: 400,
            dataModel: dataModelSS,
            colModel: colM,
            postRenderInterval: -1,
            change: function (evt, ui) {
                //saveChanges can also be called from change event. 
            },
            destroy: function () {
                //clear the interval upon destroy.
                clearInterval(interval);
            },

            // ROW Komple:
//            rowInit: function (ui) {
//                 if (ui.rowData.B_LISTE_FIYATI>0) {
//                    return {
//                        style: {"background": "yellow"} //can also return attr (for attributes) and cls (for css classes) properties.
//                    };
//                }
//            },
            load: function (evt, ui) {
                var grid = this,
                        data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({data: data});
            },
            editable: false,
            summaryTitle: "",
            sortable: false,
            wrap: false, hwrap: false,
            //numberCell: {show: false, resizable: true, width: 30, title: "#"},
            title: 'Urunler',
            resizable: false,
            rowHt: 16,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
        };
        grid = pq.grid("div#teklif_urunler_ozet", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>

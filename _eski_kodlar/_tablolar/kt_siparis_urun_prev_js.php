<?PHP
$siparis_no = $_GET['siparis_no'];
?>
<script>

    var grid;
    $(function () {
        var colM = [
            {title: "i", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id"},
            {title: "SKU", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "SKU"},
            {title: "Açıklama", hidden: false, editable: true, minWidth: 305, sortable: false, dataIndx: "ACIKLAMA"},
            {title: "Tür", hidden: false, editable: false, minWidth: 65, sortable: false, dataIndx: "TIP"},
            {title: "Süre", align: "center", hidden: false, editable: false, minWidth: 53, sortable: false, dataIndx: "SURE"},
            {title: "Stok", align: "center", hidden: false, editable: false, minWidth: 55, sortable: false, dataIndx: "STOK"},
            {title: "Adet", align: "center", hidden: false, editable: false, minWidth: 55, sortable: false, dataIndx: "ADET"},
            {title: "Sat Fiy", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "BIRIM_FIYAT"},
            {title: "Toplam", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "TOPLAM"},
            {title: "Lisans", align: "left", format: "#.###,00", hidden: false, editable: false, minWidth: 120, sortable: false, dataIndx: "LISANS"},
            {title: "LD", align: "center", hidden: false, editable: false, minWidth: 50, sortable: false, dataIndx: "LSONUC"},
            {title: "Logo Mesaj", align: "center", hidden: false, editable: false, minWidth: 280, sortable: false, dataIndx: "LMESAJ"},
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_siparis_urun_prev.php?dbname=LKS&siparis_no=<?PHP echo $siparis_no; ?>",
            getData: function (response) {
                return {data: response.data};
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
                type: 'local',
                single: true,
                sorter: [{dataIndx: 'SIRA', dir: 'up', formatter: "number"}],
                space: true,
                multiKey: false
            },
            roundCorners: false,
            rowBorders: true,
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: false,
            groupModel: {on: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false, false],
                title: '{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0}',
            }, // , dataIndx: ["DURUM"]
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
//            rowInit: function (ui) {
//                if (typeof ui.rowData.TIP === 'undefined') {
//                    return {
//                        style: {"background": "#cccccc"} //can also return attr (for attributes) and cls (for css classes) properties.
//                    };
//                }
//                if (typeof ui.rowData.TIP === 'Komtera') {
//                    return {
//                        style: {"background": "#eeeeee"} //can also return attr (for attributes) and cls (for css classes) properties.
//                    };
//                }
//                if (ui.rowData.MCSURE === "1") {
//                    return {
//                        style: {"background": "#222222"} //can also return attr (for attributes) and cls (for css classes) properties.
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
            filterModel: {
                on: true,
                header: false,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
            editable: true,
            summaryTitle: "",
//            pageModel: {
//                format: "#,###",
//                type: "local",
//                rPP: 1000,
//                strRpp: "{0}",
//                rPPOptions: [100, 1000, 10000]
//            },
            sortable: false,
            wrap: false, hwrap: false,
            //numberCell: {show: false, resizable: true, width: 30, title: "#"},
            title: 'Urunler',
            resizable: true,
            //rowHt: 19,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
        };
        grid = pq.grid("div#siparis_urun_prev", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>

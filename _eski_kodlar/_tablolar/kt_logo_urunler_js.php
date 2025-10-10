<?PHP
$siparis_no = $_GET['siparis_no'];
?>
<script>
    
    var grid;
    $(function () {
        var colM = [
            {title: "SONUC", hidden: false, editable: true, minWidth: 60, sortable: false, dataIndx: "SONUC"},
            {title: "MESAJ", hidden: false, editable: true, minWidth: 190, sortable: false, dataIndx: "MESAJ"},
            {title: "SIPARISID", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "SIPARISID"},
            {title: "MALZEMEKOD", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "MALZEMEKOD"},
            {title: "MIKTAR", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "MIKTAR"},
            {title: "FIYAT", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "FIYAT"},
            {title: "SERI_NO", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "SERI_NO"},
            {title: "FIS_DURUMU", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "FIS_DURUMU"},
            {title: "IRSALIYE_ID", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "IRSALIYE_ID"},
            {title: "SATIR_ID", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "SATIR_ID"},
            {title: "FATURA_ID", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "FATURA_ID"},
            {title: "CD", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "CreateDate"},
            {title: "Adres1", hidden: false, editable: true, minWidth: 290, sortable: false, dataIndx: "Adres1"},
            {title: "Adres2", hidden: false, editable: true, minWidth: 290, sortable: false, dataIndx: "Adres2"},
            {title: "BayiMusteri", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "BayiMusteri"},
            {title: "Hizmetmi", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "Hizmetmi"},
            {title: "LisansSuresi", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "LisansSuresi"},
            {title: "Ambar", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "Ambar"}           
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_logo_urunler.php?dbname=LKS&siparis_no=<?PHP echo $siparis_no; ?>",
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
//            groupModel: {on: true,
//                showSummary: [true],
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
        grid = pq.grid("div#logo_urunler", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>

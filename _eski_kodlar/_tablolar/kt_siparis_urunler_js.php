<?PHP
error_reporting(E_ALL);
ini_set("display_errors", true);

$siparis_no = $_GET['siparis_no'];

$sqlstring = "select SEVKIYAT_PARCALI from aa_erp_kt_firsatlar f where f.FIRSAT_NO = (select top 1  X_FIRSAT_NO from aa_erp_kt_siparisler s where s.SIPARIS_NO ='$siparis_no')";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

$parcalama_izni = 0;
IF (!is_null($gelen)) {
    $parcalama_izni = $gelen[0]['SEVKIYAT_PARCALI'];
}

$sqlstring = "select SIPARIS_DURUM from aa_erp_kt_siparisler s where s.SIPARIS_NO = '$siparis_no'";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$siparis_durum = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['SIPARIS_DURUM'];

$parcalama_izni = 0;
IF (!is_null($gelen)) {
    $parcalama_izni = $gelen[0]['SEVKIYAT_PARCALI'];
}
?>
<script>
    function SerialSec(ff, id) {
        FileMaker.PerformScriptWithOption("Siparis", "serial_window" + "\n" + ff + "\n" + id);
    }
    function saveChanges() {
        if (!$.active && !grid.getEditCell().$cell && grid.isDirty() && grid.isValidChange({allowInvalid: true}).valid) {
            var gridChanges = grid.getChanges({format: 'byVal'});
            $.ajax({
                //url: '_tablolar/tickets_edit.php', //for ASP.NET, java
                url: '_tablolar/kt_siparis_urunler.php?pq_batch=1',
                data: {
                    list: JSON.stringify(gridChanges)
                },
                dataType: "json",
                type: "POST",
                async: true,
                beforeSend: function (jqXHR, settings) {
                    grid.option("strLoading", "Saving..");
                    grid.showLoading();
                },
                success: function (changes) {
                    //commit the changes.                
                    grid.commit({type: 'add', rows: changes.addList});
                    grid.commit({type: 'update', rows: changes.updateList});
                    grid.commit({type: 'delete', rows: changes.deleteList});
                },
                complete: function () {
                    grid.hideLoading();
                    grid.option("strLoading", $.paramquery.pqGrid.defaults.strLoading);
                    refreshDV();
                    //FileMaker.PerformScriptWithOption("Siparis", "refresh");
                }
            });
        }
    }
    interval = setInterval(saveChanges, 1000);

    var grid;
    $(function () {
        var colM = [
            {title: "id", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id"},
            //{title: "Sira",formatter: "number", hidden: false, editable: true, minWidth: 50, sortable: true, dataIndx: "SIRA"},
            {title: "SKU", hidden: false, editable: true, minWidth: 90, sortable: false, dataIndx: "SKU"},
            {title: "Açıklama", hidden: false, editable: true, minWidth: 305, sortable: false, dataIndx: "ACIKLAMA"},
            //{title: "MS", hidden: false, editable: true, minWidth: 55, sortable: false, dataIndx: "MCSURE"},
            {title: "Tür", hidden: false, editable: false, minWidth: 65, sortable: false, dataIndx: "TIP"},
            {title: "Süre", align: "center", hidden: false, editable: false, minWidth: 53, sortable: false, dataIndx: "SURE"},
//            {title: "Süre", align: "center", hidden: false, editable: true, minWidth: 55, sortable: false, dataIndx: "SURE"},
//            {title: "Düzen", align: "center", hidden: false, editable: false, minWidth: 60, sortable: false, dataIndx: "SURE",
//            render: function (ui) {
//                var out='';
//                out += '<a href="#" onclick="UrunDuzenle(\'' + ui.rowData.X_TEKLIF_NO + '\',' + ui.rowData.id + ');"><span class="ui-icon ui-icon-pencil"></span></a>';
//                return out;
//                }
//            },
            {title: "Stok", align: "center", hidden: false, editable: false, minWidth: 55, sortable: false, dataIndx: "STOK"},
            {title: "Maliyet", align: "center", format: "#.###,00", hidden: false, editable: false, minWidth: 55, sortable: false, dataIndx: "MALIYET"},
            {title: "Top Mal", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "T_MALIYET"},

            {title: "Adet", align: "center", hidden: false, editable: false, minWidth: 55, sortable: false, dataIndx: "ADET"},
            {title: "Sat Fiy", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "BIRIM_FIYAT"},
            {title: "Sat Top", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "TOPLAM"},
            {title: "SLot", align: "right", hidden: false, editable: false, minWidth: 40, sortable: false, dataIndx: "SLOT"},
            {title: "Serial Sec", align: "center", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "LISANS",
                render: function (ui) {
                    var out = '';
                    if (ui.rowData.id !== undefined && ui.rowData.SLOT > 0) {
                        out += '<a href="#" onclick="SerialSec(\'' + ui.rowData.SKU + '\',\'' + ui.rowData.id + '\');">Serial Seç</a>';
                    }
                    return out;
                }
            },
            {title: "Cihaz Seri No", align: "left", format: "#.###,00", hidden: false, editable: false, minWidth: 120, sortable: false, dataIndx: "LISANS"},
            {title: "LD", align: "center", hidden: false, editable: false, minWidth: 50, sortable: false, dataIndx: "LSONUC"},
            {title: "Logo Mesaj", align: "center", hidden: false, editable: false, minWidth: 280, sortable: false, dataIndx: "LMESAJ"},
//            {title: "Seç", style: {"background": "lightgreen"}, type: 'checkbox', cbId: 'SEC', useLabel: true, useLabel: true, align: "center", hidden: true, editable: true, minWidth: 40, sortable: false, dataIndx: "SEC"},
//            {
//                dataIndx: 'SEC',
//                dataType: 'integer',
//                cb: {header: true, check: 1, uncheck: ""},
//                hidden: true,
//                editable: function (ui) {
//                    return 1;
//                }
//            },
            {title: "Seç", style: {"background": "lightgreen"}, align: "center", hidden: true, editable: true, minWidth: 50, sortable: false, dataIndx: "SEC"},
            {title: "SAd", style: {"background": "lightgreen"}, align: "center", hidden: true, editable: true, minWidth: 50, sortable: false, dataIndx: "SEC_ADET"}
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_siparis_urunler.php?dbname=LKS&siparis_no=<?PHP echo $siparis_no; ?>",
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
        grid = pq.grid("div#siparis_urunler", obj);
        grid.toggle();
//        $(window).on('unload', function () {
//            grid.saveState();
//        });
        grid.on("destroy", function () {
            this.saveState();
        });

<?PHP
if ($parcalama_izni === "1") {
    ?>
            for (let t of colM) {
                if (t.title === "SAd" || t.title === "Seç") {
                    t.hidden = false;
                }
            }
    <?PHP
}
?>

    });
</script>

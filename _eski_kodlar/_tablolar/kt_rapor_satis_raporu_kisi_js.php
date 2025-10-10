<?PHP
session_start();
$logo_user = $_SESSION['user']['LOGO_kullanici'];
$yil = $_GET['yil'];
?>
<script>
    function TeklifAc(teklif) {
        FileMaker.PerformScriptWithOption("Teklif", "Ac" + "|" + teklif);
    }

    var grid;

    $(function () {

        var colM = [
            {title: "Ay", align: "left", right: false, minWidth: 120, sortable: true, dataIndx: "Ay", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "USD Tutarı",exportRender: true,summary: {type: "sum", edit: true}, format: "#.###,00",dataType: "float", align: "right", right: false, minWidth: 120, sortable: true, dataIndx: "Net_Usd_Tutar", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "TL Tutarı",exportRender: true,summary: {type: "sum", format: "#.###,00", edit: true},format: "#.###,00", dataType: "float",  align: "right", right: false, minWidth: 120, sortable: true, dataIndx: "Net_Tl_Tutar", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Marka", align: "left", right: false, minWidth: 130, sortable: true, dataIndx: "Marka", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Ürün Türü", align: "left", right: false, minWidth: 120, sortable: true, dataIndx: "Urun_Turu", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Satış Temsilcisi", align: "left", right: false, minWidth: 120, sortable: true, dataIndx: "Satis_Temsilcisi", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Fatura No", hidden: false, editable: false, minWidth: 120, sortable: true, dataIndx: "Fatura_No", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Tarih", align: "center", dataType: "date", format: 'dd.mm.yy', hidden: false, editable: false, minWidth: 75, sortable: true, dataIndx: "Tarih", filter: {
                    crules: [{condition: 'range'}]
                }
            },
             {title: "Sipariş No", align: "center", dataType: "date", format: 'dd.mm.yy', hidden: false, editable: false, minWidth: 80, sortable: true, dataIndx: "Siparis_No", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Ch Kodu", align: "center", dataType: "date", hidden: false, editable: false, minWidth: 90, sortable: true, dataIndx: "Ch_Kodu", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Ch Ünvanı", align: "left", dataType: "date", hidden: false, editable: false, minWidth: 220, sortable: true, dataIndx: "Ch_Unvani", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Şehir", align: "left", right: false, minWidth: 80, sortable: true, dataIndx: "Sehir", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "İlçe", align: "left", right: false, minWidth: 80, sortable: true, dataIndx: "Ilce", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Vade", align: "center", right: false, minWidth: 75, sortable: true, dataIndx: "Vade", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "SKU", align: "left", right: false, minWidth: 120, sortable: true, dataIndx: "SKU", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Ürün Adı", align: "left", right: false, minWidth: 220, sortable: true, dataIndx: "Urun_Adi", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Miktar", dataType: "Int" ,align: "right", right: false, minWidth: 50, sortable: true, dataIndx: "Miktar", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_rapor_satis_raporu_kisi.php?dbname=LKS&logo_user=<?PHP echo $logo_user; ?>&yil=<?PHP echo $yil;?>",
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
                sorter: [{dataIndx: 'sku', dir: 'up'}],
                space: true,
                multiKey: false
            },
            
            
           


         toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Export",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet();
                        }
                    }
                   
                    
                ]
            },
            
            




            history: function (evt, ui) {
                var $tb = this.toolbar(),
                        $undo = $tb.find("button:contains('Undo')"),
                        $redo = $tb.find("button:contains('Redo')");

                if (ui.canUndo != null) {
                    $undo.button("option", {disabled: !ui.canUndo});
                }
                if (ui.canRedo != null) {
                    $redo.button("option", "disabled", !ui.canRedo);
                }
                $undo.button("option", {label: 'Undo (' + ui.num_undo + ')'});
                $redo.button("option", {label: 'Redo (' + ui.num_redo + ')'});
            },
            roundCorners: false,
            rowBorders: true,
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: true,
            showToolbar: true,
            showTop: true,
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
            rowInit: function (ui) {
                if (ui.rowData.type == 'Bug') {
                    return {
                        style: {"background": "#FFEEEE"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
            },
            load: function (evt, ui) {
                var grid = this,
                        data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({data: data});
            },
            // freezeCols: 2,
            filterModel: {
                on: true,
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
            groupModel: {
                on: true,
                merge: true,
                dataIndx: ['Ay'],
                showSummary: [true],
                grandSummary: true,
                collapsed: [true],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            editable: true,
            pageModel: {
                format: "#.###,00",
                type: "local",
                rPP: 10000,
                strRpp: "{0}",
                rPPOptions: [10000]
            },

            sortable: true,
            rowHt: 17,
            summaryTitle: "",
            title: "<b>Satış Raporu ( <?PHP echo 'user: </b>' . $user['kullanici'] . '<b> logo_user: </b>' . $logo_user ; ?>)</b>",
            wrap: false, hwrap: false,
            numberCell: {show: false, resizable: true, width: 30, title: "#"},
            resizable: true,
       create: function () {
            //this.loadState({refresh: false});
       },
        };
        grid = pq.grid("div#grid_rapor_satis_raporu_kisi", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>

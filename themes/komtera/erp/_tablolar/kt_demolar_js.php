<?PHP
$date1= $_GET['date1'];
$date2= $_GET['date2'];
?>
<script>
var grid;

$(function () {
     function pqDatePicker(ui) {
            var $this = $(this);
            $this
                //.css({ zIndex: 3, position: "relative" })
                .datepicker({
                    yearRange: "-25:+0", //25 years prior to present.
                    changeYear: true,
                    changeMonth: true,
                    showButtonPanel: true,
                    onClose: function (evt, ui) {
                        $(this).focus();
                    }
                });
            //default From date
            $this.filter(".pq-from").datepicker("option", "defaultDate", new Date("01-01-2021"));
            //default To date
            $this.filter(".pq-to").datepicker("option", "defaultDate", new Date("31-12-2021"));
        }

    var colM = [
            {title: "<?php echo __('status','komtera'); ?>", editable: false, minWidth: 210, sortable: true, dataIndx: "DEMO_DURUM_TEXT",filter: { 
                        crules: [{condition: 'range',value: ['Demo Sevk Bekleniyor','Demo Sevk Edildi','Irsaliye No Bekleniyor','Elden Teslim Bekleniyor','Elden Teslim Edildi','Demo Kontrol Edildi/Kapatıldı']}]
                    }
            },
            {title: "Durum", hidden: true, editable: false, minWidth: 50, sortable: true, dataIndx: "DEMO_DURUM"},
            {title: "<?php echo __('day','komtera'); ?>",align: "center", editable: false, minWidth: 60, dataType:"float", sortable: true, dataIndx: "gun"},
            {title: "Demo",style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 80, sortable: false,
                render: function (ui) {
                        return "<a href='#' class='demo_ac'>#" + ui.rowData.id + "</a>";
                },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_ac")
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Demo", "Ac" + "\n" + ui.rowData.id );
                    });
                }
            },
            {title: "<?php echo __('waybill_no','komtera'); ?>",style: {'text-color': '#dd0000'},dataIndx: "IRSALIYE_NO", align: "center", editable: false, minWidth: 120, sortable: false
                ,render: function (ui) {
                    if (ui.cellData=="!") {
                        return {style: {'background': '#FFCCCC'}, text: "Irsaliye No Bekleniyor"};
                    } else {
                        return {text: ui.cellData, style: {'background': '#CCFFCC'}};
                    }
                },
            },
            {title: "Bayi", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYI",filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Marka", hidden: false, editable: false, minWidth: 60, sortable: true, dataIndx: "MARKA",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Teslim Alan", hidden: false, editable: false, minWidth: 110,dataType: "float", sortable: true, dataIndx: "ELDEN_TESLIM_ALAN",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "Müşteri Temsilcisi", editable: false, minWidth: 120, sortable: true, dataIndx: "MUSTERI_TEMSILCISI",filter: { 
                        crules: [{condition: 'range'}],
                    }
            },
            // {title: "SKU", editable: false, minWidth: 120, sortable: true, dataIndx: "SKU",filter: {
            //             crules: [{condition: 'contain'}],
            //         }
            // },
            // {title: "Açıklama", editable: false, minWidth: 120, sortable: true, dataIndx: "ACIKLAMA",filter: {
            //             crules: [{condition: 'contain'}],
            //         }
            // },
            // {title: "Seri No", editable: false, minWidth: 140, sortable: true, dataIndx: "SERIAL_NO",filter: {
            //             crules: [{condition: 'contain'}]
            //         }
            // },
            {title: "Kime", minWidth: 80, dataIndx: "TESLIMAT_KIME"},
            {title: "Başlangıç", format: "dd.mm.yy",minWidth: 80, dataIndx: "CD", dataType: "date"},
            {title: "Bitiş", format: "dd.mm.yy", align: "center",dataType: 'date', format: 'dd-mm-yy', editable: false, minWidth: 80, sortable: true, dataIndx: "BITIS_TARIHI",
            },

            {title: "Müşteri", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYININ_MUSTERISI",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            }
            ,
            {title: "Yetkili", editable: false, minWidth: 160, sortable: true, dataIndx: "BAYI_YETKILI",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "Telefon", editable: false, minWidth: 120, sortable: true, dataIndx: "BAYI_TELEFON",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "EPosta", editable: false, minWidth: 200, sortable: true, dataIndx: "BAYI_EPOSTA",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_demolar.php?dbname=LKS&date1=<?PHP echo $date1; ?>&date2=<?PHP echo $date2; ?>",
        getData: function (response) {
                    return { data: response.data };
        }
    };
    var obj = {
        menuIcon: false,
        trackModel: { on: true },
        collapsible: {on: false, toggle: false},
        reactive: true,
        scrollModel: { autoFit: true },            
        editor: { select: true },
        /* sortModel: {
                type: 'local',
                single: true,
                sorter: [{ dataIndx: 'sku', dir: 'up' }],
                space: true,
                multiKey: false
            }, */
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
                    $undo.button("option", { disabled: !ui.canUndo });
                }
                if (ui.canRedo != null) {
                    $redo.button("option", "disabled", !ui.canRedo);
                }
                $undo.button("option", { label: 'Undo (' + ui.num_undo + ')' });
                $redo.button("option", { label: 'Redo (' + ui.num_redo + ')' });
            },
        roundCorners: false,
        rowBorders: true,
        //selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},            
        showHeader: true,
        showTitle: true,
        groupModel: {on: true, dataIndx: ["DEMO_DURUM_TEXT"]}, // , dataIndx: ["DURUM"]
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
                    style: { "background": "#FFEEEE" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
        load: function (evt, ui) {
                var grid = this,
                    data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({ data: data });
            },
        filterModel: {
                on: true,
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
        editable: true,
        pageModel: {
            format: "#,###",
            type: "local",
            rPP: 1000,
            strRpp: "{0}",
            rPPOptions: [100, 1000, 10000]
        },
        sortable: true,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<span style="font-size: 18px;"><b><?php echo __('demolar','komtera'); ?></b></span>',
        resizable: true,
        rowHt: 23,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_demolar", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>
<script>
var grid;

function YenilemeAc(id) {
        FileMaker.PerformScriptWithOption("Yenileme", id);
}

$(function () {
   
    var colM = [
        {title: "", align: "center", hidden: false, editable: false, minWidth: 130, sortable: false, dataIndx: "X_SIPARIS_NO",
            render: function (ui) {
                var out='';
                    out += '<a href="#" onclick="YenilemeAc(\'' + ui.rowData.TEKLIF_NO + '\');">' + ui.rowData.TEKLIF_NO + '</a>';
                return out;
            },
        },

        {title: "<?php echo __('Fırsat No','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "FIRSAT_NO", filter: {
                crules: [{condition: 'contain'}] //,value: ['Açık']
            },
            render: function (ui) {
                return "<a href='#' class='demo_ac'>" + ui.rowData.FIRSAT_NO + "</a>";
            },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                $cell.find(".demo_ac")
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption("Firsat", "Ac" + "|" + ui.rowData.FIRSAT_NO );
                    });
            }
        },

        {title: "<?php echo __('Bayi','komtera'); ?>", editable: false, minWidth: 360, sortable: true, dataIndx: "BAYI_ADI",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "<?php echo __('Müşteri','komtera'); ?>", align: "left", editable: false, minWidth: 316, sortable: true, dataIndx: "MUSTERI_ADI",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('Marka','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA",filter: {
                   crules: [{condition: 'contain'}]
               }
           }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_yenilemeler.php?dbname=LKS",
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
        sortModel: {
                type: 'local',
                single: true,
                sorter: [{ dataIndx: 'tarih_bit', dir: 'down' }],
                space: true,
                multiKey: false
            },
             toolbar: {
                items: [
                {
                        type: 'button',
                        label: "<?php echo __('Yenile','komtera'); ?>",                   
                        listener: function () {
                            grid.refreshDataAndView();
                        }
                } , {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('Satır Kaydır','komtera'); ?>',
                        listener: function (evt) {                            
                            this.option('wrap', evt.target.checked);
                            this.refresh();
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
        groupModel: {on: false}, // , dataIndx: ["BAYI"]
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
            if (ui.rowData.BITTI == "Bitti") {
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
        //freezeCols: 2,
        filterModel: {
                on: true,
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
        editable: true,
//        pageModel: {
//            format: "#,###",
//            type: "local",
//            rPP: 100,
//            strRpp: "{0}",
//            rPPOptions: [100, 1000, 10000]
//        },

        sortable: true,
        rowHt: 19,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<span style="font-size: 18px;"><b><?php echo __('Yenilemeler','komtera'); ?></b></span>',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_yenilemeler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

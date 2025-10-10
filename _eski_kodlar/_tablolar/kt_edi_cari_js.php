<script>
function EditCH(chkodu) {
    FileMaker.PerformScriptWithOption ( "Ayar", "edi_cari_edit" + "|" + chkodu );
}

var grid;

$(function () {
    var colM = [
        {title: "Acc ID", editable: false, minWidth: 115, sortable: true, dataIndx: "ACCOUNT",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "CH KOD", editable: false, minWidth: 80, sortable: true, dataIndx: "CH_KODU",filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "Bayi", align: "left", editable: false, minWidth: 298, sortable: true, dataIndx: "NAME",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "Edi No", editable: false, minWidth: 70, sortable: true, dataIndx: "EDI_NO",filter: {
                   crules: [{condition: 'contain'}]
               }
           }
        ,
        {title: "", align: "center", editable: false, minWidth: 40, sortable: true, dataIndx: "SEVIYE",
            render: function (ui) {
                return "<a href='#' class='demo_ac' onclick='EditCH(\"" + ui.rowData.CH_KODU + "\",\"" + ui.rowData.MARKA + "\")'><span class='ui-icon ui-icon-pencil'></span></a>";
            }
        },
        {title: "Seviye", editable: false, minWidth:85, sortable: true, dataIndx: "LEVEL",
        filter: { 
                        crules: [{condition: 'range'}]
                    },
                     render: function (ui) {
                if (ui.cellData.toUpperCase() == 'AUTHORIZED') {
                    return { style: { "background": "white" } };
                }
                if (ui.cellData.toUpperCase() == 'SILVER') {
                    return { style: { "background": "gray" , "color": "white" } };
                }
                if (ui.cellData.toUpperCase() == 'GOLD') {
                    return { style: { "background": "yellow" } };
                }
                if (ui.cellData.toUpperCase() == 'PLATINUM') {
                    return { style: { "background": "blue" , "color": "white" } };
                }
            },
        }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_edi_cari.php?dbname=LKS",
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
                sorter: [{ dataIndx: 'sku', dir: 'up' }],
                space: true,
                multiKey: false
            },
             toolbar: {
                items: [
                {
                        type: 'button',
                        label: "Yenile",                   
                        listener: function () {
                            grid.refreshDataAndView();
                        }
                } , {
                        type: 'checkbox',
                        value: false,
                        label: 'Satır Kaydır',
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
        title: 'LOGO - EDI Cari Eşleşme',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_edi_cari", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

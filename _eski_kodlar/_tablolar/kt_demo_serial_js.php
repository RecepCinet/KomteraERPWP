<script>

var grid;
$(function () {
    var colM = [
            {title: "Marka", editable: true, minWidth: 150, sortable: true, dataIndx: "MARKA",filter: { 
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "",style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 95,
                filter: { 
                        crules: [{condition: 'contain'}]
                    },
                sortable: true, dataIndx: "KILIT",
                render: function (ui) {
                    if (ui.rowData.KILIT > 0) {
                        return "<a href='#' class='demo_ac'><b>" + ui.rowData.KILIT + "</b></a>"; // <img src='images/lock.png' width=15px;>
                    } else {
                        <?PHP if (YetkiVarmi($user['kt_AltYetki'],'DE-102')==1) { ?>
                        return "<a href='#' class='demo_yeni'>Demo Oluştur</a>";
                        <?PHP } ?>
                    }
                },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_yeni")
                    //.button({ icons: { primary: 'ui-icon-zoomin'} })
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Demo", "yeni" + "\n" + ui.rowData.SERIAL_NO + "\n" + ui.rowData.SKU + "\n" + ui.rowData.MARKA + "\n" + ui.rowData.ACIKLAMA );
                    });
                    $cell.find(".demo_ac")
                    //.button({ icons: { primary: 'ui-icon-zoomin'} })
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Demo", "ac" + "\n" + ui.rowData.KILIT );
                    });
            }
            },
//            {title: "KILIT", align: "center", editable: true, minWidth: 70, sortable: true, dataIndx: "KILIT",filter: { 
//                        crules: [{condition: 'contain'}]
//                    },
//            },
            {title: "SKU", editable: true, minWidth: 135, sortable: true, dataIndx: "SKU",filter: { 
                        crules: [{condition: 'contain'}]
                    },
            },
            {title: "Seri No", editable: true, minWidth: 145, sortable: true, dataIndx: "SERIAL_NO",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "Açıklama", editable: true, minWidth: 420, sortable: true, dataIndx: "ACIKLAMA",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            }
            // ,
            // {title: "Durum", hidden: false, editable: true, minWidth: 170, sortable: true, dataIndx: "DDURUM",filter: { 
            //             crules: [{condition: 'contain'}]
            //         }
            // }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_demo_serial.php?dbname=LKS",
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
        groupModel: {on: true,dataIndx: ["MARKA"]}, // , dataIndx: ["BAYI"]
        showToolbar: true,
        showTop: true,        
        width: 1200, height: 400,
        dataModel: dataModelSS,
        colModel: colM,
        rowInit: function (ui) {
             if (ui.rowData.SATILDI == "Satıldı") {
                return { 
                    style: { "background": "#FF9999" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
            if (ui.rowData.KILIT > 0) {
                return { 
                    style: { "background": "yellow" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
        postRenderInterval: -1,
        change: function (evt, ui) {
                //saveChanges can also be called from change event. 
            },
            destroy: function () {
                //clear the interval upon destroy.
                clearInterval(interval);
            },
            
            // ROW Komple:
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
        title: 'LOGO - Demo Stokları',
        resizable: true,
        rowHt: 23,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_demo_serial", obj);
    grid.toggle();
    $(window).on('unload', function () {
       // grid.saveState();
    });
    grid.on("destroy", function () {
        //this.saveState();
    })
    
});
</script>
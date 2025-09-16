<script>
var grid;
$(function () {
    function LigGetir (ne) {
        var out="";
        if (ne==="1") {
            out="Registered";
        } else if (ne==="2") {
            out='Silver';
        } else if (ne==="3") {
            out="Gold";
        } else if (ne==="4") {
            out="Platinum";
        }
        return out;
    }
   
    var colM = [
//        {title: "Detay", editable: false, minWidth: 80, sortable: true,
//            render: function (ui) {
//                return "<button type='button' class='delete_btn' style='height: 23px;'>Detay</button>";
//            },
//            postRender: function (ui) {
//                var grid = this,
//                    $cell = grid.getCell(ui);
//                $cell.find(".delete_btn")
//                    .button({ icons: { primary: 'ui-icon-zoomin'} })
//                    .bind("click", function (evt) {
//                        FileMaker.PerformScriptWithOption ( "Ticket", "Ac" + "\n" + ui.rowData.id );
//                        //window.location.replace("fmp://172.16.80.214/Komtera2021?script=TicketAc&param=" + ui.rowData.id);
//                    });
//            }
//        },
            {title: "",export: false, editable: false, minWidth: 30, sortable: false, dataIndx: "FIRSAT_NO",filter: false,
                    render: function (ui) {
                        return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-zoomin'></span></a>";                       
                },
                postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_ac")
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Bayiler", "BayilerPrev" + "\n" + ui.rowData.CH_KODU );
                    });
                }
            },
        {title: "Firma Ünvanı", editable: false, minWidth: 410, sortable: true, dataIndx: "CH_UNVANI",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "CH_KODU",align: "center", editable: false, minWidth: 100, sortable: true, dataIndx: "CH_KODU",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "Vade", editable: false, minWidth: 70, sortable: true, dataIndx: "VADE",filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "Dikkat Listesi",exportRender: false, render: function (ui) {
                if (ui.cellData === '1') {
                        return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-check'></span></a>";
                    }
                },
                        align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "dikkat_listesi",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
                {title: "Kara Liste",exportRender: false, render: function (ui) {
                if (ui.cellData === '1') {
                        return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-check'></span></a>";
                    }
                },align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "kara_liste",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
//        {title: "CYBEROAM",align: "center", editable: false, minWidth: 90, sortable: true, dataIndx: "CYBEROAM",
//     render: function (ui) {
//                        return LigGetir(ui.rowData.CYBEROAM);                       
//                }
//    },
        {title: "SOPHOS",align: "center", editable: false, minWidth: 105, sortable: true, dataIndx: "SOPHOS",
     render: function (ui) {
                        return LigGetir(ui.rowData.SOPHOS);                       
                }},
        {title: "WATCHGUARD",align: "center", editable: false, minWidth: 105, sortable: true, dataIndx: "WATCHGUARD",
     render: function (ui) {
                        return LigGetir(ui.rowData.WATCHGUARD);
                }}
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt__bayiler.php?dbname=LKS",
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
                       label: "Dışa Aktar",
                       icon: 'ui-icon-arrowthickstop-1-s',
                       listener: function () {
                           ExcelKaydet();
                       }
                   },
               {
                       type: 'button',
                       label: "Yenile",
                       listener: function () {
                           grid.refreshDataAndView();
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
        //groupModel: {on: true}, // , dataIndx: ["BAYI"]
        //showToolbar: true,
        //showTop: true,        
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
//        rowInit: function (ui) {
//            if (ui.rowData.CYBEROAM === '1') {
//                return { 
//                    style: { "background": "blue" } //can also return attr (for attributes) and cls (for css classes) properties.
//                };
//            }
//        },
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
        pageModel: {
            format: "#,###",
            type: "local",
            rPP: 100,
            strRpp: "{0}",
            rPPOptions: [100, 1000, 10000]
        },

        sortable: true,
        rowHt: 23,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<span style="font-size: 18px;"><b>Bayiler</b></span>',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid__bayiler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>
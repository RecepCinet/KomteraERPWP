<script>var grid;

    function SerialGoster(sku) {
        FileMaker.PerformScriptWithOption("Stoklar", "SerialGoster" + "|" + sku);
    }

$(function () {
   
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
        {title: "Marka", editable: false, minWidth: 140, sortable: true, dataIndx: "MARKA",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "SKU", editable: false, minWidth: 120, sortable: true, dataIndx: "SKU",
        filter: { 
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "S", editable: false, minWidth: 30, sortable: false, dataIndx: "SKU",
            filter: {
                crules: [{condition: 'begin'}]
            },render: function (ui) {
                    return "<a href='#' onclick='SerialGoster(\"" + ui.rowData.SKU + "\")'>" + ui.rowData.SN + "</a>";
            },
        },
        {title: "Açıklama", editable: false, minWidth: 369, sortable: true, dataIndx: "ACIKLAMA",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "Grup", editable: false, minWidth: 110, sortable: true, dataIndx: "GRUP",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "Fiili Stok",format: "#", dataType: "integer", align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "FIILI_STOK"},
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_stoklar_satis.php?dbname=LKS",
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
        groupModel: {on: true, dataIndx: ["MARKA"] },
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
        freezeCols: 1,
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
//            rPP: 1000,
//            strRpp: "{0}",
//            rPPOptions: [100, 1000, 10000]
//        },
        sortable: true,
        rowHt: 21,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<span style="font-size: 18px;"><b>Stoklar</b></span>',
        resizable: true,
//        create: function () {                              
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_stoklar_satis", obj);
    grid.toggle();
    $(window).on('unload', function () {
        //grid.saveState();
    });
    grid.on("destroy", function () {
        //this.saveState();
    })
});
</script>
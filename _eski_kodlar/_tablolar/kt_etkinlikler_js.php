<script>
var grid;

function sil(ff) {
        if (confirm('Ürünü silmek istediğiniziden emin misiniz?')) {
            $.get("../_engines/tekil_getir.php?cmd=etkinlik_sil&id=" + ff, function (data) {
                refreshDV();
            });
        } else {
            //
        }
    }
function EtkinlikAc(id) {
        FileMaker.PerformScriptWithOption("Etkinlik", "Edit" + "|" + id);
}

$(function () {
   
    var colM = [
        {title: "Marka", editable: false, minWidth: 100, sortable: true, dataIndx: "marka",filter: { 
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "Başlık", align: "left", editable: false, minWidth: 176, sortable: true, dataIndx: "baslik",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "Kodu", editable: false, minWidth: 110, sortable: true, dataIndx: "kodu",filter: { 
                   crules: [{condition: 'contain'}]
               }
           }
        ,
        {title: "Baslangıç", align: "center", editable: false, minWidth: 80, sortable: true, dataIndx: "tarih_bas"},
        {title: "Bitiş", align: "center", editable: false, minWidth: 80, sortable: true, dataIndx: "tarih_bit",
        filter: {
                        crules: [{condition: 'range'}]
                    },
                     render: function (ui) {
                if (ui.cellData == 'AUTHORIZED') {
                    return { style: { "background": "white" } };
                }
                if (ui.cellData == 'SILVER') {
                    return { style: { "background": "gray" , "color": "white" } };
                }
                if (ui.cellData == 'GOLD') {
                    return { style: { "background": "yellow" } };
                }
                if (ui.cellData == 'PLATINUM') {
                    return { style: { "background": "blue" , "color": "white" } };
                }
            },
        },
        {title: "Durum", align: "center", editable: false, minWidth: 60, sortable: true, dataIndx: "BITTI",
                filter: { 
                   crules: [{condition: 'range'}]
               }
            },
              {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA",
                render: function (ui) {
                    var out='';
                if (ui.rowData.id!== undefined) {
                    out += '<a href="#" onclick="EtkinlikAc(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-pencil" style="color: rgb(255, 0, 0);"></span> </a>';
                }
                        return out;
                },
            },
              {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA",
                render: function (ui) {
                    var out='';
                if (ui.rowData.id!== undefined) {
                    out += '<a href="#" onclick="sil(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-close" style="color: rgb(255, 0, 0);"></span> </a>';
                }
                        return out;
                },
            }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_etkinlikler.php?dbname=LKS",
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
        title: 'LOGO - Bayi Listesi',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_etkinlikler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

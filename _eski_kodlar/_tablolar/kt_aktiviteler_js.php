<?PHP

$date1= $_GET['date1'];
$date2= $_GET['date2'];

?>
<script>
var grid;

$(function () {
    var colM = [
            {title: "",style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 60, sortable: false,
                render: function (ui) {
                        return "<a href='#' class='demo_ac'>#" + ui.rowData.id + "</a>";
                },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                    $cell.find(".demo_ac")
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Aktivite", "Ac" + "\n" + ui.rowData.id );
                    });
                }
            },
            {title: "ID", hidden: true, editable: false, minWidth: 120, sortable: true, dataIndx: "id",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
             {title: "Tarih", editable: false, minWidth: 90, sortable: true, dataIndx: "TARIH",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "Müşteri Temsilcisi", editable: false, minWidth: 110, sortable: true, dataIndx: "MUSTERI_TEMSILCISI",filter: { 
                        crules: [{condition: 'range'}],
                    }
            },
            {title: "BU", align: "center", editable: false, minWidth: 60, sortable: true, dataIndx: "BU",filter: { 
                        crules: [{condition: 'range'}],
                    }
            },
            {title: "Tip", editable: false, minWidth: 120, sortable: true, dataIndx: "TIP",filter: { 
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Süre", align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "SURE",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Şehir", editable: false, minWidth: 120, sortable: true, dataIndx: "SEHIR",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Bayi Tipi", editable: false, minWidth: 90, sortable: true, dataIndx: "TIP_BAYI",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Kim İle", editable: false, minWidth: 100, sortable: true, dataIndx: "TIP_KIMILE",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
            {title: "Bayi", editable: false, minWidth: 240, sortable: true, dataIndx: "BAYI",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
            {title: "Musteri", editable: false, minWidth: 240, sortable: true, dataIndx: "MUSTERI",filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Görüşülen", editable: false, minWidth: 170, sortable: true, dataIndx: "GORUSME_KISI",filter: {
                        crules: [{condition: 'range'}]
                    }
            }
            ,
            {title: "Açıklama", editable: false, minWidth: 250, sortable: true, dataIndx: "ACIKLAMA",filter: {
                        crules: [{condition: 'range'}]
                    }
            }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_aktiviteler.php?dbname=LKS&date1=<?PHP echo $date1; ?>&date2=<?PHP echo $date2; ?>",
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
                sorter: [{ dataIndx: 'TARIH', dir: 'down' }],
                space: true,
                multiKey: false
            },
     toolbar: {
                items: [
                    {
                        type: 'checkbox',
                        value: false,
                        label: 'Satır Kaydır',
                        listener: function (evt) {                            
                            this.option('wrap', evt.target.checked);
                            this.option('autoRow', evt.target.checked);
                            this.refreshDataAndView();
                        }
                    }            
            ]
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
        freezeCols: 2,
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
        title: 'Aktiviteler',
        resizable: true,
        rowHt: 23,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_aktiviteler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>

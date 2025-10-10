<script>
var grid;

$(function () {
    var colM = [
            {title: "SKU", hidden: false, editable: false, minWidth: 210, sortable: true, dataIndx: "SKU",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_mcafee_sku_sure.php?dbname=LKS",
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
//        sortModel: {
//                type: 'local',
//                single: true,
//                sorter: [{ dataIndx: 'SKU', dir: 'up' }],
//                space: true,
//                multiKey: false
//            },
   
        roundCorners: false,
        rowBorders: true,
        //selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},            
        showHeader: true,
        showTitle: true,
        groupModel: {on: false}, // , dataIndx: ["BAYI"]
        showToolbar: false,
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
        title: 'MCAFEE Yıl Eklenecek SKU lar',
        resizable: true,
        rowHt: 23,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_kt_mcafee_sku_sure", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>

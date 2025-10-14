<script>
var grid;

$(function () {
    var colM = [
        {title: "Marka", width: 130, dataIndx: "MARKA",
            filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "CH Kodu", align: "center", width: 120, dataIndx: "CH_KODU",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Ünvan", width: 400, dataIndx: "CH_UNVANI",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Seviye Kodu", align: "center", width: 90, dataIndx: "SEVIYE"},
        {title: "Seviye", width: 120, dataIndx: "SMETIN",
            filter: {
                crules: [{condition: 'range'}]
            },
            render: function (ui) {
                if (ui.cellData == 'AUTHORIZED') {
                    return { style: { "background": "white", "color": "#333" } };
                }
                if (ui.cellData == 'SILVER') {
                    return { style: { "background": "#c0c0c0", "color": "white", "font-weight": "500" } };
                }
                if (ui.cellData == 'GOLD') {
                    return { style: { "background": "#ffd700", "color": "#333", "font-weight": "500" } };
                }
                if (ui.cellData == 'PLATINUM') {
                    return { style: { "background": "#2196F3", "color": "white", "font-weight": "500" } };
                }
            }
        }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_bayi_seviye.php?dbname=LKS",
        getData: function (response) {
                    return { data: response.data };
        }
    };

    var obj = {
        trackModel: { on: true },
        menuIcon: true,
        collapsible: {on: false, toggle: false},
        reactive: true,
        sortModel: {
            type: 'local',
            single: true,
            sorter: [{ dataIndx: 'MARKA', dir: 'up' }],
            space: true,
            multiKey: false
        },
        roundCorners: false,
        rowBorders: true,
        selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},
        showHeader: true,
        showTitle: true,
        groupModel: {on: true, collapsed: [true], menuIcon: false},
        showToolbar: true,
        showTop: true,
        width: 1200,
        height: 400,
        dataModel: dataModelSS,
        colModel: colM,
        freezeCols: 1,
        filterModel: {
            on: true,
            header: true,
            mode: "AND",
            hideRows: false,
            type: 'local',
            menuIcon: false
        },
        editable: false,
        sortable: true,
        rowHt: 23,
        wrap: false,
        hwrap: false,
        numberCell: {resizable: true, width: 55, title: "#"},
        title: 'Marka Bazlı Bayi Seviyeleri',
        resizable: true
    };
    grid = pq.grid("div#grid_bayi_seviye", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

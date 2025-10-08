<script>
var grid;

function FirsatAc(firsatNo) {
    var url = '<?php echo admin_url('admin.php?page=firsatlar_detay&firsat_no='); ?>' + encodeURIComponent(firsatNo);
    if (window.parent) {
        window.parent.location.href = url;
    } else {
        window.location.href = url;
    }
}

$(function () {
    var colM = [
            {title: "",style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 40, sortable: false,
                render: function (ui) {
                    if (ui.rowData.FIRSAT_NO) {
                        return "<a href='#' onclick='FirsatAc(\"" + ui.rowData.FIRSAT_NO + "\")'><span class='ui-icon ui-icon-zoomin'></span></a>";
                    }
                }
            },
            {title: "<?php echo __('opportunity','komtera'); ?>", editable: false, minWidth: 70, sortable: true, dataIndx: "FIRSAT_NO"},
            {title: "ID", hidden: true, editable: false, minWidth: 120, sortable: true, dataIndx: "id",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
            /* {title: "Tür", editable: false, minWidth: 60, sortable: true, dataIndx: "TUR",
        render: function (ui) {
                    if (ui.cellData === 'POC') {
                        return {style: {"background": "#FF4444",color: "#FFFFFF"}};
                    } else if (ui.cellData === 'DEMO') {
                        return {style: {"background": "#9999"}};
                    } 
                }
        }, */
            {title: "<?php echo __('status','komtera'); ?>", editable: false, minWidth: 90, sortable: true, dataIndx: "DURUM",
        render: function (ui) {
                    if (ui.cellData === '<?php echo __('lost','komtera'); ?>') {
                        return {style: {"background": "#FFAAAA"}};
                    } else if (ui.cellData === '<?php echo __('won','komtera'); ?>') {
                        return {style: {"background": "#AAFFAA"}};
                    } 
                }
                },
            {title: "<?php echo __('date','komtera'); ?>", format: "dd.mm.yy", editable: false, minWidth: 90, sortable: true, dataIndx: "CD"},
            {title: "<?php echo __('time','komtera'); ?>", dataType: "date",format: "H:i",editable: false, minWidth: 52, sortable: true, dataIndx: "CT"},
            {title: "<?php echo __('brand','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA"},
            {title: "<?php echo __('dealer','komtera'); ?>", editable: false, minWidth: 280, sortable: true, dataIndx: "BAYI_ADI"},
            {title: "<?php echo __('customer','komtera'); ?>", editable: false, minWidth: 280, sortable: true, dataIndx: "MUSTERI_ADI"},
{title: "<?php echo __('on_site','komtera'); ?>",align: "center", editable: false, minWidth: 60, sortable: true, dataIndx: "Yerinde"}  ,
{title: "<?php echo __('remote','komtera'); ?>",align: "center", editable: false, minWidth: 60, sortable: true, dataIndx: "Uzaktan"}  ,
{title: "<?php echo __('total','komtera'); ?>",align: "center", editable: false, minWidth: 60, sortable: true, dataIndx: "Toplam"}  ,

           
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_poc.php?dbname=LKS",
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
//                    {
//                        type: 'checkbox',
//                        value: false,
//                        label: 'Satır Kaydır',
//                        listener: function (evt) {                            
//                            this.option('wrap', evt.target.checked);
//                            this.option('autoRow', evt.target.checked);
//                            this.refreshDataAndView();
//                        }
//                    }            
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
//        rowInit: function (ui) {
//            if (ui.rowData.TUR == 'POC') {
//                return { 
//                    style: { "background": "#FF0000",color: "#FFFFFF" } //can also return attr (for attributes) and cls (for css classes) properties.
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
        title: 'POC',
        resizable: true,
        rowHt: 23,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_poc", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>

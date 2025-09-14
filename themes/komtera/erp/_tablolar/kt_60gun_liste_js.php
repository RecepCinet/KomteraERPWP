<?PHP
$cryp = $_GET['cryp'];
?>
<script>
var grid;

function YenilemeAc(id) {
    FileMaker.PerformScriptWithOption("Yenileme", id);
}

function listeden_cikar(t) {
    if (confirm("<?php echo $cryp . ' ' . __('confirm_log_add_remove_order','komtera'); ?>")) {
        GonderGelsin(t);
        grid.refreshDataAndView();
    } else {
        console.log("<?php echo __('cancelled','komtera'); ?>");
    }
}

function GonderGelsin(t) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "http://172.16.84.214/_engines/yenileme_listeden_cikar.php?teklif_no=" + t + "&kim=<?php echo $cryp; ?>");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            console.log("<?php echo __('response','komtera'); ?>:", xhr.responseText);
        }
    };
    xhr.send();
}

$(function () {
    var colM = [
        {title: "<?php echo __('order_no','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "SIPARIS_NO", filter: {
                crules: [{condition: 'contain'}] //,value: ['Açık']
            },
            render: function (ui) {
                    return "<a href='#' class='demo_ac'>" + ui.rowData.SIPARIS_NO + "</a>";
            },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                $cell.find(".demo_ac")
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption("Siparis", "Ac" + "|" + ui.rowData.SIPARIS_NO);
                    });
            }
        },

        {title: "<?php echo __('opportunity_no','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "FIRSAT_NO", filter: {
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

        {title: "<?php echo __('remove_from_list','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "SIPARIS_NO", filter: {
                crules: [{condition: 'contain'}] //,value: ['Açık']
            },
            render: function (ui) {
                return `<a href="#" onclick="listeden_cikar('${ui.rowData.TEKLIF_NO}');"><?php echo __('remove_from_list','komtera'); ?></a>`;
            },
            // postRender: function (ui) {
            //     var grid = this,
            //         $cell = grid.getCell(ui);
            //     $cell.find(".demo_ac")
            //         .bind("click", function (evt) {
            //             FileMaker.PerformScriptWithOption("Siparis", "Ac" + "|" + ui.rowData.SIPARIS_NO);
            //         });
            // }
        },


        // {title: "SKU", editable: false, minWidth: 160, sortable: true, dataIndx: "SKU",filter: {
        //                 crules: [{condition: 'range'}]
        //             }
        //     },
        // {title: "Açıklama ", align: "left", editable: false, minWidth: 216, sortable: true, dataIndx: "ACIKLAMA",filter: {
        //                 crules: [{condition: 'contain'}]
        //             }
        //     },
        {title: "BAYI", align: "left", editable: false, minWidth: 296, sortable: true, dataIndx: "BAYI_ADI",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "BAYI_CHKODU ", align: "left", editable: false, minWidth: 116, sortable: true, dataIndx: "BAYI_CHKODU",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('customer_name','komtera'); ?>", editable: false, minWidth: 280, sortable: true, dataIndx: "MUSTERI_ADI",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('brand','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA",filter: {
                   crules: [{condition: 'contain'}]
               }
           },
        {title: "<?php echo __('customer_representative','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MUSTERI_TEMSILCISI",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        // {dataType: "date", format: 'dd.mm.yy',title: "SiparişTarihi", editable: false, minWidth: 80, sortable: true, dataIndx: "CD",filter: {
        //         crules: [{condition: 'contain'}]
        //     }
        // }
        // ,
        {dataType: "date", format: 'dd.mm.yy',title: "<?php echo __('renewal_date','komtera'); ?>", editable: false, minWidth: 80, sortable: true, dataIndx: "YENILEMETARIHI",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        <?PHP
            if ($cryp=="gursel.tursun" || $cryp=="gokhan.ilgit" || $cryp=="recep.cinet") {

        ?>

        <?PHP
    }
    ?>
        ,
        // {title: "Serial", editable: false, minWidth: 110, sortable: true, dataIndx: "SERIAL",filter: {
        //         crules: [{condition: 'contain'}]
        //     }
        // }
        // ,
        // {title: "Lisans", editable: false, minWidth: 110, sortable: true, dataIndx: "LISANS",filter: {
        //         crules: [{condition: 'contain'}]
        //     }
        // }
        // ,
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_60gun_liste.php?dbname=LKS&cryp=<?PHP echo $cryp; ?>",
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
                sorter: [{ dataIndx: 'YENILEMETARIHI', dir: 'up' }],
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
                            this.exportData({
                                url: "export.php",
                                format: "xlsx",
                                nopqdata: true, //applicable for JSON export.
                                render: true,
                                name: "Firsatlar"
                            });
                        }
                    },
                {
                        type: 'button',
                        label: "<?php echo __('refresh','komtera'); ?>",                   
                        listener: function () {
                            grid.refreshDataAndView();
                        }
                } , {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('wrap_rows','komtera'); ?>',
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
        title: '<?php echo __('renewals','komtera'); ?>',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_kt_60gun_liste", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

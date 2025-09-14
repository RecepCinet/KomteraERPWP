<?PHP
$cryp = $_GET['cryp'];



?>
<script>
var grid;

function YenilemeAc(id) {
    FileMaker.PerformScriptWithOption("Yenileme", id);
}


$(function () {
   
    var colM = [
        {title: "<?php echo __('siparis_no','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "SIPARIS_NO", filter: {
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
        {title: "<?php echo __('firsat_no','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "FIRSAT_NO", filter: {
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

        {title: "<?php echo __('sku','komtera'); ?>", editable: false, minWidth: 160, sortable: true, dataIndx: "SKU",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "<?php echo __('aciklama','komtera'); ?>", align: "left", editable: false, minWidth: 216, sortable: true, dataIndx: "ACIKLAMA",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('bayi_ch_kodu','komtera'); ?>", align: "left", editable: false, minWidth: 116, sortable: true, dataIndx: "BAYI_CHKODU",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('marka','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA",filter: {
                   crules: [{condition: 'contain'}]
               }
           },
        {title: "<?php echo __('musteri_temsilcisi','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MUSTERI_TEMSILCISI",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {dataType: "date", format: 'dd.mm.yy',title: "<?php echo __('siparis_tarihi','komtera'); ?>", editable: false, minWidth: 80, sortable: true, dataIndx: "CD",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {dataType: "date", format: 'dd.mm.yy',title: "<?php echo __('yenileme_tarihi','komtera'); ?>", editable: false, minWidth: 80, sortable: true, dataIndx: "YENILEMETARIHI",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        <?PHP
            if ($cryp=="gursel.tursun" || $cryp=="gokhan.ilgit" || $cryp=="recep.cinet") {

        ?>
        ,
        {title: "<?php echo __('tarih_duzelt','komtera'); ?>", editable: false, minWidth: 80, sortable: true, dataIndx: "",filter: {
                crules: [{condition: 'contain'}]
            },render: function (ui) {
                var out='';
                out += '<a href="#" onclick="YenilemeAc(\'' + ui.rowData.TEKLIF_NO + '\');">' + ui.rowData.TEKLIF_NO + '</a>';
                return out;
            }
        }
        <?PHP
    }
    ?>
        ,
        {title: "<?php echo __('alis_fiyati','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "ALIS_FIYATI",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('satis_fiyati','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "SATIS_FIYATI",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('sku','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "SKU",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('aciklama','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "ACIKLAMA",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('serial','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "SERIAL",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('lisans','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "LISANS",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('adet','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "ADET",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('musteri_adi','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MUSTERI_ADI",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('musteri_yetkili','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MUSTERI_YETKILI_ISIM",filter: {
                crules: [{condition: 'contain'}]
            }
        }
        ,
        {title: "<?php echo __('bayi_yetkili','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "BAYI_YETKILI_ISIM",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('marka_manager','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA_MANAGER",filter: {
                crules: [{condition: 'contain'}]
            }
        }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_yenilemeler_liste.php?dbname=LKS&cryp=<?PHP echo $cryp; ?>",
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
                        label: "<?php echo __('export','komtera'); ?>",
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
                        label: "<?php echo __('yenile','komtera'); ?>",                   
                        listener: function () {
                            grid.refreshDataAndView();
                        }
                } , {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('satir_kaydir','komtera'); ?>',
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
        title: '<span style="font-size: 18px;"><b><?php echo __('yenilemeler','komtera'); ?></b></span>',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_yenilemeler_liste", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

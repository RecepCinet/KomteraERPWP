<script>var grid;

    function SerialGoster(sku) {
        FileMaker.PerformScriptWithOption("Stoklar", "SerialGoster" + "|" + sku);
    }

    function FaturaBas(id) {
        if (id!="") {
            if (confirm("<?php echo __('logo_ids','komtera'); ?>: " + id + "\n\n <?php echo __('will_be_invoiced','komtera'); ?>?") == true) {
                $.get("_service/z_fatura_emri.php?idler=" + id, function (data) {
                    refreshDV();
                });
            } else {
                //
            }
        } else {
            alert("<?php echo __('no_logids_selected_for_invoice','komtera'); ?>");
        }
    }

    function FaturaBasEskiTarih(id) {
        simdikiTarih = new Date();
        gun = ("0" + simdikiTarih.getDate()).slice(-2);
        ay = ("0" + (simdikiTarih.getMonth() + 1)).slice(-2); // JavaScript'te aylar 0-11 arasında sayılır.
        yil = simdikiTarih.getFullYear();

        tarih = yil + "-" + ay + "-" + gun;

        ad=prompt('<?php echo __('invoice_print_date','komtera'); ?>',tarih);

        if (ad == null) {
            console.log("<?php echo __('cancelled','komtera'); ?>");
        } else {

            if (id!="") {
                if (confirm("<?php echo __('logo_ids','komtera'); ?>: " + id + "\n\n <?php echo __('will_be_invoiced','komtera'); ?>?") == true) {
                    $.get("_service/z_fatura_emri.php?idler=" + id + "&ft=" + ad, function (data) {
                        //refreshDV();
                    });
                } else {
                    //
                }
            } else {
                alert("<?php echo __('no_logids_selected_for_invoice','komtera'); ?>");
            }


        }

    }

    function HatalilariSil() {
        $.get("_service/hatalilari_sil.php", function (data) {
            //
            refreshDV();
        });
    }

    function TekrarDene(id) {
        $.get("_service/z_fatura_tekrar_dene.php?id=" + id, function (data) {
            alert('<?php echo __('will_retry','komtera'); ?>' + id);
            refreshDV();
        });
    }

    function NoDuzelt(sip_no,fatura) {
        $.get("_service/z_fatura_no_duzelt.php?siparis_no=" + sip_no + "&fatura=" + fatura, function (data) {
            refreshDV();
        });
    }

$(function () {
   
    var colM = [
        { dataIndx: "state", maxWidth: 30, minWidth: 30, align: "center", resizable: false,
            title: "",
            menuIcon: false,
            type: 'checkBoxSelection', cls: 'ui-state-default', sortable: false, editor: false,
            dataType: 'bool',
            cb: {
                all: false, //checkbox selection in the header affect current page only.
                header: true //show checkbox in header.
            },render: function (ui) {
                if (ui.rowData.projeKodu!="FATURA" && ui.rowData.r_LogoId!='0') {
                    //
                } else {
                    return "";
                }
        }
        },
        {title: "<?php echo __('offer','komtera'); ?>", align: "center", editable: false, minWidth: 80, sortable: false, dataIndx: "_teklif_no",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('order_no','komtera'); ?>", align: "center",  editable: false, minWidth: 90, sortable: false, dataIndx: "siparisNo",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('retry','komtera'); ?>",render: function (ui) {
                if (ui.rowData.r_result=="error" || ui.rowData.r_result=="to") {
                    return "<a href='#' class='demo_ac' onclick='TekrarDene(\"" + ui.rowData.id + "\")'><b><?php echo __('retry','komtera'); ?></b></a>";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}, align: "center", editable: false, minWidth: 85, sortable: false, dataIndx: "projeKodu",filter: false
        },
        {title: "<?php echo __('project','komtera'); ?>", editable: false, minWidth: 72, sortable: false, dataIndx: "projeKodu",filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "<?php echo __('dealer','komtera'); ?>", editable: false, minWidth: 300, sortable: false, dataIndx: "bbayi",
        filter: { 
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "<?php echo __('is_invoice','komtera'); ?>", align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "_faturami",
            filter: {
                crules: [{condition: 'range'}]
            },render: function (ui) {
                if (ui.rowData._faturami=="1") {
                    return "<span class='ui-icon ui-icon-check'></span></a> ";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}
        },
        //<span class='ui-icon ui-icon-locked'></span></a>
        {title: "<?php echo __('İrsaliye', 'komtera'); ?>",align: "center", editable: false, minWidth: 60, sortable: false, dataIndx: "_status_i",filter: {
                        crules: [{condition: 'range'}]
                    },render: function (ui) {
                if (ui.rowData._status_i=="1") {
                    return "<span class='ui-icon ui-icon-check'></span></a> ";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}
            },
        {title: "<?php echo __('Fatura', 'komtera'); ?>",align: "center", editable: false, minWidth: 60, sortable: false, dataIndx: "_status_f",filter: {
                        crules: [{condition: 'range'}]
                    },render: function (ui) {
                if (ui.rowData._status_f=="1") {
                    return "<span class='ui-icon ui-icon-check'></span></a> ";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}
            },
        {title: "<?php echo __('receipt_no','komtera'); ?>",align: "center", render: function (ui) {
                if (ui.rowData.r_LogoId<1) {
                    return "<a href='#' class='demo_ac' onclick='NoDuzelt(\"" + ui.rowData.siparisNo + "\",\"" + ui.rowData._faturami + "\")'><?php echo __('fix','komtera'); ?></a>";
                } else {
                    return true;
                }}, editable: false, minWidth: 135, sortable: false, dataIndx: "r_FisNo",filter: {
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "<?php echo __('Logo ID', 'komtera'); ?>",align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "r_LogoId",filter: {
                crules: [{condition: 'begin'}]
            }
        },
        {title: "<?php echo __('result','komtera'); ?>", editable: false, minWidth: 75, sortable: false, dataIndx: "r_result",filter: {
                crules: [{condition: 'begin'}]
            }
        },
        {title: "<?php echo __('response','komtera'); ?>", editable: false, minWidth: 540, sortable: false, dataIndx: "r_response",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "<?php echo __('waybill_date','komtera'); ?>", editable: false, minWidth: 150, sortable: false, dataIndx: "irsaliyeTarihi"},
        {title: "<?php echo __('invoice_date','komtera'); ?>", editable: false, minWidth: 150, sortable: false, dataIndx: "faturaTarihi"},
        {title: "<?php echo __('Döviz Türü', 'komtera'); ?>", align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "dovizTuru",filter: false},
        {title: "<?php echo __('exchange_rate','komtera'); ?>",align: "center", editable: false, minWidth: 85, sortable: false, dataIndx: "dovizKuru",filter: false},
        {title: "<?php echo __('warehouse','komtera'); ?>",align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "ambarKodu",filter: {
                crules: [{condition: 'range'}]
            }
        }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_faturalar.php?dbname=LKS",
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
                        label: '<?php echo __('invoice','komtera'); ?>',
                        listener: function () {
                            var checked = this.Checkbox('state').getCheckedNodes().map(function(rd){
                                return rd.r_LogoId;
                            })
                            FaturaBas(checked);
                        }
                    },
                    {
                        type: 'button',
                        label: '<?php echo __('invoice_to_old_date','komtera'); ?>',
                        listener: function () {
                            var checked = this.Checkbox('state').getCheckedNodes().map(function(rd){
                                return rd.r_LogoId;
                            })
                            FaturaBasEskiTarih(checked);
                        }
                    },
                    {
                       type: 'button',
                       label: "<?php echo __('export','komtera'); ?>",
                       icon: 'ui-icon-arrowthickstop-1-s',
                       listener: function () {
                           ExcelKaydet();
                       }
                   },
                    {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('wrap_rows','komtera'); ?>',
                        listener: function (evt) {
                            this.option('wrap', evt.target.checked);
                            this.option('autoRow', evt.target.checked);
                            this.refreshDataAndView();
                        }
                    },
                    {
                        type: 'button',
                        icon: 'ui-icon-arrowreturn-1-s',
                        label: '<?php echo __('undo','komtera'); ?>',
                        options: { disabled: true },
                        listener: function () {
                            grid.history({ method: 'undo' });
                        }
                    },
                    {
                        type: 'button',
                        icon: 'ui-icon-arrowrefresh-1-s',
                        label: '<?php echo __('redo','komtera'); ?>',
                        options: { disabled: true },
                        listener: function () {
                            grid.history({ method: 'redo' });
                        }
                    },
                    {
                        type:'button',
                        label: '<?php echo __('clear_filter','komtera'); ?>',
                        listener: function(){
                            this.reset({filter: true});
                            grid.saveState();
                        }
                    },
                    {
                        type:'button',
                        label: '<?php echo __('save_design','komtera'); ?>',
                        listener: function(){
                            grid.saveState();
                        }
                    },
                    {
                        type:'button',
                        label: '<?php echo __('load_design','komtera'); ?>',
                        listener: function(){
                            grid.loadState({refresh: false});
                        }
                    },
                    {
                        type: 'button',
                        label: "<?php echo __('delete_errors','komtera'); ?>",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            HatalilariSil();
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
                $undo.button("option", { label: '<?php echo __('Geri Al','komtera'); ?>' + ' (' + ui.num_undo + ')' });
                $redo.button("option", { label: '<?php echo __('Yinele','komtera'); ?>' + ' (' + ui.num_redo + ')' });
            },
        roundCorners: false,
        rowBorders: true,
        //selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},            
        showHeader: true,
        showTitle: true,
        //groupModel: {on: true, dataIndx: ["MARKA"] },
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
        rowInit: function (ui) {
            if (ui.rowData.r_result == 'error' || ui.rowData.r_result == 'to') {
                return {
                    style: {"background": "#FFDDDD"} //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
            if (ui.rowData.r_result == 'success' && ui.rowData.projeKodu!= 'FATURA') {
                return {
                    style: {"background": "#DDFFDD"} //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
            if (ui.rowData.projeKodu == 'FATURA' && ui.rowData.r_result == 'success') {
                return {
                    style: {"background": "#CCEECC"} //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
        pageModel: {
            format: "#,###",
            type: "local",
            rPP: 1000,
            strRpp: "{0}",
            rPPOptions: [100, 1000, 10000]
        },
        sortable: false,
        rowHt: 21,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<?php echo __('logo_invoices','komtera'); ?>',
        resizable: true,
//        create: function () {                              
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_faturalar", obj);
    grid.toggle();
    $(window).on('unload', function () {
        //grid.saveState();
    });
    grid.on("destroy", function () {
        //this.saveState();
    })
});
</script>
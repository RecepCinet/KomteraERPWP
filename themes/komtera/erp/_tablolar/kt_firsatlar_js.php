<script>
    console.log("<?php echo get_user_locale(); ?>");
    console.log("<?php echo get_locale(); ?>");

    $(function () {
        function pqDatePicker(ui) {
            var $this = $(this);
            $this
                    //.css({ zIndex: 3, position: "relative" })
                    .datepicker({
                        yearRange: "-25:+0", //25 years prior to present.
                        changeYear: true,
                        changeMonth: true,
                        showButtonPanel: true,
                        onClose: function (evt, ui) {
                            $(this).focus();
                        }
                    });
            //default From date
            $this.filter(".pq-from").datepicker("option", "defaultDate", new Date("01-01-2021"));
            //default To date
            $this.filter(".pq-to").datepicker("option", "defaultDate", new Date("31-12-2021"));
        }
        var colM = [
            {title: "<?php echo __('durum','komtera');?>", editable: false, minWidth: 110, sortable: true, dataIndx: "DURUM", filter: {
                    crules: [{condition: 'range'}]
                },render: function (ui) {
                    if (ui.cellData === 'Açık') {
                        return {style: {"background": "#ebebeb"}};
                    } else if (ui.cellData === 'Kazanıldı') {
                        return {style: {"background": "#b2f4ac"}};
                    } else if (ui.cellData === 'Kaybedildi') {
                        return {style: {"background": "#f4acb8"}};
                    }
                }
            },
//            {title: "", export: false, editable: false, minWidth: 30, sortable: false, dataIndx: "FIRSAT_NO", filter: false,
//                render: function (ui) {
//                    return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-zoomin'></span></a>";
//                },
//                postRender: function (ui) {
//                    var grid = this,
//                            $cell = grid.getCell(ui);
//                    $cell.find(".demo_ac")
//                            .bind("click", function (evt) {
//                                FileMaker.PerformScriptWithOption("Firsat", "Ac" + "\nF" + ui.rowData.id);
//                            });
//                }
//            },
            {title: "<?php echo __('firsat','komtera'); ?>",render: function (ui) {
                    if (ui.rowData.FIRSAT_NO) {
                        return "<a href='#' class='demo_ac' onclick='FirsatAc(\"" + ui.rowData.FIRSAT_NO + "\")'>"+ui.rowData.FIRSAT_NO+"</a>";
                    }
                },exportRender: false, style: {'text-color': '#dd0000'}, dataIndx: "FIRSAT_NO", align: "center", editable: false, minWidth: 60, sortable: false,filter: {
                    crules: [{condition: 'contain'}]
                }},
                    
                    {title: "R",render: function (ui) {
                    if (ui.rowData.REGISTER==='1') {
                        return "<span class='ui-icon ui-icon-check'></span>";
                    }
                },exportRender: false, style: {'text-color': '#dd0000'}, dataIndx: "REGISTER", align: "center", editable: false, minWidth: 35, sortable: false,filter: {
                    crules: [{condition: 'range'}]
                }},
                    
                    
            {title: "<?php echo __('teklifler','komtera'); ?>",exportRender: false, style: {'text-color': '#dd0000'}, dataIndx: "Teklifler", align: "left", editable: false, minWidth: 90, sortable: false,
                render: function (ui) {
                    var out = "";
                    var data = ui.rowData.Teklifler;
                    if (data) {
                        var tek = data.split(',');
                        for (let i = 0; i < tek.length; i++) {
                            if (i > 0) {
                                out = out + " , ";
                            }
                            out = out + "<a href='#' class='acac' onclick='TeklifAc(\"" + tek[i] + "\")'>" + tek[i] + "</a>";
                            //out = out + "<a href='#' class='demo_ac' onclick='TeklifAc(\"" + tek[i] + "\")'><span class='ui-icon ui-icon-zoomin'></span></a>" + tek[i];
                        }
                        return out;
                    }
                },filter: {
                    crules: [{condition: 'contain'}]
                }
            },{title: "<?php echo __('skular','komtera'); ?>",filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "skular"},
            {title: "<?php echo __('cozumler','komtera'); ?>",filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "Cozumler"},

            {title: "<?php echo __('satis_tipi','komtera'); ?>", sortable: true, minWidth: 120, dataIndx: "SATIP",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('tarih','komtera'); ?>", sortable: true, minWidth: 80, dataIndx: "BASLANGIC_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('son_degisiklik','komtera'); ?>", minWidth: 80, dataIndx: "REVIZE_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('bitis_tarihi','komtera'); ?>", minWidth: 80, dataIndx: "BITIS_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "BitisAY", hidden: false, editable: false, minWidth: 70, sortable: true, dataIndx: "BITIS_AY", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('marka','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA", filter: {
                    crules: [{condition: 'range'}]
                }
            },
			{title: "<?php echo __('marka_manager','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA_MANAGER", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "ID", hidden: true, editable: false, minWidth: 110, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('kayidi_acan','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === 'KULLANICI') {
                        return {style: {"background": "yellow"}};
                    }
                },
                editable: false, minWidth: 120, sortable: true, dataIndx: "KAYIDI_ACAN", filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "<?php echo __('musteri_temsilcisi','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === 'KULLANICI') {
                        return {style: {"background": "yellow"}};
                    }
                },
                editable: false, minWidth: 120, sortable: true, dataIndx: "MUSTERI_TEMSILCISI", filter: {
                    crules: [{condition: 'range'}],
                }
            },
//            {title: "SKU", editable: false, minWidth: 120, sortable: true, dataIndx: "SKU",filter: { 
//                        crules: [{condition: 'contain'}],
//                    }
//            },
//            {title: "Seri No", editable: false, minWidth: 140, sortable: true, dataIndx: "SERIAL_NO",filter: { 
//                        crules: [{condition: 'contain'}]
//                    }
//            },
//            {title: "Bitiş", align: "center",dataType: 'date', format: 'dd-mm-yy', editable: false, minWidth: 80, sortable: true, dataIndx: "BITIS_TARIHI",
//            },
            {title: "<?php echo __('tutar','komtera'); ?>", exportRender: true,dataType: "float", render: function (ui) {
                    if (ui.cellData === null) {
                        return {style: {"background": "#FF8888"}};
                    }
                },align: "right", format: "#.###,00", editable: false, minWidth: 90, sortable: true, dataIndx: "TUTAR"},
            {title: "", editable: false, minWidth: 40, sortable: true, dataIndx: "PARA_BIRIMI"},
            {title: "USD",exportRender: true,dataType: "float",render: function (ui) {
                    if (ui.cellData === null) {
                        return {style: {"background": "#FF8888"}};
                    }
                }, summary: {type: "sum", edit: true}, align: "right", format: "#.###,00", editable: false, minWidth: 90, sortable: true, dataIndx: "DLR_TUTAR"},
            {title: "<?php echo __('bayi','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
            ,
						{title: "<?php echo __('bayi_yetkili','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "BAYI_YETKILI_ISIM", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            //     {title: "Bayi Yetkili", editable: false, minWidth: 120, sortable: true, dataIndx: "BAYI_YETKILI_ISIM", filter: {
            //         crules: [{condition: 'contain'}]
            //     }
            // },
            {title: "<?php echo __('musteri','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "MUSTERI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('olasilik','komtera'); ?>",filter: {
                    crules: [{condition: 'range'}]
                },minWidth: 210, dataIndx: "OLASILIK",
                render: function (ui) {
                    if (ui.cellData === '1-Discovery') {
                        return {style: {"background": "#FF2611"}};
                    } else if (ui.cellData === '2-Solution Mapping') {
                        return {style: {"background": "#FD7038"}};
                    } else if (ui.cellData === '3-Demo/POC') {
                        return {style: {"background": "#FFFF33"}};
                    } else if (ui.cellData === '4-Negotiation') {
                        return {style: {"background": "#A3D879"}};
                    } else if (ui.cellData === '5-Confirmed/Waiting for End-User PO') {
                        return {style: {"background": "#548E28"}};
                    } else if (ui.cellData === '6-Run Rate') {
                        return {style: {"background": "#AE00F0"}};
                    }
                }
            },
                {title: "<?php echo __('gelis_kanali','komtera'); ?>", editable: false, minWidth: 160, sortable: true, dataIndx: "GELIS_KANALI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
{title: "<?php echo __('etkinlik','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "ETKINLIK", filter: {
                    crules: [{condition: 'contain'}]
                }
            },{title: "<?php echo __('proje_adi','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "PROJE_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },{title: "<?php echo __('firsat_aciklama','komtera'); ?>", editable: false, minWidth: 250, sortable: true, dataIndx: "FIRSAT_ACIKLAMA", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
                {title: "<?php echo __('notlar','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "TNOTLAR", filter: {
                    crules: [{condition: 'contain'}]
                }
                }
        ];
        // URL parametrelerini al
        const urlParams = new URLSearchParams(window.location.search);
        const date1 = urlParams.get('date1') || '';
        const date2 = urlParams.get('date2') || '';
        
        // URL'yi tarih parametreleriyle oluştur
        let dataUrl = "_tablolar/kt_firsatlar.php?dbname=LKS";
        if (date1) dataUrl += "&date1=" + encodeURIComponent(date1);
        if (date2) dataUrl += "&date2=" + encodeURIComponent(date2);

        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: dataUrl,
            getData: function (response) {
                return {data: response.data};
            }
        };
        var obj = {
            menuIcon: false,
            trackModel: {on: true},
            collapsible: {on: false, toggle: false},
            reactive: true,
            scrollModel: {autoFit: true},
            editor: {select: true},
            // sortModel: {
            //     type: 'local',
            //     single: true,
            //     sorter: [{dataIndx: 'id', dir: 'down'}],
            //     space: true,
            //     multiKey: false
            // },
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "<?php echo __('excel_kaydet','komtera'); ?>",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet();
                        }
                    },
                    {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('satir_kaydir','komtera'); ?>',
                        listener: function (evt) {
                            this.option('wrap', evt.target.checked);
                            this.option('autoRow', evt.target.checked);
                            this.refreshDataAndView();
                        }
                    },
                    {
                    type: 'button',
                    icon: 'ui-icon-arrowreturn-1-s',
                    label: 'Undo',                    
                    options: { disabled: true },
                    listener: function () {
                        grid.history({ method: 'undo' });
                    }
                },
                {
                    type: 'button',
                    icon: 'ui-icon-arrowrefresh-1-s',
                    label: 'Redo',
                    options: { disabled: true },
                    listener: function () {
                        grid.history({ method: 'redo' });
                    }
                },
//                {
//                    type: 'button',
//                    label: 'Ayarı Kaydet',
//                    listener: function () {
//                        this.saveState();
//                    }
//                },
//                {
//                    type: 'button',
//                    label: 'Ayarı Yükle',
//                    listener: function () {
//                        //debugger;
//                        this.loadState();
//                    }
//                },
                {
                                type:'button',
                                label: '<?php echo __('filtre_temizle','komtera'); ?>',
                                listener: function(){
                                        this.reset({filter: true});
                                        grid.saveState();
                                }                        
                    },
                              {
                            type:'button',
                            label: '<?php echo __('dizayni_kaydet','komtera'); ?>',
                            listener: function(){
                                    grid.saveState();
                            }
                    },
                        {
                            type:'button',
                            label: '<?php echo __('dizayni_yukle','komtera'); ?>',
                            listener: function(){
                                    grid.loadState({refresh: false});
                            }
                    }
                    
                ]
            },
            history: function (evt, ui) {
                var $tb = this.toolbar(),
                        $undo = $tb.find("button:contains('Undo')"),
                        $redo = $tb.find("button:contains('Redo')");

                if (ui.canUndo != null) {
                    $undo.button("option", {disabled: !ui.canUndo});
                }
                if (ui.canRedo != null) {
                    $redo.button("option", "disabled", !ui.canRedo);
                }
                $undo.button("option", {label: 'Undo (' + ui.num_undo + ')'});
                $redo.button("option", {label: 'Redo (' + ui.num_redo + ')'});
            },
            roundCorners: false,
            rowBorders: true,
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: true, 
            selectionModel: { type: '', native: true },
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
                        style: {"background": "#FFEEEE"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
            },
            load: function (evt, ui) {
                var grid = this,
                data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({data: data});
            },
            filterModel: {
                on: true,
                header: true,
                mode: "AND",
//                hideRows: false,
//                type: 'local',
                menuIcon: false
            },
          
            editable: false,
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
            title: '<span style="font-size: 18px;"><b><?php echo __('firsatlar','komtera'); ?></b></span>',
            resizable: true,
            summaryTitle: "",
            groupModel: {
                on: true,
                merge: true,
                dataIndx: ['DURUM'],
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                "{0} ({1})",
                "{0} - {1}"
            ]
            },
            
            //rowHt: 23,
            freezeCols: 2,
        create: function () {
                    this.loadState({refresh: false});
        },
        };
        grid = pq.grid("div#grid_firsatlar", obj);
  
           grid.one("load", function (evt, ui) {
//            grid.getColumn({ dataIndx: "ShipRegion" }).filter.options 
//                = grid.getData({ dataIndx: ["ShipCountry", "ShipRegion"] });

            grid.getColumn({ dataIndx: "DURUM" }).filter.options 
                = grid.getData({ dataIndx: ["DURUM"] });

            //and apply initial filtering.
            grid.filter({
                oper: 'add',
                rules: [
                    { dataIndx: 'DURUM', value: ['Açık'] }
                ]
            });
        });
        grid.toggle();

       $(window).on('unload', function () {
           grid.saveState();
       });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>
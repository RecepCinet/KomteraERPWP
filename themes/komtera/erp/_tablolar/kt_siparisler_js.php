<script>
    function Gizle() {
        alert('tiklama');
        grid.getColumn({title: 'Finans'}).hidden = true;
        grid.refreshCM();
        grid.refresh();
    }

    var grid;
    function OzelKur(siparis, sindiki) {
        var okk = prompt("Özel Kur Giriniz:", sindiki);
        if (okk === null) {
            //
        }
        okk = okk.replace(',', '.');
        $.get("_engines/tekil_getir.php?cmd=ozel_kur&kur=" + okk + "&siparis=" + siparis, function (data) {
            refreshDataAndView();
        });
    }

    function Komut(ne, ne2, sn) {
        var aciklama = "";
        if (ne === -1 && ne2 === 0) {
            aciklama = "Siparişi Pasif Yapmak istiyor musunuz?";
        }
        if (ne === 0 && ne2 === 0) {
            aciklama = "Sipariş Açık Yapmak istiyor musunuz?";
        }
        if (ne === -1 && ne2 === 42) {
            aciklama = "Teklifi Dikkat Listesine almak istiyor musunuz?";
        }
        if (ne === -1 && ne2 === 11) {
            aciklama = "Teklifi Kara Listeye almak istiyor musunuz?";
        }
        if (confirm(aciklama)) {
            $.get("_engines/tekil_getir.php?cmd=finans_islemleri&i=" + ne + "&i2=" + ne2 + "&sn=" + sn, function (data) {
                refreshDataAndView();
            });
        } else {
            //
        }
    }

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
//            {title: "Durum",style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 80, sortable: false,dataIndx: "SIPARIS_DURUM"},
            {title: "<?php echo __('statu','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "SIPARIS_DURUM_YAZI", filter: {
                    crules: [{condition: 'range', value: ['Aktif', 'Pasif', 'Açık']}]
                }
            },
            {title: "<?php echo __('durum','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "SIPARIS_DURUM_ALT_YAZI", filter: {
                    crules: [{condition: 'range'}]
                }
            },
//    {title: "DurumKod", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "SIPARIS_DURUM_ALT", filter: {
//    crules: [{condition: 'range'}]
//    }
//    },
//    {title: "DurumKod", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "SIPARIS_DURUM", filter: {
//    crules: [{condition: 'range'}]
//    }
//    },
            {title: "<?php echo __('teklif_no','komtera'); ?>", exportRender: false, style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 80, sortable: true, dataIndx: "X_TEKLIF_NO", filter: {
                    crules: [{condition: 'contain'}]
                },
                render: function (ui) {
                    if (ui.rowData.id > 0) {
                        return "<a href='#' class='demo_ac'>" + ui.rowData.X_TEKLIF_NO + "</a>";
                    } else {
                        return "";
                    }
                },
                postRender: function (ui) {
                    var grid = this,
                            $cell = grid.getCell(ui);
                    $cell.find(".demo_ac")
                            .bind("click", function (evt) {
                                FileMaker.PerformScriptWithOption("Teklif", "Ac" + "|" + ui.rowData.X_TEKLIF_NO);
                            });
                }
            },
            {title: "<?php echo __('siparis_no','komtera'); ?>", exportRender: false, editable: false, minWidth: 90, sortable: true, dataIndx: "SIPARIS_NO", filter: {
                    crules: [{condition: 'contain'}] //,value: ['Açık']
                },
                render: function (ui) {
                    if (ui.rowData.id > 0) {
                        return "<a href='#' class='demo_ac'>" + ui.rowData.SIPARIS_NO + "</a>";
                    } else {
                        return "";
                    }
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
            {title: "<?php echo __('satis_tipi','komtera'); ?>", sortable: true, minWidth: 120, dataIndx: "SATIP",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('fatura_tarihi','komtera'); ?>",dataType: "date", format: 'dd.mm.yy', filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "FATTARIHI"},
            {title: "<?php echo __('skular','komtera'); ?>", filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "skular"},
            {title: "<?php echo __('serialler','komtera'); ?>", filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 140, sortable: true, dataIndx: "lisanslar"},
            {title: "<?php echo __('vade','komtera'); ?>", align: "center", hidden: false, editable: false, minWidth: 50, sortable: true, dataIndx: "VADE"},
            {title: "", render: function (ui) {
                    if (ui.rowData.id > 0) {
                        if (ui.rowData.SIPARIS_DURUM_ALT === "21") {
                            return "<image src='images/airplane.png' width=14>";
                        }
                        if (ui.rowData.SIPARIS_DURUM_ALT === "24") {
                            return "<span class='ui-icon ui-icon-document' tooltip='Kota Bekleniyor'></span>";
                        }
                        if (ui.rowData.HardwareVarmi>=1) {
                            return "<span class='ui-icon ui-icon-suitcase' tooltip='Kota Bekleniyor'></span>";
                        }
                    } else {
                        return "";
                    }
                }, align: "center", hidden: false, editable: false, minWidth: 50, sortable: true, dataIndx: "SIPARIS_DURUM_ALT"},
            {title: "<?php echo __('finans','komtera'); ?>", hidden: false, editable: false, minWidth: 50, sortable: false, dataIndx: "id",
                render: function (ui) {
                    if (ui.rowData.id > 0) {
                        return "<a href='#' title='Pasif Yap' onclick='Komut(-1,0,\"" + ui.rowData.SIPARIS_NO + "\")'><span class='ui-icon ui-icon-locked'></span></a> " +
                                "<a href='#' title='Açık Yap' onclick='Komut(0,0,\"" + ui.rowData.SIPARIS_NO + "\")'><span class='ui-icon ui-icon-unlocked'></span></a> ";
                    } else {
                        return "";
                    }
                }},
            {title: "<?php echo __('ozel_kur','komtera'); ?>", render: function (ui) {
                    if (ui.rowData.id > 0) {
                        if (ui.rowData.VADE === 'PEŞİN' || ui.rowData.VADE === 'KKART') {
                            if (ui.rowData.OZEL_KUR > 0) {
                                return {text: "<a href='#' title='Özel Kur' onclick='OzelKur(\"" + ui.rowData.SIPARIS_NO + "\",\"" + ui.rowData.OZEL_KUR + "\")'>Kur Gir</a> "};
                            } else {
                                return {style: {"background": "lightgreen"}, text: "<a href='#' title='Özel Kur' onclick='OzelKur(\"" + ui.rowData.SIPARIS_NO + "\",\"" + ui.rowData.OZEL_KUR + "\")'>Kur Gir</a> "};
                            }
                        } else {
                            return {text: "<a href='#' title='Özel Kur' onclick='OzelKur(\"" + ui.rowData.SIPARIS_NO + "\",\"" + ui.rowData.OZEL_KUR + "\")'>Kur Gir</a> "};
                        }
                    } else {
                        return "";
                    }
                }, hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id"
            },

//            {title: "Satır", hidden: false, editable: false, minWidth: 60, sortable: true, dataIndx: "SATIR",filter: { 
//                        crules: [{condition: 'range'}]
//                    }
//            },
            {title: "<?php echo __('ozel_kur','komtera'); ?>",exportRender: true,dataType: "float", format: "#.###,00", hidden: true, editable: false, minWidth: 60, sortable: true, dataIndx: "OZEL_KUR"},
            {title: "<?php echo __('marka','komtera'); ?>", filter: {
                    crules: [{condition: 'range'}]
                }, hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA"},
            {title: "<?php echo __('toplam','komtera'); ?>",exportRender: true,dataType: "float", align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: true, dataIndx: "TOPLAM"},
            {title: "<?php echo __('para_birimi','komtera'); ?>", align: "right", hidden: false, editable: false, minWidth: 40, sortable: true, dataIndx: "PARA_BIRIMI"},
            {title: "<?php echo __('toplam','komtera'); ?>", summary: {type: "sum", edit: true}, align: "right", exportRender: true,dataType: "float",format: "#.###,00", hidden: false, editable: false, minWidth: 80, sortable: true, dataIndx: "DLR_TUTAR"},
            {title: "ID", hidden: true, editable: false, minWidth: 110, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                },
            },
            {title: "<?php echo __('musteri_temsilcisi','komtera'); ?>", render: function (ui) {
                    if (ui.cellData === '<?PHP echo $user['kullanici']; ?>') {
                        return {style: {"background": "yellow"}};
                    }
                }, editable: false, minWidth: 120, sortable: true, dataIndx: "MUSTERI_TEMSILCISI", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('siparis_tarihi','komtera'); ?>", minWidth: 80, dataIndx: "CD", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('siparis_saati','komtera'); ?>", minWidth: 52, dataIndx: "CT", dataType: "date", format: 'dd.mm.yy H:i:s'},
            {title: "<?php echo __('bayi','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('musteri','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "MUSTERI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "K", minWidth: 80, dataIndx: "KARGO_GONDERI_NO"},
            {title: "<?php echo __('kargo_durum','komtera'); ?>", minWidth: 180, dataIndx: "KARGO_DURUM"},
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_siparisler.php?dbname=LKS&date1=<?PHP echo $date1; ?>&date2=<?PHP echo $date2; ?>",
            getData: function (response) {
                return {data: response.data};
            }
        };
        var obj = {
            selectionModel: {type: '', native: true},
            menuIcon: false,
            trackModel: {on: true},
            collapsible: {on: false, toggle: false},
            reactive: true,
            scrollModel: {autoFit: true},
            editor: {select: true},
            sortModel: {
                type: 'local',
                single: true,
                sorter: [{dataIndx: 'id', dir: 'down'}],
                space: true,
                multiKey: false
            },
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
                        options: {disabled: true},
                        listener: function () {
                            grid.history({method: 'undo'});
                        }
                    },
                    {
                        type: 'button',
                        icon: 'ui-icon-arrowrefresh-1-s',
                        label: 'Redo',
                        options: {disabled: true},
                        listener: function () {
                            grid.history({method: 'redo'});
                        }
                    },
//                    {
//                        type: 'button',
//                        label: 'Ayarı Kaydet',
//                        listener: function () {
//                            this.saveState();
//                        }
//                    },
//                    {
//                        type: 'button',
//                        label: 'Ayarı Yükle',
//                        listener: function () {
//                            //debugger;
//                            this.loadState();
//                        }
//                    },
                    {
                        type: 'button',
                        label: '<?php echo __('filtre_temizle','komtera'); ?>',
                        listener: function () {
                            this.reset({filter: true});
                        }
                    },
                              {
                            type:'button',
                            label: 'Dizaynı Kaydet',
                            listener: function(){
                                    grid.saveState();
                            }
                    },
                        {
                            type:'button',
                            label: 'Dizaynı Yukle',
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
            //groupModel: {on: true, dataIndx: ["DURUM"]}, // , dataIndx: ["DURUM"]
            showToolbar: true,
            showTop: true,
//            groupModel: {
//                on: true,
//                showSummary: [true],
//                grandSummary: true,
//                collapsed: [true],
//                title: '{0}',
//            },
            groupModel: {
                on: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                merge: true,
                dataIndx: ["SIPARIS_DURUM_YAZI"]},
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
                hideRows: false,
                type: 'local',
                menuIcon: false
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
            title: 'Siparişler',
            resizable: false,
            summaryTitle: "",
            freezeCols: 4,
            rowHt: 23,
            create: function () {
                this.loadState({refresh: false});
                for (let t of colM) {
                    if (t.title==="Finans" || t.title==="Özel Kur") {
                        t.hidden = false;
                    }
                }
            },
        };
        grid = pq.grid("div#grid_siparisler", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        });

        
    });
</script>

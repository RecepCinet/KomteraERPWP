<script>
    function TeklifAc(teklif) {
        FileMaker.PerformScriptWithOption("Teklif", "Ac" + "|" + teklif);
    }

    function Odendimi(id, sindiki) {
    	FileMaker.PerformScriptWithOption("Raporlar", "RA-110-window" + "|" + id);
//         var okk = prompt("Ödeme notu giriniz:", sindiki);
//         if (okk === null) {
//             $.get("_engines/tekil_getir.php?cmd=komisyon_odeme&tem=1&aciklama=&id=" + id, function (data) {
//             refreshDV();
//         });
//         }
//         $.get("_engines/tekil_getir.php?cmd=komisyon_odeme&tem=0&aciklama=" + okk + "&id=" + id, function (data) {
//             refreshDV();
//         });
    }

    var grid;

    $(function () {

        var colM = [
            {title: "ID", hidden: true, editable: false, minWidth: 180, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Marka", hidden: false, editable: false, minWidth: 120, sortable: true, dataIndx: "MARKA", filter: {
                    crules: [{condition: 'range'}]
                }
            },
             {title: "Tarih",exportRender: true,align: "center", dataType: "date", format: 'dd.mm.yy', hidden: false, editable: false, minWidth: 90, sortable: true, dataIndx: "FATTAR", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Teklif No",render: function (ui) {
            if (ui.rowData.id>0) {
                return "<a href='#' class='demo_ac' onclick='TeklifAc(\"" + ui.rowData.TEKLIF_NO + "\")'>"+ui.rowData.TEKLIF_NO+"</a>";
            }
        },exportRender: false, hidden: false, editable: false, minWidth: 80, sortable: true, dataIndx: "TEKLIF_NO", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Fatura NO", align: "left", right: false, minWidth: 110, sortable: true, dataIndx: "FATNO", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Ödeme",exportRender: true, align: "center", editable: false, minWidth: 70, sortable: true,
                render: function (ui) {
                    if (ui.rowData.KOMISYON_ODENDI!='1') {
                        return "<a href='#' class='delete_btn' onclick='Odendimi(\"" + ui.rowData.TEKLIF_NO + "\",\"" + ui.rowData.KOMISYON_ODENDI_ACIKLAMA + "\")'>Ödeme</a>";
                    } else {
                        return '';
                    }
                }
            },
            {title: "Odendi",exportRender: false,render: function (ui) {
                    if (ui.rowData.KOMISYON_ODENDI==='1') {
                        return "<span class='ui-icon ui-icon-check'></span>";
                    }
                },exportRender: false, style: {'text-color': '#dd0000'}, dataIndx: "KOMISYON_ODENDI", align: "center", editable: false, minWidth: 85, sortable: false,filter: {
                    crules: [{condition: 'range'}]
                }},
                 {title: "Odeme Notu", hidden: false, editable: false, minWidth: 180, sortable: true, dataIndx: "KOMISYON_ODENDI_ACIKLAMA", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Bayi", hidden: false, editable: false, minWidth: 290, sortable: true, dataIndx: "BAYI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "Musteri", hidden: false, editable: false, minWidth: 290, sortable: true, dataIndx: "MUSTERI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "PB", hidden: false, editable: false, minWidth: 60, sortable: true, dataIndx: "PARA_BIRIMI", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            // {title: "Sip.Tutari",dataType: "float", exportRender: true,format: "#.###,00", align: "right", editable: false, minWidth: 108, sortable: true, dataIndx: "TOPTUT", filter: {
            //         crules: [{condition: 'contain'}]
            //     },summary: {type: "sum", edit: true}
            // },
            // {title: "Ayirma",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "KOMISYON_F1", filter: {
            //         crules: [{condition: 'contain'}]
            //     },summary: {type: "sum", edit: true}
            // },
            // {title: "Partner Kurulum",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "KOMISYON_F2", filter: {
            //         crules: [{condition: 'contain'}]
            //     },summary: {type: "sum", edit: true}
            // },
            // {title: "Harcama",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "KOMISYON_H", filter: {
            //         crules: [{condition: 'contain'}]
            //     },summary: {type: "sum", edit: true}
            // },
            // {title: "Hizmet Urunleri",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "Komtera_Hizmet_Toplami", filter: {
            //         crules: [{condition: 'contain'}]
            //     },summary: {type: "sum", edit: true}
            // },

            {title: "Sip.Tutari",dataType: "float", exportRender: true,format: "#.###,00", align: "right", editable: false, minWidth: 108, sortable: true, dataIndx: "orj_TOPTUT", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "Fon Harcama",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "orj_KOMISYON_H", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "Fon Ayirma",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "orj_KOMISYON_F1", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "Partner Kurulum",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "orj_KOMISYON_F2", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "Fon 3",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "orj_KOMISYON_F3", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "Komtera",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "orj_KOMTERA_HIZMET_BEDELI", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "FisF Harcama",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "FisFonHarcama", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            {title: "FisF Gelir ",dataType: "float", exportRender: true,format: "#.###,00", align: "right", right: false, minWidth: 88, sortable: true, dataIndx: "FisFonGeliri", filter: {
                    crules: [{condition: 'contain'}]
                },summary: {type: "sum", edit: true}
            },
            
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_rapor_komisyon_raporu.php?dbname=LKS",
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
            sortModel: {
                type: 'local',
                single: true,
                sorter: [{dataIndx: 'FATTAR', dir: 'down'}],
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
                            ExcelKaydet();
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
                if ( (parseFloat(ui.rowData.FisFonGeliri) == parseFloat(ui.rowData.orj_KOMISYON_F1)) &&
                     (parseFloat(ui.rowData.FisFonHarcama) == parseFloat(ui.rowData.orj_KOMISYON_H))
                ) {
                    return {
                        style: {"background": "#EEFFEE"} //can also return attr (for attributes) and cls (for css classes) properties.
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
            // freezeCols: 2,
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
                rPPOptions: [1000]
            },

            sortable: true,
            rowHt: 17,
            summaryTitle: "",
            title: '<b>Komisyon Raporu</b>',
            wrap: false, hwrap: false,
            numberCell: {show: false, resizable: true, width: 30, title: "#"},
            resizable: true,
       create: function () {
            //this.loadState({refresh: false});
       },
        };
        grid = pq.grid("div#grid_rapor_komisyon_raporu", obj);
        grid.toggle();
        $(window).on('unload', function () {
            //grid.saveState();
        });
        grid.on("destroy", function () {
            //this.saveState();
        })
    });
</script>

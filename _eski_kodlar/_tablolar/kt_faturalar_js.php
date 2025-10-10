<script>var grid;

    function SerialGoster(sku) {
        FileMaker.PerformScriptWithOption("Stoklar", "SerialGoster" + "|" + sku);
    }

    function FaturaBas(id) {
        if (id!="") {
            if (confirm("Logo idleri: " + id + "\n\n Faturalanacak?") == true) {
                $.get("_service/z_fatura_emri.php?idler=" + id, function (data) {
                    refreshDV();
                });
            } else {
                //
            }
        } else {
            alert("Fatura için LogId'ler seçilmedi!");
        }
    }

    function FaturaBasEskiTarih(id) {
        simdikiTarih = new Date();
        gun = ("0" + simdikiTarih.getDate()).slice(-2);
        ay = ("0" + (simdikiTarih.getMonth() + 1)).slice(-2); // JavaScript'te aylar 0-11 arasında sayılır.
        yil = simdikiTarih.getFullYear();

        tarih = yil + "-" + ay + "-" + gun;

        ad=prompt('Faturanin Basilacagi Tarih',tarih);

        if (ad == null) {
            console.log("İptal edildi.");
        } else {

            if (id!="") {
                if (confirm("Logo idleri: " + id + "\n\n Faturalanacak?") == true) {
                    $.get("_service/z_fatura_emri.php?idler=" + id + "&ft=" + ad, function (data) {
                        //refreshDV();
                    });
                } else {
                    //
                }
            } else {
                alert("Fatura için LogId'ler seçilmedi!");
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
            alert('Tekrar Denenecek' + id);
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
        {title: "Teklif", align: "center", editable: false, minWidth: 80, sortable: false, dataIndx: "_teklif_no",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Sipariş No", align: "center",  editable: false, minWidth: 90, sortable: false, dataIndx: "siparisNo",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Tekrar",render: function (ui) {
                if (ui.rowData.r_result=="error" || ui.rowData.r_result=="to") {
                    return "<a href='#' class='demo_ac' onclick='TekrarDene(\"" + ui.rowData.id + "\")'><b>Tekrar Dene</b></a>";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}, align: "center", editable: false, minWidth: 85, sortable: false, dataIndx: "projeKodu",filter: false
        },
        {title: "Proje", editable: false, minWidth: 72, sortable: false, dataIndx: "projeKodu",filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "Bayi", editable: false, minWidth: 300, sortable: false, dataIndx: "bbayi",
        filter: { 
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "Faturamı", align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "_faturami",
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
        {title: "Irs",align: "center", editable: false, minWidth: 60, sortable: false, dataIndx: "_status_i",filter: {
                        crules: [{condition: 'range'}]
                    },render: function (ui) {
                if (ui.rowData._status_i=="1") {
                    return "<span class='ui-icon ui-icon-check'></span></a> ";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}
            },
        {title: "Fat",align: "center", editable: false, minWidth: 60, sortable: false, dataIndx: "_status_f",filter: {
                        crules: [{condition: 'range'}]
                    },render: function (ui) {
                if (ui.rowData._status_f=="1") {
                    return "<span class='ui-icon ui-icon-check'></span></a> ";
                } else {
                    return "<a href='#' class='demo_ac'></a>";
                }}
            },
        {title: "Fiş No",align: "center", render: function (ui) {
                if (ui.rowData.r_LogoId<1) {
                    return "<a href='#' class='demo_ac' onclick='NoDuzelt(\"" + ui.rowData.siparisNo + "\",\"" + ui.rowData._faturami + "\")'>Düzelt</a>";
                } else {
                    return true;
                }}, editable: false, minWidth: 135, sortable: false, dataIndx: "r_FisNo",filter: {
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "Logo Id",align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "r_LogoId",filter: {
                crules: [{condition: 'begin'}]
            }
        },
        {title: "Result", editable: false, minWidth: 75, sortable: false, dataIndx: "r_result",filter: {
                crules: [{condition: 'begin'}]
            }
        },
        {title: "Response", editable: false, minWidth: 270, sortable: false, dataIndx: "r_response",filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Irsaliye Tarihi", editable: false, minWidth: 150, sortable: false, dataIndx: "irsaliyeTarihi"},
        {title: "Fatura Tarihi", editable: false, minWidth: 150, sortable: false, dataIndx: "faturaTarihi"},
        {title: "DT", align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "dovizTuru",filter: false},
        {title: "Döviz Kuru",align: "center", editable: false, minWidth: 85, sortable: false, dataIndx: "dovizKuru",filter: false},
        {title: "Ambar",align: "center", editable: false, minWidth: 70, sortable: false, dataIndx: "ambarKodu",filter: {
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
                        label: 'Faturala',
                        listener: function () {
                            var checked = this.Checkbox('state').getCheckedNodes().map(function(rd){
                                return rd.r_LogoId;
                            })
                            FaturaBas(checked);
                        }
                    },
                    {
                        type: 'button',
                        label: 'Eski Tarihe Faturala',
                        listener: function () {
                            var checked = this.Checkbox('state').getCheckedNodes().map(function(rd){
                                return rd.r_LogoId;
                            })
                            FaturaBasEskiTarih(checked);
                        }
                    },
                    {
                       type: 'button',
                       label: "Export",
                       icon: 'ui-icon-arrowthickstop-1-s',
                       listener: function () {
                           ExcelKaydet();
                       }
                   },
                    {
                        type: 'checkbox',
                        value: false,
                        label: 'Satır Kaydır',
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
                    {
                        type:'button',
                        label: 'Filtre Temizle',
                        listener: function(){
                            this.reset({filter: true});
                            grid.saveState();
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
                    },
                    {
                        type: 'button',
                        label: "Hatalıları Sil",
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
        title: 'LOGO - Faturalar',
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
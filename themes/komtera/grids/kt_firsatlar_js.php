<script>
    function refreshDV() {
        grid.refreshDataAndView();
        //alert("yes");
    }

    function ExcelKaydet() {
        blob = grid.exportData({
            url: "export.php",
            format: 'xls',
            nopqdata: true, //applicable for JSON export.
            render: false
        });
        if (typeof blob === "string") {
            blob = new Blob([blob]);
        }
        saveAs(blob, new Date().toISOString() + ".xls");
    }
</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<html>
<head>
    <link rel="stylesheet" href="pqgrid.min.css" />
    <link rel="stylesheet" href="pqgrid.ui.min.css" />
    <link rel='stylesheet' href='themes/white/pqgrid.css' />
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="pqgrid.min.js"></script>
    <script src="localize/pq-localize-tr.js"></script>
    <script src="pqTouch/pqtouch.min.js"></script>
    <script src="jsZip-2.5.0/jszip.min.js"></script>
    <script src="js/base64.min.js"></script>
    <script src="js/FileSaver.js"></script>
    <?PHP
    include "_tablolar/" . $tablo . "_js.php";
    ?>
</head>
<body>

<?PHP
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$string='select kt_yetki_firsatlar from TF_USERS where kullanici=\'' . $cryp . '\'';
//echo $string;
$stmt = $conn2->prepare($string);
$stmt->execute();
$izin = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['kt_yetki_firsatlar'];
//echo "-------------------------------------------";
//echo $izin;
//echo "<br /><br />";
//echo YetkiVarmi($izin,'FI-104');
//echo "-------------------------------------------";

?>
<script>
    var grid;
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
            {title: "<?php echo __('Durum','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "DURUM", filter: {
                    crules: [{condition: 'range'}]
                },render: function (ui) {
                    if (ui.cellData === '<?php echo __('Açık','komtera'); ?>') {
                        return {style: {"background": "#ebebeb"}};
                    } else if (ui.cellData === '<?php echo __('Kazanıldı','komtera'); ?>') {
                        return {style: {"background": "#b2f4ac"}};
                    } else if (ui.cellData === '<?php echo __('Kaybedildi','komtera'); ?>') {
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
            {title: "<?php echo __('Fırsat','komtera'); ?>",render: function (ui) {
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
                    
                    
            {title: "<?php echo __('Teklifler','komtera'); ?>",exportRender: false, style: {'text-color': '#dd0000'}, dataIndx: "Teklifler", align: "left", editable: false, minWidth: 90, sortable: false,
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
            },{title: "<?php echo __('SKUlar','komtera'); ?>",filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "skular"},
            {title: "<?php echo __('Cozumler','komtera'); ?>",filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "Cozumler"},

            {title: "<?php echo __('Satis Tipi','komtera'); ?>", sortable: true, minWidth: 120, dataIndx: "SATIP",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('Tarih','komtera'); ?>", sortable: true, minWidth: 80, dataIndx: "BASLANGIC_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('Son Değişiklik','komtera'); ?>", minWidth: 80, dataIndx: "REVIZE_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('Bitis Tarihi','komtera'); ?>", minWidth: 80, dataIndx: "BITIS_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('BitisAY','komtera'); ?>", hidden: false, editable: false, minWidth: 70, sortable: true, dataIndx: "BITIS_AY", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('Marka','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA", filter: {
                    crules: [{condition: 'range'}]
                }
            },
			{title: "<?php echo __('Mar.Man.','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "MARKA_MANAGER", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "ID", hidden: true, editable: false, minWidth: 110, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('Kayıdı Açan','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === '<?PHP echo $user['kullanici']; ?>') {
                        return {style: {"background": "yellow"}};
                    }
                },
                editable: false, minWidth: 120, sortable: true, dataIndx: "KAYIDI_ACAN", filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "<?php echo __('Müşteri Temsilcisi','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === '<?PHP echo $user['kullanici']; ?>') {
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
            {title: "Tutar", exportRender: true,dataType: "float", render: function (ui) {
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
            {title: "<?php echo __('Bayi','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
            ,
						{title: "<?php echo __('Bayi Yetkili','komtera'); ?>", hidden: false, editable: false, minWidth: 110, sortable: true, dataIndx: "BAYI_YETKILI_ISIM", filter: {
                    crules: [{condition: 'range'}]
                }
            },
            //     {title: "Bayi Yetkili", editable: false, minWidth: 120, sortable: true, dataIndx: "BAYI_YETKILI_ISIM", filter: {
            //         crules: [{condition: 'contain'}]
            //     }
            // },
            {title: "<?php echo __('Müşteri','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "MUSTERI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('Olasılık','komtera'); ?>",filter: {
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
                {title: "<?php echo __('Gelis Kanali','komtera'); ?>", editable: false, minWidth: 160, sortable: true, dataIndx: "GELIS_KANALI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
{title: "<?php echo __('Etkinlik','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "ETKINLIK", filter: {
                    crules: [{condition: 'contain'}]
                }
            },{title: "<?php echo __('Proje Adı','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "PROJE_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },{title: "<?php echo __('Fırsat Açıklama','komtera'); ?>", editable: false, minWidth: 250, sortable: true, dataIndx: "FIRSAT_ACIKLAMA", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
                {title: "<?php echo __('Notlar','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "TNOTLAR", filter: {
                    crules: [{condition: 'contain'}]
                }
                }
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_firsatlar.php?dbname=LKS&date1=<?PHP echo $date1; ?>&date2=<?PHP echo $date2; ?>",
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
                    <?PHP if (YetkiVarmi($izin,'FI-104')==1) { ?>
                    {
                        type: 'button',
                        label: "<?php echo __('Excel\'e Aktar','komtera'); ?>",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet();
                        }
                    },
                    <?PHP } ?>
                    {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('Satır Kaydır','komtera'); ?>',
                        listener: function (evt) {
                            this.option('wrap', evt.target.checked);
                            this.option('autoRow', evt.target.checked);
                            this.refreshDataAndView();
                        }
                    },
                    {
                    type: 'button',
                    icon: 'ui-icon-arrowreturn-1-s',
                    label: '<?php echo __('Geri Al','komtera'); ?>',                    
                    options: { disabled: true },
                    listener: function () {
                        grid.history({ method: 'undo' });
                    }
                },
                {
                    type: 'button',
                    icon: 'ui-icon-arrowrefresh-1-s',
                    label: '<?php echo __('Yinele','komtera'); ?>',
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
                                label: '<?php echo __('Filtre Temizle','komtera'); ?>',
                                listener: function(){
                                        this.reset({filter: true});
                                        grid.saveState();
                                }                        
                    },
                              {
                            type:'button',
                            label: '<?php echo __('Görünümü Kaydet','komtera'); ?>',
                            listener: function(){
                                    grid.saveState();
                            }
                    },
                        {
                            type:'button',
                            label: '<?php echo __('Görünümü Yükle','komtera'); ?>',
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
                $undo.button("option", {label: '<?php echo __('Geri Al','komtera'); ?>' + ' (' + ui.num_undo + ')'});
                $redo.button("option", {label: '<?php echo __('Yinele','komtera'); ?>' + ' (' + ui.num_redo + ')'});
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
            title: '<?php echo __('Fırsatlar','komtera'); ?>',
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
                    { dataIndx: 'DURUM', value: ['<?php echo __('Açık','komtera'); ?>'] }
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



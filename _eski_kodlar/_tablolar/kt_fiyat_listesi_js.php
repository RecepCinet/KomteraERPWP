<?PHP

$marka= $_GET['marka'];

?>
<script>$(function () {

//        function filterhandler() {
//            var $toolbar = this.toolbar();
//            deger = $toolbar.find(".marka_ne").val();
//            
//            this.filter({
//                oper: 'replace',
//                rules: [{ dataIndx: "marka", condition: "contain", value: deger}]
//            });
//            this.option( "pageModel", {  format: "#,###",
//                type: "remote",
//                rPP: 100000,
//                strRpp: "{0}",
//                rPPOptions: [1000, 10000, 100000] } );
//        }
        
//        function filterRender(ui) {
//            var val = ui.cellData,
//                filter = ui.column.filter,
//                crules = (filter || {}).crules;
//            if (filter && filter.on && crules && crules[0].value) {
//                var condition = crules[0].condition,
//                    valUpper = val.toUpperCase(),
//                    txt = crules[0].value,
//                    txt = (txt == null) ? "" : txt.toString(),
//                    txtUpper = txt.toUpperCase(),
//                    indx = -1;
//                if (condition == "end") {
//                    indx = valUpper.lastIndexOf(txtUpper);
//                    //if not at the end
//                    if (indx + txtUpper.length != valUpper.length) {
//                        indx = -1;
//                    }
//                }
//                else if (condition == "contain") {
//                    indx = valUpper.indexOf(txtUpper);
//                }
//                else if (condition == "begin") {
//                    indx = valUpper.indexOf(txtUpper);
//                    //if not at the beginning.
//                    if (indx > 0) {
//                        indx = -1;
//                    }
//                }
//                if (indx >= 0) {
//                    var txt1 = val.substring(0, indx);
//                    var txt2 = val.substring(indx, indx + txt.length);
//                    var txt3 = val.substring(indx + txt.length);
//                    return txt1 + "<span style='background:yellow;color:#333;'>" + txt2 + "</span>" + txt3;
//                }
//                else {
//                    return val;
//                }
//            }
//            else {
//                return val;
//            }
//        }

        var colM = [
            {title: "SKU", editable: true, width: 140, dataIndx: "sku",
                filter: {
                    crules: [{condition: 'begin'}],
                    groupIndx: "SKU"
                }
            },
            {title: "Açıklama", width: 490, dataIndx: "urunAciklama",
                filter: {
                    crules: [{condition: 'contain'}],
                    groupIndx: "urunAciklama"
                }
            },
            {title: "Marka", width: 130, dataIndx: "marka",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Tür", width: 130, dataIndx: "tur",
                render: function (ui) {
                    if (ui.cellData == 'Hardware') {
                        return {style: {"background": "red"}};
                    }
                },
                filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "Çözüm", width: 130, dataIndx: "cozum",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Süre", align: 'center', width: 90, dataIndx: "lisansSuresi",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Fiyat",format: "#.###,00", align: 'right', width: 90, dataIndx: "listeFiyati"},
            {title: "UpLift",format: "#.###,00", align: 'right', width: 90, dataIndx: "listeFiyatiUpLift"},
            {title: "PB", align: 'center', width: 70, dataIndx: "paraBirimi",
                filter: {
                    crules: [{condition: 'range'}]
                }
            }<?PHP
    if ($marka=="WATCHGUARD" || $marka=="SECHARD") {
            ?>,
            {title: "wCtgr", align: 'center', width: 90, dataIndx: "wgCategory",
                filter: {
                    crules: [{condition: 'contain'}]
                }
            },
			<?PHP
    if ($marka!="SECHARD") {
            ?>
            {title: "wUpcCode", align: 'center', width: 130, dataIndx: "wgUpcCode",
                filter: {
                    crules: [{condition: 'contain'}]
                }
	}<?PHP }}?>
        ];
        var dataModel = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            url: "_tablolar/kt_fiyat_listesi.php?dbname=LKS&marka=<?PHP echo $marka; ?>",
            getData: function (dataJSON) {
                var data = dataJSON.data;
                return {curPage: dataJSON.curPage, totalRecords: dataJSON.totalRecords, data: data};
            }
        };

        var obj = {
            trackModel: {on: true},
//            toolbar: {
//                items: [
////                    { 
////                        type: 'select',                         
////                        cls: "marka_ne",
////                        listener: filterhandler,
////                        options: [
////                            //<?PHP
//                            $out="";
//                            for ($t=0;$t<count($marka_hepsi);$t++) {
//                                if ($t>0) {
//                                    $out .= ",";
//                                }
//                                $out .= '{ "' . $marka_hepsi[$t]['marka'] . '": "' . $marka_hepsi[$t]['marka'] . '" }';
//                            }
//                            echo $out;                            
//                            ?>////
////                        ]
////                    },
//                    {
//                       type: 'button',
//                       label: "Export",
//                       icon: 'ui-icon-arrowthickstop-1-s',
//                       listener: function () {
//                           this.exportData({
//                               url: "export.php",
//                               format: "xlsx",
//                               nopqdata: true, //applicable for JSON export.
//                               render: true
//                           });
//                       }
//                   }
//                ]
//            },
            menuIcon: true,
            collapsible: {on: false, toggle: false},
            reactive: true,
            sortModel: {
                type: 'local',
                single: true,
                sorter: [{dataIndx: 'id', dir: 'up'}],
                space: true,
                multiKey: false
            },
            roundCorners: false,
            rowBorders: true,
            selectionModel: {type: 'cell'},
            stripeRows: false,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: true,
            //groupModel: {on: true},
            showToolbar: true,
            showTop: true,
            stripeRows: true,
            width: 1200, height: 400,
            dataModel: dataModel,
            colModel: colM,
            // ROW Komple:
            rowInit: function (ui) {
                if (ui.rowData.tur == 'Hardware') {
                    return {
                        style: {"background": "yellow"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
            },
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
            pageModel: {
                format: "#,###",
                type: "remote",
                rPP: 1000,
                strRpp: "{0}",
                rPPOptions: [1000]
            },
            sortable: true,
            wrap: false, hwrap: false,
            numberCell: {resizable: true, width: 30, title: "#"},
            title: '<?PHP echo $marka ; ?> - Fiyat Listesi',
            rowHt: 23,
            resizable: true,
//            create: function () {
//                this.loadState({refresh: false});
//            },
        };
        var grid = pq.grid("div#grid_paging", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>
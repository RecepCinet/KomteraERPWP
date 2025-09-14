<?PHP
$marka = $_GET['marka'];
$teklif_no=$_GET['teklif_no'];
$bayi_seviye=$_GET['bayi_seviye'];
$seclis=$_GET['seclis'];

if ($seclis=="") {
    $seclis=0;
}

session_start();
?>
<script>
    var grid;

    function acac(sku,kac) {
        var orjkac=kac;
        var seclis=<?PHP echo $seclis; ?>;
        var mmarka="<?PHP echo $marka; ?>";
        if (seclis=="") {
            seclis=0;
        }
        if (seclis>0) {
            kac=seclis;
        }
        if (kac=="") {
            kac=1;
        }
        //FileMaker.PerformScriptWithOption("COMMON_WINDOW", "Urun_Sec_Back" + "\n" + gelen);
        var tn="<?PHP echo $teklif_no; ?>";
        var bs="<?PHP echo $bayi_seviye; ?>";
        var user="<?PHP echo $_SESSION['user']['kullanici']; ?>";
        var adet = kac;
        if (mmarka!="SECHARD") {
            kac=1;
            adet = prompt("<?php echo __('how_many_pieces','komtera'); ?> " + tn, kac);
            adet=Number.parseInt(adet);
        if (isNaN(adet)) {
            adet=0;
        }
        } else {
            adet=seclis;
        }
        if (adet<=0 || adet==null || adet=="NaN") {
            alert ('<?php echo __('quantity_cannot_be_zero','komtera'); ?>!');
            acac(sku,kac);
            return false;
        }
         $.ajax({
            url: '_tinywin/urun_ekle.php?teklif_no='+tn+'&sku=' + sku + "&adet=" + adet + "&bayi_seviye_kod=" + bs + "&user=" + user,
            data: {
//              list: JSON.stringify(gridChanges)
            },
            dataType: "json",
            type: "POST",
            async: true,
            beforeSend: function (jqXHR, settings) {
//
            },
            success: function (changes) {
                
            },
            complete: function (gelen) {
//                
                FileMaker.PerformScriptWithOption("Teklif", "urun_ekle");
            }
        });
    }

    $(function () {
        var colM = [
            {title: "", style: {'text-color': '#dd0000'}, align: "center", editable: false, minWidth: 40, sortable: false,
                render: function (ui) {
                    return "<a href='#' class='demo_ac' onclick='acac(\"" + ui.rowData.sku + "\",\"" + ui.rowData.wgCategory + "\")'><?php echo __('add','komtera'); ?></a>";
                }
            },
            {title: "ID", hidden: true, editable: false, minWidth: 60, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('stock_status','komtera'); ?>", width: 80, dataIndx: "STOK_DURUM",
                filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "<?php echo __('stock','komtera'); ?>", width: 80, dataIndx: "STOK_ADET",
                filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "<?php echo __('type','komtera'); ?>", width: 80, dataIndx: "tur",
                filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {title: "<?php echo __('solution','komtera'); ?>", width: 80, dataIndx: "cozum",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "<?php echo __('duration','komtera'); ?>", align: 'center', width: 60, dataIndx: "lisansSuresi",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },

            {title: "SKU", editable: true, width: 130, dataIndx: "sku",
                filter: {
                    crules: [{condition: 'begin'}],
                    groupIndx: "SKU"
                }
            },
            {title: "<?php echo __('description','komtera'); ?>", width: 390, dataIndx: "urunAciklama",
                filter: {
                    crules: [{condition: 'contain'}],
                    groupIndx: "urunAciklama"
                }
            },
            {title: "<?php echo __('price','komtera'); ?>", format: "#.###,00", align: 'right', width: 90, dataIndx: "listeFiyati"},
            {title: "Brm", align: 'left', width: 30, dataIndx: "paraBirimi"}
			,
            {title: "Users", align: 'center', width: 55, dataIndx: "wgCategory",
                filter: {
                    crules: [{condition: 'contain'}]
                }
            }
                    //{title: "Maliyet",format: "#,###.00", align: 'right', width: 90, dataIndx: "maliyet"},
        ];
        var dataModel = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_fiyat_lis_sec.php?dbname=LKS&marka=<?PHP echo $marka; ?>&seclis=<?PHP echo $seclis; ?>",
            getData: function (response) {
                return {data: response.data};
            }
        };
        var obj = {
            trackModel: {on: true},
            menuIcon: false,
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
            showTitle: false,
            showToolbar: true,
            showTop: true,
            stripeRows: true,
            width: 1200, height: 400,
            dataModel: dataModel,
            colModel: colM,
            freezeCols: false,
                    rowInit: function (ui) {
            if (ui.rowData.STOK_DURUM == '<?php echo __('stock_available','komtera'); ?>') {
                return { 
                    style: { "background": "#ADFF2F" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
            filterModel: {
                on: true,
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: false
            },
            editable: false,
            pageModel: false,
            sortable: true,
            wrap: false, hwrap: false,
            numberCell: false,
            numberCell: {show: false, resizable: true, width: 30, title: "#"},
            rowHt: 17,
            resizable: true,
        };
        var grid = pq.grid("div#grid_fiyat_sec", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>
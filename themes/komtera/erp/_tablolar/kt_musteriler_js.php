<script>

    var grid;

    $(function () {

        var colM = [
            {title: "ID", hidden: true, editable: false, minWidth: 180, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'begin'}]
                }
            },
            {title: "<?php echo __('Müşteri','komtera'); ?>", editable: false, minWidth: 370, sortable: true, dataIndx: "musteri", filter: {
                    crules: [{condition: 'begin'}]
                }
            },
            {title: "<?php echo __('Adres','komtera'); ?>", editable: false, minWidth: 480, sortable: true, dataIndx: "adres", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {title: "<?php echo __('Şehir','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "sehir", filter: {
                    crules: [{condition: 'begin'}]
                }
            },
            {title: "<?php echo __('Posta Kodu','komtera'); ?>", editable: false, minWidth: 100, sortable: true, dataIndx: "posta_kodu", filter: {
                    crules: [{condition: 'begin'}]
                }
            }
            //{title: "", editable: false, minWidth: 80, sortable: false,
            //    render: function (ui) {
            //        return "<a href='#' class='delete_btn'><?php //echo __('Detay','komtera'); ?>//</a>";
            //    },
            //    postRender: function (ui) {
            //        var grid = this,
            //        $cell = grid.getCell(ui);
            //        $cell.find(".delete_btn")
            //            .bind("click", function (evt) {
            //            FileMaker.PerformScriptWithOption ( "Musteri", "Ac" + "\n" + ui.rowData.id );
            //        });
            //    }
            //}
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/kt_musteriler.php?dbname=LKS",
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
                space: true,
                multiKey: false
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
            title: '<span style="font-size: 18px;"><b><?php echo __('Müşteriler','komtera'); ?></b></span>',
            showToolbar: false,
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
            freezeCols: 2,
            filterModel: {
                on: true,
                header: true,
                mode: "AND",
                type: 'local',
                menuIcon: true
            },
            editable: true,
            pageModel: {
                type: "local",
                rPP: 100,
                rPPOptions: [50, 100, 200, 500]
            },

            sortable: true,
            rowHt: 17,
            wrap: false, hwrap: false,
            numberCell: {show: false, resizable: true, width: 30, title: "#"},
            resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
        };
        grid = pq.grid("div#grid_musteriler", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>

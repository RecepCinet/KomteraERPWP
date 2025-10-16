<script>
var grid;

$(function () {
   
    var colM = [
        {title: "Detay", editable: false, minWidth: 80, sortable: false,
            render: function (ui) {
                return "<button type='button' class='bayi_detail_btn' style='height: 23px;'>Detay</button>";
            },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                $cell.find(".bayi_detail_btn")
                    .button({ icons: { primary: 'ui-icon-search'} })
                    .bind("click", function (evt) {
                        openBayiDetailModal(ui.rowData);
                    });
            }
        },
        {title: "<?php echo __('ch_unvani','komtera'); ?>", editable: false, minWidth: 380, sortable: true, dataIndx: "CH_UNVANI",filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('Cari Kodu','komtera'); ?>", editable: false, minWidth: 160, sortable: true, dataIndx: "CH_KODU",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('bayi_durum','komtera'); ?>", editable: false, minWidth: 100, sortable: true, dataIndx: "BAYI",
            filter: { 
                            crules: [{condition: 'range',value: ['Aktif']}]
                        }
            },
        {title: "<?php echo __('vade','komtera'); ?>", editable: false, minWidth: 120, sortable: true, dataIndx: "VADE",filter: { 
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "<?php echo __('adres_1','komtera'); ?>", editable: false, minWidth: 450, sortable: true, dataIndx: "ADRES1",filter: { 
                    crules: [{condition: 'contain'}]
                }
        },
        {title: "<?php echo __('adres_2','komtera'); ?>", editable: false, minWidth: 450, sortable: true, dataIndx: "ADRES2",filter: { 
                    crules: [{condition: 'contain'}]
                }
        },
        {title: "<?php echo __('sehir','komtera'); ?>", align: "center", editable: false, minWidth: 120, sortable: true, dataIndx: "SEHIR"},
        {title: "<?php echo __('vergi_no','komtera'); ?>", align: "center", editable: false, minWidth: 120, sortable: true, dataIndx: "VERGI_NO"},
        {title: "<?php echo __('vergi_dairesi','komtera'); ?>", align: "center", editable: false, minWidth: 160, sortable: true, dataIndx: "VERGI_DAIRESI"},
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_bayiler.php?dbname=LKS",
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
        groupModel: {on: true}, // , dataIndx: ["BAYI"]
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
        pageModel: {
            format: "#,###",
            type: "local",
            rPP: 100,
            strRpp: "{0}",
            rPPOptions: [100, 1000, 10000]
        },

        sortable: true,
        rowHt: 23,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: 'LOGO - Bayi Listesi',
        resizable: true,
//        create: function () {
//                        this.loadState({refresh: false});
//        },
    };
    grid = pq.grid("div#grid_bayiler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })

});

// Bayi Detay Modal Fonksiyonları
function openBayiDetailModal(rowData) {
    var html = '';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Cari Ünvanı:</div>';
    html += '<div class="bayi-info-value">' + (rowData.CH_UNVANI || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Cari Kodu:</div>';
    html += '<div class="bayi-info-value">' + (rowData.CH_KODU || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Adres 1:</div>';
    html += '<div class="bayi-info-value">' + (rowData.ADRES1 || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Adres 2:</div>';
    html += '<div class="bayi-info-value">' + (rowData.ADRES2 || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Şehir:</div>';
    html += '<div class="bayi-info-value">' + (rowData.SEHIR || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Vade:</div>';
    html += '<div class="bayi-info-value">' + (rowData.VADE || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Bayi Durumu:</div>';
    html += '<div class="bayi-info-value">' + (rowData.BAYI || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Vergi No:</div>';
    html += '<div class="bayi-info-value">' + (rowData.VERGI_NO || '-') + '</div>';
    html += '</div>';

    html += '<div class="bayi-info-row">';
    html += '<div class="bayi-info-label">Vergi Dairesi:</div>';
    html += '<div class="bayi-info-value">' + (rowData.VERGI_DAIRESI || '-') + '</div>';
    html += '</div>';

    $('#bayi-detail-content').html(html);
    $('#bayi-detail-modal').addClass('active');
}

function closeBayiDetailModal() {
    $('#bayi-detail-modal').removeClass('active');
}

// Modal dışına tıklanınca kapat
$(document).on('click', '.bayi-modal-overlay', function(e) {
    if ($(e.target).hasClass('bayi-modal-overlay')) {
        closeBayiDetailModal();
    }
});
</script>

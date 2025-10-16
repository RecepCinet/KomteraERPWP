<script>
var grid;

$(function () {
    function LigGetir (ne) {
        var out="";
        if (ne==="1") {
            out="Registered";
        } else if (ne==="2") {
            out='Silver';
        } else if (ne==="3") {
            out="Gold";
        } else if (ne==="4") {
            out="Platinum";
        }
        return out;
    }

    var colM = [
        {title: "",export: false, editable: false, minWidth: 30, sortable: false, dataIndx: "FIRSAT_NO",filter: false,
                render: function (ui) {
                    return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-zoomin'></span></a>";
            },
            postRender: function (ui) {
            var grid = this,
                $cell = grid.getCell(ui);
                $cell.find(".demo_ac")
                .bind("click", function (evt) {
                    evt.preventDefault();
                    openBayiModal(ui.rowData.CH_KODU);
                });
            }
        },
        {title: "<?php echo __('ch_unvani','komtera'); ?>", editable: false, minWidth: 410, sortable: true, dataIndx: "CH_UNVANI",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "CH_KODU",align: "center", editable: false, minWidth: 100, sortable: true, dataIndx: "CH_KODU",filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('vade','komtera'); ?>", editable: false, minWidth: 70, sortable: true, dataIndx: "VADE",filter: {
            crules: [{condition: 'range'}]
        }
        },
        {title: "Dikk Lis",exportRender: false, render: function (ui) {
                if (ui.cellData === '1') {
                        return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-check'></span></a>";
                    }
                },
                        align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "dikkat_listesi",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "Kara Lis",exportRender: false, render: function (ui) {
                if (ui.cellData === '1') {
                        return "<a href='#' class='demo_ac'><span class='ui-icon ui-icon-check'></span></a>";
                    }
                },align: "center", editable: false, minWidth: 70, sortable: true, dataIndx: "kara_liste",filter: {
                        crules: [{condition: 'range'}]
                    }
            },
        {title: "SOPHOS",align: "center", editable: false, minWidth: 105, sortable: true, dataIndx: "SOPHOS",
            render: function (ui) {
                return LigGetir(ui.rowData.SOPHOS);
            }
        },
        {title: "WATCHGUARD",align: "center", editable: false, minWidth: 105, sortable: true, dataIndx: "WATCHGUARD",
            render: function (ui) {
                return LigGetir(ui.rowData.WATCHGUARD);
            }
        }
    ];

    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt__bayiler.php?dbname=LKS",
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
                },
                {
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
        stripeRows: true,
        scrollModel: {autoFit: false},
        showHeader: true,
        showTitle: true,
        title: '<?php echo __('Bayiler','komtera'); ?> - LOGO',
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
        resizable: true
    };

    grid = pq.grid("div#grid__bayiler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })

});

// Modal fonksiyonları
function openBayiModal(ch_kodu) {
    $('#bayi-detail-content').html('<p style="text-align: center; color: #999;">Yükleniyor...</p>');
    $('#bayi-detail-modal').addClass('active');

    $.ajax({
        url: '_tablolar/kt__bayiler_detay.php',
        method: 'GET',
        data: { ch_kodu: ch_kodu },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderBayiModal(response);
            } else {
                $('#bayi-detail-content').html('<p style="color: red;">Hata: ' + response.error + '</p>');
            }
        },
        error: function() {
            $('#bayi-detail-content').html('<p style="color: red;">Veri yüklenirken hata oluştu</p>');
        }
    });
}

function renderBayiModal(data) {
    var bayi = data.bayi;
    var html = '';

    // Temel bilgiler
    html += '<div class="bayi-info-section">';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Cari Ünvanı:</div><div class="bayi-info-value">' + (bayi.CH_UNVANI || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Cari Kodu:</div><div class="bayi-info-value">' + (bayi.CH_KODU || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Adres1:</div><div class="bayi-info-value">' + (bayi.ADRES1 || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Adres2:</div><div class="bayi-info-value">' + (bayi.ADRES2 || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Şehir:</div><div class="bayi-info-value">' + (bayi.SEHIR || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Vade:</div><div class="bayi-info-value">' + (bayi.VADE || '-') + '</div></div>';
    html += '<div class="bayi-info-row"><div class="bayi-info-label">Vergi Dairesi:</div><div class="bayi-info-value">' + (bayi.VERGI_DAIRESI || '-') + ' VD ' + (bayi.VERGI_NO || '-') + '</div></div>';
    html += '</div>';

    // Checkboxlar
    html += '<div class="bayi-checkboxes">';
    html += '<div class="bayi-checkbox-item"><input type="checkbox" id="dikkat_listesi_cb" data-ch-kodu="' + bayi.CH_KODU + '" ' + (bayi.dikkat_listesi == '1' ? 'checked' : '') + '> Dikkat Listesinde</div>';
    html += '<div class="bayi-checkbox-item"><input type="checkbox" id="kara_liste_cb" data-ch-kodu="' + bayi.CH_KODU + '" ' + (bayi.kara_liste == '1' ? 'checked' : '') + '> Kara Listede</div>';
    html += '</div>';

    $('#bayi-detail-content').html(html);

    // Checkbox event handlers ekle
    $('#dikkat_listesi_cb').on('change', function() {
        updateKaraListe($(this).data('ch-kodu'), 'dikkat_listesi', $(this).is(':checked') ? 1 : 0);
    });

    $('#kara_liste_cb').on('change', function() {
        updateKaraListe($(this).data('ch-kodu'), 'kara_liste', $(this).is(':checked') ? 1 : 0);
    });
}

function updateKaraListe(ch_kodu, field, value) {
    $.ajax({
        url: '_tablolar/kt__bayiler_kara_liste_update.php',
        method: 'POST',
        data: {
            ch_kodu: ch_kodu,
            field: field,
            value: value
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Grid'i yenile
                grid.refreshDataAndView();
            } else {
                alert('Hata: ' + response.error);
            }
        },
        error: function() {
            alert('Güncelleme sırasında hata oluştu');
        }
    });
}

function closeBayiModal() {
    $('#bayi-detail-modal').removeClass('active');
}

// Modal dışına tıklanınca kapat
$(document).on('click', '.bayi-modal-overlay', function(e) {
    if ($(e.target).hasClass('bayi-modal-overlay')) {
        closeBayiModal();
    }
});
</script>

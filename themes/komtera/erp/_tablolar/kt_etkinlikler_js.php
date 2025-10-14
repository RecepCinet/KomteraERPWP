<script>
var grid;

function sil(ff) {
    if (confirm('Bu etkinliği silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '_service/delete_etkinlik.php?id=' + ff,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    grid.refreshDataAndView();
                    alert(response.message || 'Etkinlik başarıyla silindi.');
                } else {
                    alert(response.message || 'Etkinlik silinirken hata oluştu.');
                }
            },
            error: function(xhr, status, error) {
                alert('Etkinlik silinirken hata oluştu: ' + error);
            }
        });
    }
}

function EtkinlikAc(id) {
    // AJAX ile etkinlik bilgilerini getir
    $.ajax({
        url: '_tablolar/kt_etkinlikler.php?id=' + id,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var etkinlik = response.data[0];

                // Form alanlarını doldur
                $('#etkinlik_id').val(etkinlik.id);
                $('#etkinlik_marka').val(etkinlik.marka);
                $('#etkinlik_baslik').val(etkinlik.baslik);
                $('#etkinlik_kodu').val(etkinlik.kodu);

                // Tarihleri formatla (YYYY-MM-DD formatına çevir)
                if (etkinlik.tarih_bas) {
                    var tarihBas = etkinlik.tarih_bas.split(' ')[0]; // "2025-05-06 00:00:00.000" -> "2025-05-06"
                    $('#etkinlik_tarih_bas').val(tarihBas);
                }
                if (etkinlik.tarih_bit) {
                    var tarihBit = etkinlik.tarih_bit.split(' ')[0];
                    $('#etkinlik_tarih_bit').val(tarihBit);
                }

                // Modal'ı aç
                $('#etkinlik_modal').show();
            }
        },
        error: function() {
            alert('Etkinlik bilgileri yüklenirken hata oluştu.');
        }
    });
}

function closeEtkinlikModal() {
    $('#etkinlik_modal').hide();
    $('#etkinlik_form')[0].reset();
}

function saveEtkinlik() {
    var formData = {
        id: $('#etkinlik_id').val(),
        marka: $('#etkinlik_marka').val(),
        baslik: $('#etkinlik_baslik').val(),
        kodu: $('#etkinlik_kodu').val(),
        tarih_bas: $('#etkinlik_tarih_bas').val(),
        tarih_bit: $('#etkinlik_tarih_bit').val()
    };

    $.ajax({
        url: '_service/update_etkinlik.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                closeEtkinlikModal();
                grid.refreshDataAndView();
                alert(response.message || 'Etkinlik başarıyla güncellendi.');
            } else {
                alert(response.message || 'Etkinlik güncellenirken hata oluştu.');
            }
        },
        error: function(xhr, status, error) {
            alert('Etkinlik güncellenirken hata oluştu: ' + error);
        }
    });
}

function yeniEtkinlikEkle() {
    var newRow = {
        marka: '',
        baslik: 'Yeni Etkinlik',
        kodu: '',
        tarih_bas: new Date().toISOString().split('T')[0],
        tarih_bit: new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0]
    };

    $.ajax({
        url: '_service/insert_etkinlik.php',
        method: 'POST',
        data: newRow,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                grid.refreshDataAndView();
            } else {
                alert(response.message || 'Yeni etkinlik eklenirken hata oluştu.');
            }
        },
        error: function(xhr, status, error) {
            alert('Yeni etkinlik eklenirken hata oluştu: ' + error);
        }
    });
}

// Modal dışına tıklandığında kapat
$(document).on('click', function(event) {
    if (event.target.id === 'etkinlik_modal') {
        closeEtkinlikModal();
    }
});

// X butonuna tıklandığında kapat
$(document).ready(function() {
    $('#etkinlik_modal_close').on('click', function() {
        closeEtkinlikModal();
    });
});

$(function () {
    var colM = [
        {title: "Marka", width: 120, dataIndx: "marka",
            filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "Başlık", width: 300, dataIndx: "baslik",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Kodu", width: 150, dataIndx: "kodu",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Başlangıç", align: "center", width: 100, dataIndx: "tarih_bas"},
        {title: "Bitiş", align: "center", width: 100, dataIndx: "tarih_bit",
            filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "Durum", align: "center", width: 100, dataIndx: "BITTI",
            filter: {
                crules: [{condition: 'range'}]
            },
            render: function (ui) {
                if (ui.cellData == 'Bitti') {
                    return { style: { "background": "#ffebee", "color": "#c62828", "font-weight": "500" } };
                } else {
                    return { style: { "background": "#e8f5e9", "color": "#2e7d32", "font-weight": "500" } };
                }
            }
        },
        {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA",
            render: function (ui) {
                var out = '';
                if (ui.rowData.id !== undefined) {
                    out += '<a href="#" onclick="EtkinlikAc(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-pencil" style="color: rgb(255, 0, 0);"></span> </a>';
                }
                return out;
            }
        },
        {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA",
            render: function (ui) {
                var out = '';
                if (ui.rowData.id !== undefined) {
                    out += '<a href="#" onclick="sil(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-close" style="color: rgb(255, 0, 0);"></span> </a>';
                }
                return out;
            }
        }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_etkinlikler.php?dbname=LKS",
        getData: function (response) {
                    return { data: response.data };
        }
    };

    var obj = {
        trackModel: { on: true },
        menuIcon: true,
        collapsible: {on: false, toggle: false},
        reactive: true,
        sortModel: {
            type: 'local',
            single: true,
            sorter: [{ dataIndx: 'BITTI', dir: 'down' }, { dataIndx: 'tarih_bit', dir: 'down' }],
            space: true,
            multiKey: false
        },
        roundCorners: false,
        rowBorders: true,
        selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},
        showHeader: true,
        showTitle: true,
        groupModel: {
            on: true,
            dataIndx: ['BITTI'],
            menuIcon: false,
            collapsed: function(ui) {
                // "Bitti" grupları kapalı, "Devam" grupları açık
                return ui.group === 'Bitti';
            }
        },
        showToolbar: true,
        showTop: true,
        width: 'flex',
        height: 'flex',
        maxHeight: 600,
        toolbar: {
            items: [
                {
                    type: 'button',
                    label: 'Ekle',
                    icon: 'ui-icon-plus',
                    listener: function() {
                        yeniEtkinlikEkle();
                    }
                }
            ]
        },
        dataModel: dataModelSS,
        colModel: colM,
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
        sortable: true,
        rowHt: 23,
        wrap: false,
        hwrap: false,
        numberCell: {resizable: true, width: 55, title: "#"},
        title: 'Etkinlikler',
        resizable: true
    };
    grid = pq.grid("div#grid_etkinlikler", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
    
});
</script>

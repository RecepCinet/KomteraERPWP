<script>
var grid;

function sil(ff) {
    if (confirm('Bu kampanyayı silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '_service/delete_kampanya.php?id=' + ff,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    grid.refreshDataAndView();
                    alert(response.message || 'Kampanya başarıyla silindi.');
                } else {
                    alert(response.message || 'Kampanya silinirken hata oluştu.');
                }
            },
            error: function(xhr, status, error) {
                alert('Kampanya silinirken hata oluştu: ' + error);
            }
        });
    }
}

function KampanyaAc(id) {
    $.ajax({
        url: '_tablolar/kt_kampanyalar.php?id=' + id,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var kampanya = response.data[0];
                $('#kampanya_id').val(kampanya.id);
                $('#kampanya_marka').val(kampanya.marka);
                $('#kampanya_baslik').val(kampanya.baslik);
                $('#kampanya_kodu').val(kampanya.kodu);

                if (kampanya.tarih_bas) {
                    var tarihBas = kampanya.tarih_bas.split(' ')[0];
                    $('#kampanya_tarih_bas').val(tarihBas);
                }
                if (kampanya.tarih_bit) {
                    var tarihBit = kampanya.tarih_bit.split(' ')[0];
                    $('#kampanya_tarih_bit').val(tarihBit);
                }

                $('#kampanya_modal').show();
            }
        },
        error: function() {
            alert('Kampanya bilgileri yüklenirken hata oluştu.');
        }
    });
}

function closeKampanyaModal() {
    $('#kampanya_modal').hide();
    $('#kampanya_form')[0].reset();
}

function saveKampanya() {
    var formData = {
        id: $('#kampanya_id').val(),
        marka: $('#kampanya_marka').val(),
        baslik: $('#kampanya_baslik').val(),
        kodu: $('#kampanya_kodu').val(),
        tarih_bas: $('#kampanya_tarih_bas').val(),
        tarih_bit: $('#kampanya_tarih_bit').val()
    };

    $.ajax({
        url: '_service/update_kampanya.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                closeKampanyaModal();
                grid.refreshDataAndView();
                alert(response.message || 'Kampanya başarıyla güncellendi.');
            } else {
                alert(response.message || 'Kampanya güncellenirken hata oluştu.');
            }
        },
        error: function(xhr, status, error) {
            alert('Kampanya güncellenirken hata oluştu: ' + error);
        }
    });
}

function yeniKampanyaEkle() {
    var newRow = {
        marka: '',
        baslik: 'Yeni Kampanya',
        kodu: '',
        tarih_bas: new Date().toISOString().split('T')[0],
        tarih_bit: new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0]
    };

    $.ajax({
        url: '_service/insert_kampanya.php',
        method: 'POST',
        data: newRow,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                grid.refreshDataAndView();
            } else {
                alert(response.message || 'Yeni kampanya eklenirken hata oluştu.');
            }
        },
        error: function(xhr, status, error) {
            alert('Yeni kampanya eklenirken hata oluştu: ' + error);
        }
    });
}

$(document).on('click', function(event) {
    if (event.target.id === 'kampanya_modal') {
        closeKampanyaModal();
    }
});

$(document).ready(function() {
    $('#kampanya_modal_close').on('click', function() {
        closeKampanyaModal();
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
                    out += '<a href="#" onclick="KampanyaAc(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-pencil" style="color: rgb(255, 0, 0);"></span> </a>';
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
        url: "_tablolar/kt_kampanyalar.php?dbname=LKS",
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
                        yeniKampanyaEkle();
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
        title: 'Kampanyalar',
        resizable: true
    };

    grid = pq.grid("div#grid_kampanyalar", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>

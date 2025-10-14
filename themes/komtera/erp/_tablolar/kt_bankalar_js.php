<script>
var grid;

function sil(ff) {
    if (confirm('Bu banka kaydını silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '_service/delete_banka.php?id=' + ff,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    grid.refreshDataAndView();
                } else {
                    alert(response.message || 'Banka kaydı silinirken hata oluştu.');
                }
            },
            error: function(xhr, status, error) {
                alert('Banka kaydı silinirken hata oluştu: ' + error);
            }
        });
    }
}

function BankaAc(id) {
    $.ajax({
        url: '_tablolar/kt_bankalar.php?id=' + id,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var banka = response.data[0];
                $('#banka_id').val(banka.id);
                $('#banka_sira').val(banka.sira);
                $('#banka_adi').val(banka.banka);
                $('#banka_iban').val(banka.iban);
                $('#banka_kur').val(banka.kur);

                $('#banka_modal').show();
            }
        },
        error: function() {
            alert('Banka bilgileri yüklenirken hata oluştu.');
        }
    });
}

function closeBankaModal() {
    $('#banka_modal').hide();
    $('#banka_form')[0].reset();
}

function saveBanka() {
    var id = $('#banka_id').val();
    var formData = {
        sira: $('#banka_sira').val(),
        banka: $('#banka_adi').val(),
        iban: $('#banka_iban').val(),
        kur: $('#banka_kur').val()
    };

    // Yeni kayıt mı güncelleme mi?
    var url = id ? '_service/update_banka.php' : '_service/insert_banka.php';
    if (id) {
        formData.id = id;
    }

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                closeBankaModal();
                grid.refreshDataAndView();
            } else {
                alert(response.message || 'İşlem sırasında hata oluştu.');
            }
        },
        error: function(xhr, status, error) {
            alert('İşlem sırasında hata oluştu: ' + error);
        }
    });
}

function yeniSatirEkle() {
    // Modal'ı aç ama ID olmadan (yeni kayıt)
    $('#banka_id').val('');
    $('#banka_sira').val('1');
    $('#banka_adi').val('');
    $('#banka_iban').val('');
    $('#banka_kur').val('TRY');
    $('#banka_modal').show();
}

$(document).on('click', function(event) {
    if (event.target.id === 'banka_modal') {
        closeBankaModal();
    }
});

$(document).ready(function() {
    $('#banka_modal_close').on('click', function() {
        closeBankaModal();
    });
});

$(function () {
    var colM = [
        {title: "Sıra", width: 80, dataIndx: "sira", align: "center"},
        {title: "Banka", width: 250, dataIndx: "banka",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "IBAN", width: 350, dataIndx: "iban",
            filter: {
                crules: [{condition: 'contain'}]
            }
        },
        {title: "Para Birimi", width: 150, dataIndx: "kur", align: "center",
            filter: {
                crules: [{condition: 'range'}]
            }
        },
        {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "EDIT",
            render: function (ui) {
                var out = '';
                if (ui.rowData.id !== undefined) {
                    out += '<a href="#" onclick="BankaAc(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-pencil" style="color: rgb(255, 0, 0);"></span> </a>';
                }
                return out;
            }
        },
        {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "DEL",
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
        url: "_tablolar/kt_bankalar.php?dbname=LKS",
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
            sorter: [{ dataIndx: 'kur' }, { dataIndx: 'sira' }],
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
        groupModel: {on: true, collapsed: [false], menuIcon: false, dataIndx: ['kur']},
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
                        yeniSatirEkle();
                    }
                }
            ]
        },
        dataModel: dataModelSS,
        colModel: colM,
        freezeCols: 0,
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
        rowHt: 30,
        wrap: false,
        hwrap: false,
        numberCell: {show: false},
        title: 'Bankalar',
        resizable: true,
        rowInit: function(ui) {
            var rowData = ui.rowData;
            if (rowData.kur === 'USD') {
                return { cls: 'currency-usd' };
            } else if (rowData.kur === 'TRY') {
                return { cls: 'currency-try' };
            } else if (rowData.kur === 'EUR') {
                return { cls: 'currency-eur' };
            }
        }
    };

    grid = pq.grid("div#grid_bankalar", obj);
    grid.toggle();
    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    })
});
</script>

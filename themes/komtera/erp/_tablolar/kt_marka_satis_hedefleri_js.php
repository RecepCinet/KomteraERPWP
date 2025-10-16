<script>
var grid;
var originalData = [];

function geriAl() {
    if (confirm('Tüm değişiklikleri geri almak istediğinizden emin misiniz?')) {
        grid.refreshDataAndView();
    }
}

function tumunuKaydet() {
    var changes = grid.getChanges();

    if (!changes || !changes.updateList || changes.updateList.length === 0) {
        alert('Kaydedilecek değişiklik yok.');
        return;
    }

    if (!confirm(changes.updateList.length + ' kayıt güncellenecek. Devam etmek istiyor musunuz?')) {
        return;
    }

    var successCount = 0;
    var errorCount = 0;
    var total = changes.updateList.length;

    changes.updateList.forEach(function(row, index) {
        $.ajax({
            url: '_service/update_marka_hedef.php',
            method: 'POST',
            data: {
                id: row.id,
                marka: row.marka,
                q1: row.q1 || 0,
                q2: row.q2 || 0,
                q3: row.q3 || 0,
                q4: row.q4 || 0
            },
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            },
            error: function() {
                errorCount++;
            }
        });
    });

    if (errorCount === 0) {
        alert('Tüm kayıtlar başarıyla güncellendi (' + successCount + ' kayıt)');
        grid.refreshDataAndView();
    } else {
        alert('Bazı kayıtlar güncellenemedi. Başarılı: ' + successCount + ', Hatalı: ' + errorCount);
        grid.refreshDataAndView();
    }
}

$(function () {
    var colM = [
        {title: "Marka", width: 150, dataIndx: "marka", editable: false},
        {title: "Q1", width: 150, dataIndx: "q1", align: "right", editable: true,
            dataType: "float",
            format: '#,###',
            editor: {
                type: 'textbox'
            }
        },
        {title: "Q2", width: 150, dataIndx: "q2", align: "right", editable: true,
            dataType: "float",
            format: '#,###',
            editor: {
                type: 'textbox'
            }
        },
        {title: "Q3", width: 150, dataIndx: "q3", align: "right", editable: true,
            dataType: "float",
            format: '#,###',
            editor: {
                type: 'textbox'
            }
        },
        {title: "Q4", width: 150, dataIndx: "q4", align: "right", editable: true,
            dataType: "float",
            format: '#,###',
            editor: {
                type: 'textbox'
            }
        }
    ];

    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/kt_marka_satis_hedefleri.php?dbname=LKS",
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
            sorter: [{ dataIndx: 'marka' }],
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
        showToolbar: false,
        showTop: true,
        width: 'flex',
        height: 'flex',
        maxHeight: 600,
        dataModel: dataModelSS,
        colModel: colM,
        freezeCols: 1,
        filterModel: {
            on: false
        },
        editable: true,
        editModel: {
            clicksToEdit: 1,
            saveKey: $.ui.keyCode.ENTER
        },
        sortable: true,
        rowHt: 30,
        wrap: false,
        hwrap: false,
        numberCell: {show: false},
        title: 'Marka Satış Hedefleri',
        resizable: true
    };

    grid = pq.grid("div#grid_marka_hedefler", obj);
    grid.toggle();

    $(window).on('unload', function () {
        grid.saveState();
    });
    grid.on("destroy", function () {
        this.saveState();
    });
});
</script>

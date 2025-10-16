<script>
var savedMarkalar = []; // Veritabanındaki markalar
var allMarkalar = []; // Fiyat listesindeki tüm markalar

// Sayfa yüklendiğinde
$(function() {
    loadSavedMarkalar();
    loadAllMarkalar();
});

// Kaydedilmiş markaları yükle
function loadSavedMarkalar() {
    $.ajax({
        url: '_tablolar/kt_tl_faturalanmayacak_markalar.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                savedMarkalar = response.data;
                renderMarkaList();
            } else if (response && response.data && response.data.length === 0) {
                savedMarkalar = [];
                renderMarkaList();
            } else {
                $('#markalar-list').html('<div class="no-data">Veri yüklenirken hata oluştu</div>');
            }
        },
        error: function() {
            $('#markalar-list').html('<div class="no-data">Veri yüklenirken hata oluştu</div>');
        }
    });
}

// Tüm markaları yükle (fiyat listesinden)
function loadAllMarkalar() {
    $.ajax({
        url: '_tablolar/kt_markalar.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                allMarkalar = response.data;
            }
        },
        error: function() {
            console.error('Marka listesi yüklenirken hata oluştu');
        }
    });
}

// Marka listesini render et
function renderMarkaList() {
    var container = $('#markalar-list');
    container.empty();

    if (!savedMarkalar || savedMarkalar.length === 0) {
        container.html(`
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <div class="empty-state-text">Henüz marka eklenmemiş.<br>"+ Ekle" butonuna tıklayarak ekleyebilirsiniz.</div>
            </div>
        `);
        return;
    }

    savedMarkalar.forEach(function(item) {
        var markaHtml = `
            <div class="tl-marka-item">
                <div class="tl-marka-name">${item.marka}</div>
                <button class="btn-remove-marka" onclick="removeMarka(${item.id}, '${item.marka.replace(/'/g, "\\'")}')">Çıkar</button>
            </div>
        `;
        container.append(markaHtml);
    });
}

// Marka seçim modalını aç
function openMarkaSelectionModal() {
    $('#marka-selection-modal').addClass('active');
    $('#marka-search').val('');
    renderAvailableMarkas();
}

function closeMarkaSelectionModal() {
    $('#marka-selection-modal').removeClass('active');
}

// Eklenebilir markaları göster (henüz eklenmemiş olanlar)
function renderAvailableMarkas(filtered = null) {
    var list = $('#available-markalar-list');
    list.empty();

    var markasToShow = filtered || allMarkalar;

    if (!markasToShow || markasToShow.length === 0) {
        list.html('<div class="no-data">Marka bulunamadı</div>');
        return;
    }

    // Zaten eklenmiş markaları filtrele
    var savedMarkaNames = savedMarkalar.map(function(m) { return m.marka; });

    var availableMarkas = markasToShow.filter(function(item) {
        return savedMarkaNames.indexOf(item.marka) === -1;
    });

    if (availableMarkas.length === 0) {
        list.html('<div class="no-data">Tüm markalar zaten eklenmiş</div>');
        return;
    }

    availableMarkas.forEach(function(item) {
        var html = `
            <div class="selection-item" onclick="addMarka('${item.marka.replace(/'/g, "\\'")}')">
                <div class="selection-item-title">${item.marka}</div>
            </div>
        `;
        list.append(html);
    });
}

// Marka ara
function filterAvailableMarkas() {
    var searchTerm = $('#marka-search').val().toLowerCase();

    if (!searchTerm) {
        renderAvailableMarkas();
        return;
    }

    var filtered = allMarkalar.filter(function(item) {
        return item.marka.toLowerCase().indexOf(searchTerm) >= 0;
    });

    renderAvailableMarkas(filtered);
}

// Marka ekle
function addMarka(marka) {
    $.ajax({
        url: '_service/add_tl_faturalanmayacak_marka.php',
        method: 'POST',
        data: { marka: marka },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                closeMarkaSelectionModal();
                loadSavedMarkalar();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Marka eklenirken bir hata oluştu!');
        }
    });
}

// Marka çıkar
function removeMarka(id, marka) {
    if (!confirm('"' + marka + '" markasını çıkarmak istediğinizden emin misiniz?')) {
        return;
    }

    $.ajax({
        url: '_service/remove_tl_faturalanmayacak_marka.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadSavedMarkalar();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Marka çıkarılırken bir hata oluştu!');
        }
    });
}

// Tümünü çıkar
function clearAllMarkas() {
    if (savedMarkalar.length === 0) {
        alert('Çıkarılacak marka yok!');
        return;
    }

    if (!confirm('Tüm markaları çıkarmak istediğinizden emin misiniz? (' + savedMarkalar.length + ' marka)')) {
        return;
    }

    $.ajax({
        url: '_service/clear_all_tl_faturalanmayacak_markalar.php',
        method: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Tüm markalar başarıyla çıkarıldı!');
                loadSavedMarkalar();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('İşlem sırasında bir hata oluştu!');
        }
    });
}

// Listeyi yenile
function refreshList() {
    loadSavedMarkalar();
    loadAllMarkalar();
}

// Modal dışına tıklanınca kapat
$(document).on('click', '.modal-overlay', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
        closeMarkaSelectionModal();
    }
});
</script>

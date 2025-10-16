<script>
var markaData = [];
var bayiData = [];
var usersData = [];
var currentUserType = ''; // 'eski' veya 'yeni'

// Sayfa yüklendiğinde
$(function() {
    loadMarkaList();
    loadUsersData();
});

// Marka listesini yükle
function loadMarkaList() {
    $.ajax({
        url: '_tablolar/kt_markalar.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                markaData = response.data;
                renderMarkaDropdown();
            } else {
                $('#marka').html('<option value="">Marka bulunamadı</option>');
            }
        },
        error: function() {
            $('#marka').html('<option value="">Hata oluştu</option>');
        }
    });
}

function renderMarkaDropdown() {
    var html = '<option value="">Marka seçiniz</option>';
    markaData.forEach(function(item) {
        html += '<option value="' + item.marka + '">' + item.marka + '</option>';
    });
    $('#marka').html(html);
}

// Bayi listesini yükle
function loadBayiData() {
    if (bayiData.length > 0) {
        renderBayiList(bayiData);
        return;
    }

    $.ajax({
        url: '_tablolar/kt_bayiler_view.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                bayiData = response.data;
                renderBayiList(bayiData);
            } else {
                $('#bayi-list').html('<div class="no-data">Bayi bulunamadı</div>');
            }
        },
        error: function() {
            $('#bayi-list').html('<div class="no-data">Bayiler yüklenirken hata oluştu</div>');
        }
    });
}

function renderBayiList(data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<div class="no-data">Bayi bulunamadı</div>';
    } else {
        data.forEach(function(bayi) {
            html += '<div class="selection-item" onclick="selectBayi(\'' +
                    bayi.CH_KODU.replace(/'/g, "\\'") + '\', \'' +
                    (bayi.CH_UNVANI || '').replace(/'/g, "\\'") + '\')">';
            html += '<div class="selection-item-title">' + (bayi.CH_UNVANI || '-') + '</div>';
            html += '<div class="selection-item-subtitle">' + bayi.CH_KODU + '</div>';
            html += '</div>';
        });
    }
    $('#bayi-list').html(html);
}

function filterBayiList() {
    var searchTerm = $('#bayi-search').val().toLowerCase();

    if (!searchTerm) {
        renderBayiList(bayiData);
        return;
    }

    var filtered = bayiData.filter(function(bayi) {
        var chKodu = (bayi.CH_KODU || '').toLowerCase();
        var chUnvani = (bayi.CH_UNVANI || '').toLowerCase();
        return chKodu.indexOf(searchTerm) >= 0 || chUnvani.indexOf(searchTerm) >= 0;
    });

    renderBayiList(filtered);
}

function selectBayi(chKodu, chUnvani) {
    $('#bayi-ch-kodu').val(chKodu);
    $('#bayi-unvani').val(chUnvani);
    closeBayiModal();
}

// Kullanıcı listesini yükle
function loadUsersData() {
    $.ajax({
        url: '_tablolar/kt_wp_users.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                usersData = response.data;
            }
        },
        error: function() {
            console.error('Kullanıcılar yüklenirken hata oluştu');
        }
    });
}

function renderUserList(data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<div class="no-data">Kullanıcı bulunamadı</div>';
    } else {
        data.forEach(function(user) {
            html += '<div class="selection-item" onclick="selectUser(\'' +
                    user.display_name.replace(/'/g, "\\'") + '\')">';
            html += '<div class="selection-item-title">' + user.display_name + '</div>';
            html += '<div class="selection-item-subtitle">' + user.user_email + '</div>';
            html += '</div>';
        });
    }
    $('#user-list').html(html);
}

function filterUserList() {
    var searchTerm = $('#user-search').val().toLowerCase();

    if (!searchTerm) {
        renderUserList(usersData);
        return;
    }

    var filtered = usersData.filter(function(user) {
        var displayName = (user.display_name || '').toLowerCase();
        var email = (user.user_email || '').toLowerCase();
        return displayName.indexOf(searchTerm) >= 0 || email.indexOf(searchTerm) >= 0;
    });

    renderUserList(filtered);
}

function selectUser(displayName) {
    if (currentUserType === 'eski') {
        $('#eski-temsilci').val(displayName);
    } else if (currentUserType === 'yeni') {
        $('#yeni-temsilci').val(displayName);
    }
    closeUserModal();
}

// Modal açma/kapama fonksiyonları
function openBayiModal() {
    $('#bayi-modal').addClass('active');
    $('#bayi-search').val('');
    loadBayiData();
}

function closeBayiModal() {
    $('#bayi-modal').removeClass('active');
}

function openUserModal(type) {
    currentUserType = type;
    $('#user-modal').addClass('active');
    $('#user-search').val('');
    renderUserList(usersData);
}

function closeUserModal() {
    $('#user-modal').removeClass('active');
    currentUserType = '';
}

function openMarkaModal() {
    $('#marka-modal').addClass('active');
    $('#marka-search').val('');
    renderMarkaModalList(markaData);
}

function closeMarkaModal() {
    $('#marka-modal').removeClass('active');
}

function renderMarkaModalList(data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<div class="no-data">Marka bulunamadı</div>';
    } else {
        data.forEach(function(item) {
            html += '<div class="selection-item" onclick="selectMarka(\'' +
                    item.marka.replace(/'/g, "\\'") + '\')">';
            html += '<div class="selection-item-title">' + item.marka + '</div>';
            html += '</div>';
        });
    }
    $('#marka-list').html(html);
}

function filterMarkaList() {
    var searchTerm = $('#marka-search').val().toLowerCase();

    if (!searchTerm) {
        renderMarkaModalList(markaData);
        return;
    }

    var filtered = markaData.filter(function(item) {
        return item.marka.toLowerCase().indexOf(searchTerm) >= 0;
    });

    renderMarkaModalList(filtered);
}

function selectMarka(marka) {
    $('#marka').val(marka);
    closeMarkaModal();
}

// Form işlemleri
function clearForm() {
    $('#marka').val('');
    $('#bayi-ch-kodu').val('');
    $('#bayi-unvani').val('');
    $('#eski-temsilci').val('');
    $('#yeni-temsilci').val('');
}

function submitChange() {
    var marka = $('#marka').val();
    var bayiChKodu = $('#bayi-ch-kodu').val();
    var eskiTemsilci = $('#eski-temsilci').val();
    var yeniTemsilci = $('#yeni-temsilci').val();

    // Validasyon
    if (!marka) {
        alert('Lütfen marka seçiniz!');
        return;
    }

    if (!eskiTemsilci) {
        alert('Lütfen eski müşteri temsilcisi seçiniz!');
        return;
    }

    if (!yeniTemsilci) {
        alert('Lütfen yeni müşteri temsilcisi seçiniz!');
        return;
    }

    if (eskiTemsilci === yeniTemsilci) {
        alert('Eski ve yeni temsilci aynı olamaz!');
        return;
    }

    // Onay
    var confirmMsg = 'Aşağıdaki değişiklik yapılacak:\n\n';
    confirmMsg += 'Marka: ' + marka + '\n';
    if (bayiChKodu) {
        confirmMsg += 'Bayi: ' + bayiChKodu + '\n';
    }
    confirmMsg += 'Eski Temsilci: ' + eskiTemsilci + '\n';
    confirmMsg += 'Yeni Temsilci: ' + yeniTemsilci + '\n\n';
    confirmMsg += 'Bu işlem GERİ ALINAMAZ! Devam etmek istiyor musunuz?';

    if (!confirm(confirmMsg)) {
        return;
    }

    // Backend'e gönder
    $.ajax({
        url: '_service/musteri_temsilcisi_degistir.php',
        method: 'POST',
        data: {
            marka: marka,
            bayi_ch_kodu: bayiChKodu,
            eski_temsilci: eskiTemsilci,
            yeni_temsilci: yeniTemsilci
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Başarılı!\n\n' + response.message + '\n\nEtkilenen kayıt sayısı: ' + response.affected_rows);
                clearForm();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('İşlem sırasında bir hata oluştu!');
        }
    });
}

// Modal dışına tıklanınca kapat
$(document).on('click', '.modal-overlay', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
        closeBayiModal();
        closeUserModal();
        closeMarkaModal();
    }
});
</script>

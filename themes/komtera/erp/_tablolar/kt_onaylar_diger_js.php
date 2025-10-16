<script>
var originalData = [];
var currentData = [];
var modifiedCards = new Set();
var usersData = [];
var currentUserCardId = null;

function loadData() {
    $.ajax({
        url: '_tablolar/kt_onaylar_diger.php?dbname=LKS',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                originalData = JSON.parse(JSON.stringify(response.data));
                currentData = JSON.parse(JSON.stringify(response.data));
                renderCards();
            } else {
                $('#diger-cards-container').html('<div class="no-data">Veri bulunamadı</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#diger-cards-container').html('<div class="no-data">Veriler yüklenirken hata oluştu: ' + error + '</div>');
        }
    });
}

function renderCards() {
    var container = $('#diger-cards-container');
    container.empty();

    if (!currentData || currentData.length === 0) {
        container.html('<div class="no-data">Henüz kayıt yok. "+ Yeni" butonuna tıklayarak ekleyebilirsiniz.</div>');
        updateStats();
        return;
    }

    currentData.forEach(function(item) {
        var card = createCard(item);
        container.append(card);
    });

    updateStats();
}

function createCard(item) {
    var isModified = modifiedCards.has(item.id);
    var modifiedClass = isModified ? 'modified' : '';

    var card = $('<div>', {
        class: 'diger-card ' + modifiedClass,
        'data-id': item.id
    });

    var cardHtml = `
        <div class="diger-card-header">
            <div class="diger-card-title">${item.key_name || '-'}</div>
            <div class="diger-card-actions">
                <button class="btn-delete-card" onclick="deleteCard(${item.id})" title="Sil">Sil</button>
                <div class="diger-card-id">#${item.id}</div>
            </div>
        </div>
        <div class="diger-card-body">
            <div class="diger-field-group">
                <label class="diger-field-label">Açıklama</label>
                <textarea class="diger-field-textarea"
                          data-field="aciklama"
                          onchange="updateField(${item.id}, 'aciklama', this.value)">${item.aciklama || ''}</textarea>
            </div>
            <div class="diger-field-group">
                <label class="diger-field-label">Kullanıcı</label>
                <div class="user-input-group">
                    <input type="text"
                           class="diger-field-input"
                           data-field="kullanici"
                           value="${item.kullanici || ''}"
                           onchange="updateField(${item.id}, 'kullanici', this.value)">
                    <button class="btn-select-user" onclick="openUserSelectionModal(${item.id})">Seç</button>
                </div>
            </div>
        </div>
    `;

    card.html(cardHtml);
    return card;
}

function updateField(id, field, value) {
    var item = currentData.find(function(i) { return i.id == id; });
    if (!item) return;

    var originalItem = originalData.find(function(i) { return i.id == id; });

    item[field] = value;

    var isModified = false;
    ['key_name', 'aciklama', 'kullanici'].forEach(function(f) {
        if ((item[f] || '') != (originalItem[f] || '')) {
            isModified = true;
        }
    });

    var $card = $('.diger-card[data-id="' + id + '"]');

    if (isModified) {
        modifiedCards.add(id);
        $card.addClass('modified');
        $card.find('[data-field="' + field + '"]').addClass('modified');
    } else {
        modifiedCards.delete(id);
        $card.removeClass('modified');
        $card.find('.diger-field-input, .diger-field-textarea').removeClass('modified');
    }

    updateStats();
}

function updateStats() {
    var modCount = modifiedCards.size;
    var statsHtml = currentData.length + ' kayıt';
    if (modCount > 0) {
        statsHtml += ' (' + modCount + ' değişiklik)';
    }
    $('#card-stats').html(statsHtml);
}

function resetChanges() {
    if (!confirm('Tüm değişiklikleri geri almak istediğinizden emin misiniz?')) {
        return;
    }

    modifiedCards.clear();
    currentData = JSON.parse(JSON.stringify(originalData));
    renderCards();
}

function saveAllChanges() {
    if (modifiedCards.size === 0) {
        alert('Kaydedilecek değişiklik yok.');
        return;
    }

    if (!confirm(modifiedCards.size + ' kayıt güncellenecek. Devam etmek istiyor musunuz?')) {
        return;
    }

    var successCount = 0;
    var errorCount = 0;

    modifiedCards.forEach(function(id) {
        var item = currentData.find(function(i) { return i.id == id; });
        if (!item) return;

        $.ajax({
            url: '_service/update_onaylar_diger.php',
            method: 'POST',
            data: {
                id: item.id,
                key_name: item.key_name,
                aciklama: item.aciklama || '',
                kullanici: item.kullanici || ''
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
        originalData = JSON.parse(JSON.stringify(currentData));
        modifiedCards.clear();
        renderCards();
    } else {
        alert('Bazı kayıtlar güncellenemedi. Başarılı: ' + successCount + ', Hatalı: ' + errorCount);
    }
}

function deleteCard(id) {
    if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) {
        return;
    }

    $.ajax({
        url: '_service/delete_onaylar_diger.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Kayıt başarıyla silindi!');
                originalData = originalData.filter(function(i) { return i.id != id; });
                currentData = currentData.filter(function(i) { return i.id != id; });
                modifiedCards.delete(id);
                renderCards();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Kayıt silinirken bir hata oluştu.');
        }
    });
}

function openNewRecordModal() {
    $('#new-key').val('');
    $('#new-aciklama').val('');
    $('#new-kullanici').val('');
    $('#new-record-modal').addClass('active');
}

function closeNewRecordModal() {
    $('#new-record-modal').removeClass('active');
}

function saveNewRecord() {
    var key_name = $('#new-key').val().trim();
    var aciklama = $('#new-aciklama').val().trim();
    var kullanici = $('#new-kullanici').val().trim();

    if (!key_name) {
        alert('Key (Anahtar) zorunludur!');
        return;
    }

    $.ajax({
        url: '_service/insert_onaylar_diger.php',
        method: 'POST',
        data: {
            key_name: key_name,
            aciklama: aciklama,
            kullanici: kullanici
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Yeni kayıt başarıyla eklendi!');
                closeNewRecordModal();
                loadData();
            } else {
                alert('Hata: ' + response.message);
            }
        },
        error: function() {
            alert('Kayıt eklenirken bir hata oluştu.');
        }
    });
}

function openUserSelectionModal(cardId) {
    currentUserCardId = cardId;
    $('#user-selection-modal').addClass('active');

    if (usersData.length === 0) {
        loadUsersData();
    } else {
        renderUserList(usersData);
    }
}

function closeUserSelectionModal() {
    $('#user-selection-modal').removeClass('active');
    $('#user-search-input').val('');
    currentUserCardId = null;
}

function loadUsersData() {
    $.ajax({
        url: '_tablolar/kt_wp_users.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                usersData = response.data;
                renderUserList(usersData);
            } else {
                $('#user-list').html('<div class="no-data">Kullanıcı bulunamadı</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#user-list').html('<div class="no-data">Kullanıcılar yüklenirken hata oluştu: ' + error + '</div>');
        }
    });
}

function renderUserList(users) {
    var listHtml = '';

    if (!users || users.length === 0) {
        listHtml = '<div class="no-data">Kullanıcı bulunamadı</div>';
    } else {
        users.forEach(function(user) {
            listHtml += `
                <div class="user-item" onclick="selectUser('${user.display_name.replace(/'/g, "\\'")}')">
                    <div class="user-item-name">${user.display_name}</div>
                    <div class="user-item-email">${user.user_email}</div>
                </div>
            `;
        });
    }

    $('#user-list').html(listHtml);
}

function filterUserList() {
    var searchTerm = $('#user-search-input').val().toLowerCase();

    if (!searchTerm) {
        renderUserList(usersData);
        return;
    }

    var filtered = usersData.filter(function(user) {
        var displayName = (user.display_name || '').toLowerCase();
        var email = (user.user_email || '').toLowerCase();
        var login = (user.user_login || '').toLowerCase();
        return displayName.indexOf(searchTerm) >= 0 || email.indexOf(searchTerm) >= 0 || login.indexOf(searchTerm) >= 0;
    });

    renderUserList(filtered);
}

function selectUser(displayName) {
    if (currentUserCardId === 'new') {
        $('#new-kullanici').val(displayName);
    } else {
        var $card = $('.diger-card[data-id="' + currentUserCardId + '"]');
        var $input = $card.find('[data-field="kullanici"]');
        $input.val(displayName);
        updateField(currentUserCardId, 'kullanici', displayName);
    }

    closeUserSelectionModal();
}

// Close modal on outside click
$(document).on('click', '.modal-overlay', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
        closeNewRecordModal();
        closeUserSelectionModal();
    }
});

$(function() {
    loadData();
});
</script>

<script>
var originalData = [];  // Never modified - baseline for comparison
var currentData = [];   // Current working data with modifications
var modifiedCards = new Set();
var currentFilter = '';

function loadData() {
    $.ajax({
        url: '_tablolar/kt_onaylar_kar_oranlari.php?dbname=LKS',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                // Deep clone for truly original immutable data
                originalData = JSON.parse(JSON.stringify(response.data));
                // Deep clone for working data
                currentData = JSON.parse(JSON.stringify(response.data));
                loadMarkaList();
                filterByMarka();
            } else {
                $('#kar-cards-container').html('<div class="no-data">Veri bulunamadı</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#kar-cards-container').html('<div class="no-data">Veriler yüklenirken hata oluştu: ' + error + '</div>');
        }
    });
}

function loadMarkaList() {
    $.ajax({
        url: '_tablolar/kt_markalar.php?dbname=LKS',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                populateMarkaFilter(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.log('Marka listesi yüklenemedi:', error);
            // Fallback to existing data
            var markalar = [...new Set(originalData.map(item => item.marka))].sort();
            populateMarkaFilter(markalar.map(m => ({marka: m})));
        }
    });
}

var markaListData = [];

function populateMarkaFilter(markaData) {
    // Store for later use in modal
    markaListData = markaData;

    var select = $('#marka-filter');
    select.find('option:not(:first)').remove();

    markaData.forEach(function(item) {
        if (item.marka) {
            select.append($('<option>', {
                value: item.marka,
                text: item.marka
            }));
        }
    });
}

function populateNewMarkaDropdown() {
    var select = $('#new-marka');
    select.find('option:not(:first)').remove();

    markaListData.forEach(function(item) {
        if (item.marka) {
            select.append($('<option>', {
                value: item.marka,
                text: item.marka
            }));
        }
    });
}

function filterByMarka() {
    currentFilter = $('#marka-filter').val();
    var filteredData = currentFilter ?
        currentData.filter(item => item.marka === currentFilter) :
        currentData;

    // If "All" is selected (empty filter), show summary table
    if (!currentFilter) {
        renderSummaryTable(filteredData);
        // Hide edit buttons, show message
        $('.kar-oranlari-actions .btn-reset, .kar-oranlari-actions .btn-save').hide();
        $('.kar-oranlari-actions').append('<span class="edit-message" style="color: #999; font-size: 13px; font-style: italic;">Düzenleme için lütfen marka seçiniz</span>');
    } else {
        renderCards(filteredData);
        // Show edit buttons, hide message
        $('.kar-oranlari-actions .btn-reset, .kar-oranlari-actions .btn-save').show();
        $('.kar-oranlari-actions .edit-message').remove();
    }

    updateStats(filteredData.length, currentData.length);
}

function selectMarkaFromTable(marka) {
    // Set the marka filter dropdown
    $('#marka-filter').val(marka);
    // Trigger filter
    filterByMarka();
}

function updateStats(shown, total) {
    var modCount = modifiedCards.size;
    var statsHtml = shown + ' / ' + total + ' kayıt';
    if (modCount > 0) {
        statsHtml += ' (' + modCount + ' değişiklik)';
    }
    $('#card-stats').html(statsHtml);
}

function renderSummaryTable(data) {
    var container = $('#kar-cards-container');
    container.empty();
    container.removeClass('kar-cards-grid');

    if (!data || data.length === 0) {
        container.html('<div class="no-data">Veri bulunamadı</div>');
        updateStats(0, currentData.length);
        return;
    }

    var tableHtml = `
        <div class="kar-summary-table">
            <table>
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Marka</th>
                        <th class="text-center">Seviye</th>
                        <th>Bayi CH</th>
                        <th>Bayi Ünvanı</th>
                        <th class="text-right">Onay 1 (%)</th>
                        <th>Onay 1 Mail</th>
                        <th class="text-right">Onay 2 (%)</th>
                        <th>Onay 2 Mail</th>
                        <th class="text-center" style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach(function(item) {
        var onay1 = item.onay1_oran ? Math.round(item.onay1_oran) : 0;
        var onay2 = item.onay2_oran ? Math.round(item.onay2_oran) : 0;

        tableHtml += `
            <tr>
                <td class="text-center id-col">${item.id}</td>
                <td class="marka-col" onclick="selectMarkaFromTable('${item.marka}')" title="Bu markayı düzenlemek için tıklayın">${item.marka || '-'}</td>
                <td class="text-center">${item.seviye || '-'}</td>
                <td>${item.bayi_ch_kodu || '-'}</td>
                <td>${item.bayi_unvani || '-'}</td>
                <td class="text-right">${onay1}</td>
                <td>${item.onay1_mail || '-'}</td>
                <td class="text-right">${onay2}</td>
                <td>${item.onay2_mail || '-'}</td>
                <td class="text-center">
                    <button class="btn-delete-row" onclick="deleteCard(${item.id})" title="Bu kaydı sil">&times;</button>
                </td>
            </tr>
        `;
    });

    tableHtml += `
                </tbody>
            </table>
        </div>
    `;

    container.html(tableHtml);
    updateStats(data.length, currentData.length);
}

function renderCards(data) {
    var container = $('#kar-cards-container');
    container.empty();
    container.addClass('kar-cards-grid');

    if (!data || data.length === 0) {
        container.html('<div class="no-data">Veri bulunamadı</div>');
        updateStats(0, currentData.length);
        return;
    }

    data.forEach(function(item) {
        var card = createCard(item);
        container.append(card);
    });

    updateStats(data.length, currentData.length);
}

function createCard(item) {
    var isModified = modifiedCards.has(item.id);
    var modifiedClass = isModified ? 'modified' : '';

    var displayInfo = '';
    if (item.bayi_unvani) {
        displayInfo = item.bayi_unvani;
    } else if (item.bayi_ch_kodu) {
        displayInfo = item.bayi_ch_kodu;
    } else if (item.seviye) {
        displayInfo = 'Seviye ' + item.seviye;
    } else {
        displayInfo = 'Genel';
    }

    var card = $('<div>', {
        class: 'kar-card ' + modifiedClass,
        'data-id': item.id,
        'data-marka': item.marka
    });

    var cardHtml = `
        <div class="kar-card-header">
            <div>
                <div class="kar-card-title">${item.marka || '-'}</div>
                <div class="kar-card-subtitle">${displayInfo}</div>
            </div>
            <div class="kar-card-actions">
                <button class="btn-delete-card" onclick="deleteCard(${item.id})" title="Sil">Sil</button>
                <div class="kar-card-id">#${item.id}</div>
            </div>
        </div>
        <div class="kar-card-body">
            <div class="kar-info-row">
                <div class="kar-field-group" style="flex: 0 0 auto; width: 80px;">
                    <label class="kar-field-label">Seviye</label>
                    <select class="kar-field-input"
                            data-field="seviye"
                            onchange="updateField(${item.id}, 'seviye', this.value)">
                        <option value="">-</option>
                        <option value="1" ${item.seviye == 1 ? 'selected' : ''}>1</option>
                        <option value="2" ${item.seviye == 2 ? 'selected' : ''}>2</option>
                        <option value="3" ${item.seviye == 3 ? 'selected' : ''}>3</option>
                        <option value="4" ${item.seviye == 4 ? 'selected' : ''}>4</option>
                    </select>
                </div>
                <div class="kar-field-group" style="flex: 1;">
                    <label class="kar-field-label">Bayi CH</label>
                    <div class="bayi-input-group">
                        <input type="text"
                               class="kar-field-input"
                               data-field="bayi_ch_kodu"
                               value="${item.bayi_ch_kodu || ''}"
                               onchange="updateField(${item.id}, 'bayi_ch_kodu', this.value)">
                        <button class="btn-select-bayi" onclick="openBayiSelectionModal(${item.id})">Seç</button>
                    </div>
                </div>
            </div>

            <div class="kar-onay-section">
                <div class="kar-onay-title">1. Onay</div>
                <div class="kar-field-group">
                    <label class="kar-field-label">Oran (%)</label>
                    <input type="number"
                           class="kar-field-input"
                           data-field="onay1_oran"
                           value="${item.onay1_oran || 0}"
                           onchange="updateField(${item.id}, 'onay1_oran', this.value)">
                </div>
                <div class="kar-field-group">
                    <label class="kar-field-label">Kullanıcı</label>
                    <div class="bayi-input-group">
                        <input type="text"
                               class="kar-field-input"
                               data-field="onay1_mail"
                               value="${item.onay1_mail || ''}"
                               onchange="updateField(${item.id}, 'onay1_mail', this.value)">
                        <button class="btn-select-bayi" onclick="openUserSelectionModal(${item.id}, 'onay1_mail')">Seç</button>
                    </div>
                </div>
            </div>

            <div class="kar-onay-section">
                <div class="kar-onay-title">2. Onay</div>
                <div class="kar-field-group">
                    <label class="kar-field-label">Oran (%)</label>
                    <input type="number"
                           class="kar-field-input"
                           data-field="onay2_oran"
                           value="${item.onay2_oran || 0}"
                           onchange="updateField(${item.id}, 'onay2_oran', this.value)">
                </div>
                <div class="kar-field-group">
                    <label class="kar-field-label">Kullanıcı</label>
                    <div class="bayi-input-group">
                        <input type="text"
                               class="kar-field-input"
                               data-field="onay2_mail"
                               value="${item.onay2_mail || ''}"
                               onchange="updateField(${item.id}, 'onay2_mail', this.value)">
                        <button class="btn-select-bayi" onclick="openUserSelectionModal(${item.id}, 'onay2_mail')">Seç</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    card.html(cardHtml);
    return card;
}

function updateField(id, field, value) {
    console.log('updateField called:', {id, field, value, idType: typeof id});

    // Find item in currentData (working copy that gets modified)
    // Use == instead of === to handle string/number mismatch
    var item = currentData.find(function(i) { return i.id == id; });
    console.log('Item found in currentData:', item);
    if (!item) {
        console.log('Item not found! Searching in all currentData ids:', currentData.map(i => ({id: i.id, type: typeof i.id})));
        return;
    }

    // Get original item for comparison (never modified)
    var originalItem = originalData.find(function(i) { return i.id == id; });
    console.log('Original item found:', originalItem);

    // Update value
    if (field === 'onay1_oran' || field === 'onay2_oran') {
        item[field] = parseFloat(value) || 0;
    } else if (field === 'seviye') {
        item[field] = value === '' ? null : parseInt(value);
    } else if (field === 'bayi_ch_kodu') {
        item[field] = value === '' ? null : value;
    } else {
        item[field] = value;
    }

    // Check if modified - normalize null/empty comparisons
    var isModified = false;
    ['seviye', 'bayi_ch_kodu', 'onay1_oran', 'onay1_mail', 'onay2_oran', 'onay2_mail'].forEach(function(f) {
        var currentVal = item[f];
        var originalVal = originalItem[f];

        // Normalize empty values for comparison
        if (f === 'seviye' || f === 'bayi_ch_kodu') {
            currentVal = currentVal === '' || currentVal === null ? null : currentVal;
            originalVal = originalVal === '' || originalVal === null ? null : originalVal;
        }

        if (currentVal != originalVal) {
            isModified = true;
            console.log('Field modified:', f, 'Current:', currentVal, 'Original:', originalVal);
        }
    });

    var $card = $('.kar-card[data-id="' + id + '"]');

    if (isModified) {
        modifiedCards.add(id);
        $card.addClass('modified');
        // Mark all modified fields
        ['seviye', 'bayi_ch_kodu', 'onay1_oran', 'onay1_mail', 'onay2_oran', 'onay2_mail'].forEach(function(f) {
            var cv = item[f];
            var ov = originalItem[f];
            if (f === 'seviye' || f === 'bayi_ch_kodu') {
                cv = cv === '' || cv === null ? null : cv;
                ov = ov === '' || ov === null ? null : ov;
            }
            if (cv != ov) {
                $card.find('[data-field="' + f + '"]').addClass('modified');
            } else {
                $card.find('[data-field="' + f + '"]').removeClass('modified');
            }
        });
    } else {
        modifiedCards.delete(id);
        $card.removeClass('modified');
        $card.find('.kar-field-input').removeClass('modified');
    }

    // Update stats
    var filteredData = currentFilter ?
        currentData.filter(item => item.marka === currentFilter) :
        currentData;
    updateStats(filteredData.length, currentData.length);

    console.log('Modified cards:', Array.from(modifiedCards));
}

function resetChanges() {
    if (!confirm('Tüm değişiklikleri geri almak istediğinizden emin misiniz?')) {
        return;
    }

    modifiedCards.clear();
    currentData = JSON.parse(JSON.stringify(originalData));
    filterByMarka();
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
    var total = modifiedCards.size;

    modifiedCards.forEach(function(id) {
        var item = currentData.find(function(i) { return i.id == id; });
        if (!item) {
            console.log('SaveAllChanges: Item not found for id:', id, 'type:', typeof id);
            return;
        }
        console.log('SaveAllChanges: Saving item:', item);

        $.ajax({
            url: '_service/update_onaylar_kar.php',
            method: 'POST',
            data: {
                id: item.id,
                marka: item.marka,
                seviye: item.seviye || null,
                bayi_ch_kodu: item.bayi_ch_kodu || null,
                onay1_oran: item.onay1_oran || 0,
                onay1_mail: item.onay1_mail || '',
                onay2_oran: item.onay2_oran || 0,
                onay2_mail: item.onay2_mail || ''
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

        // Update originalData to match currentData (now these are the new baseline)
        originalData = JSON.parse(JSON.stringify(currentData));

        // Clear modified cards
        modifiedCards.clear();

        // Re-render current view to remove modified highlights
        filterByMarka();
    } else {
        alert('Bazı kayıtlar güncellenemedi. Başarılı: ' + successCount + ', Hatalı: ' + errorCount);
    }
}

function deleteCard(id) {
    if (!confirm('Bu kaydı silmek istediğinizden emin misiniz?')) {
        return;
    }

    $.ajax({
        url: '_service/delete_onaylar_kar.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Kayıt başarıyla silindi!');

                // Remove from both arrays
                originalData = originalData.filter(function(i) { return i.id != id; });
                currentData = currentData.filter(function(i) { return i.id != id; });

                // Remove from modified cards if exists
                modifiedCards.delete(id);

                // Re-render
                filterByMarka();
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
    // Populate marka dropdown
    populateNewMarkaDropdown();

    // Clear form
    $('#new-marka').val('');
    $('#new-seviye').val('');
    $('#new-bayi-ch').val('');
    $('#new-onay1-oran').val(0);
    $('#new-onay1-mail').val('');
    $('#new-onay2-oran').val(0);
    $('#new-onay2-mail').val('');

    $('#new-record-modal').addClass('active');
}

function closeNewRecordModal() {
    $('#new-record-modal').removeClass('active');
}

function saveNewRecord() {
    var marka = $('#new-marka').val();
    var seviye = $('#new-seviye').val();
    var bayi_ch = $('#new-bayi-ch').val().trim();
    var onay1_oran = $('#new-onay1-oran').val();
    var onay1_mail = $('#new-onay1-mail').val().trim();
    var onay2_oran = $('#new-onay2-oran').val();
    var onay2_mail = $('#new-onay2-mail').val().trim();

    if (!marka || marka === '') {
        alert('Marka seçimi zorunludur!');
        return;
    }

    $.ajax({
        url: '_service/insert_onaylar_kar.php',
        method: 'POST',
        data: {
            marka: marka,
            seviye: seviye || null,
            bayi_ch_kodu: bayi_ch || null,
            onay1_oran: onay1_oran || 0,
            onay1_mail: onay1_mail || '',
            onay2_oran: onay2_oran || 0,
            onay2_mail: onay2_mail || ''
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

var bayilerData = [];
var currentBayiCardId = null;
var usersData = [];
var currentUserCardId = null;
var currentUserField = null;

function openBayiSelectionModal(cardId) {
    currentBayiCardId = cardId;
    $('#bayi-selection-modal').addClass('active');

    if (bayilerData.length === 0) {
        loadBayilerData();
    } else {
        renderBayiList(bayilerData);
    }
}

function openUserSelectionModal(cardId, fieldName) {
    currentUserCardId = cardId;
    currentUserField = fieldName;
    $('#user-selection-modal').addClass('active');

    if (usersData.length === 0) {
        loadUsersData();
    } else {
        renderUserList(usersData);
    }
}

function closeBayiSelectionModal() {
    $('#bayi-selection-modal').removeClass('active');
    $('#bayi-search-input').val('');
    currentBayiCardId = null;
}

function closeUserSelectionModal() {
    $('#user-selection-modal').removeClass('active');
    $('#user-search-input').val('');
    currentUserCardId = null;
    currentUserField = null;
}

function loadBayilerData() {
    $.ajax({
        url: '_tablolar/kt_bayiler.php?dbname=LKS',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response && response.data) {
                bayilerData = response.data;
                renderBayiList(bayilerData);
            } else {
                $('#bayi-list').html('<div class="no-data">Bayi bulunamadı</div>');
            }
        },
        error: function(xhr, status, error) {
            $('#bayi-list').html('<div class="no-data">Bayiler yüklenirken hata oluştu: ' + error + '</div>');
        }
    });
}

function renderBayiList(bayiler) {
    var listHtml = '';

    if (!bayiler || bayiler.length === 0) {
        listHtml = '<div class="no-data">Bayi bulunamadı</div>';
    } else {
        bayiler.forEach(function(bayi) {
            listHtml += `
                <div class="bayi-item" onclick="selectBayi('${bayi.CH_KODU}', '${bayi.CH_UNVANI.replace(/'/g, "\\'")}')">
                    <div class="bayi-item-name">${bayi.CH_UNVANI}</div>
                    <div class="bayi-item-code">Kod: ${bayi.CH_KODU}</div>
                </div>
            `;
        });
    }

    $('#bayi-list').html(listHtml);
}

function filterBayiList() {
    var searchTerm = $('#bayi-search-input').val().toLowerCase();

    if (!searchTerm) {
        renderBayiList(bayilerData);
        return;
    }

    var filtered = bayilerData.filter(function(bayi) {
        var unvani = (bayi.CH_UNVANI || '').toLowerCase();
        var kod = (bayi.CH_KODU || '').toLowerCase();
        return unvani.indexOf(searchTerm) >= 0 || kod.indexOf(searchTerm) >= 0;
    });

    renderBayiList(filtered);
}

function selectBayi(chKodu, chUnvani) {
    console.log('selectBayi called:', {chKodu, chUnvani, currentBayiCardId});

    if (currentBayiCardId === 'new') {
        // New record modal
        $('#new-bayi-ch').val(chKodu);
    } else {
        // Existing card
        var $card = $('.kar-card[data-id="' + currentBayiCardId + '"]');
        console.log('Card found:', $card.length);
        var $input = $card.find('[data-field="bayi_ch_kodu"]');
        console.log('Input found:', $input.length, 'Old value:', $input.val());
        $input.val(chKodu);
        console.log('New value set:', $input.val());
        console.log('Calling updateField with:', currentBayiCardId, 'bayi_ch_kodu', chKodu);
        updateField(currentBayiCardId, 'bayi_ch_kodu', chKodu);
    }

    closeBayiSelectionModal();
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
                <div class="bayi-item" onclick="selectUser('${user.display_name.replace(/'/g, "\\'")}', '${user.user_email}')">
                    <div class="bayi-item-name">${user.display_name}</div>
                    <div class="bayi-item-code">${user.user_email}</div>
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

function selectUser(displayName, email) {
    if (currentUserCardId === 'new') {
        // New record modal
        if (currentUserField === 'onay1_mail') {
            $('#new-onay1-mail').val(displayName);
        } else if (currentUserField === 'onay2_mail') {
            $('#new-onay2-mail').val(displayName);
        }
    } else {
        // Existing card
        var $card = $('.kar-card[data-id="' + currentUserCardId + '"]');
        var $input = $card.find('[data-field="' + currentUserField + '"]');
        $input.val(displayName);
        updateField(currentUserCardId, currentUserField, displayName);
    }

    closeUserSelectionModal();
}

// Close modal on outside click
$(document).on('click', '.modal-overlay', function(e) {
    if ($(e.target).hasClass('modal-overlay')) {
        closeNewRecordModal();
        closeBayiSelectionModal();
        closeUserSelectionModal();
    }
});

$(function() {
    loadData();
});
</script>

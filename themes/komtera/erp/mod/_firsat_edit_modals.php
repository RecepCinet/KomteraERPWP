<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    color: #333;
}

.modal-close {
    background: none;
    border: none;
    font-size: 32px;
    color: #999;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 32px;
    height: 32px;
}

.modal-close:hover {
    color: #333;
}

.modal-search {
    padding: 15px 20px;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.search-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-container input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-container input:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.1);
}

.search-options {
    display: flex;
    gap: 5px;
}

.search-option {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.search-option:hover {
    background: #f0f0f0;
}

.search-option.active {
    background: #007cba;
    color: white;
    border-color: #007cba;
}

.yetkili-add-form {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.form-row {
    display: flex;
    gap: 10px;
}

.yetkili-input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.yetkili-input:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.1);
}

.modal-list {
    padding: 20px;
    overflow-y: auto;
    flex: 1;
    max-height: 500px;
}

.yetkili-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.yetkili-table th,
.yetkili-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.yetkili-table th {
    background-color: #f5f5f5;
    font-weight: bold;
    color: #333;
}

.yetkili-table tr:hover {
    background-color: #f9f9f9;
}

.yetkili-table tr.yetkili-delete-row {
    background-color: #f8d7da !important;
}

.yetkili-name-cell {
    cursor: pointer;
    color: #007cba;
    font-weight: 500;
}

.yetkili-name-cell:hover {
    text-decoration: underline;
}

.yetkili-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #007cba;
    color: white;
}

.btn-primary:hover {
    background: #005a87;
}

.btn-small {
    padding: 4px 8px;
    font-size: 12px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-edit {
    background: #007cba;
    color: white;
}

.btn-edit:hover {
    background: #005a87;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.yetkili-edit-input {
    width: 100%;
    padding: 6px;
    border: 1px solid #007cba;
    border-radius: 3px;
    font-size: 13px;
}

.musteri-item {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background 0.2s;
}

.musteri-item:hover {
    background: #f0f0f0;
}

.musteri-item:active {
    background: #e0e0e0;
}

.etkinlik-item {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
    cursor: pointer;
    transition: background 0.2s;
}

.etkinlik-item:hover {
    background: #f0f0f0;
}

.etkinlik-item:active {
    background: #e0e0e0;
}
</style>

<script>
const FIRSAT_NO = '<?php echo htmlspecialchars($firsat_no ?? ''); ?>';
const BAYI_CHKODU = '<?php echo htmlspecialchars($firsat_data['BAYI_CHKODU'] ?? ''); ?>';
let MUSTERI_ID = ''; // Müşteri seçildiğinde güncellenecek

// ============== BAYI YETKILI MODAL ==============
function editBayiYetkili(event) {
    event.preventDefault();

    if (!BAYI_CHKODU) {
        alert('<?php echo __('Önce bayi seçilmeli', 'komtera'); ?>');
        return;
    }

    const modalHtml = `
        <div id="bayi-yetkili-modal" class="modal-overlay" onclick="closeBayiYetkiliModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3><?php echo __('Bayi Yetkili Seçimi', 'komtera'); ?></h3>
                    <button class="modal-close" onclick="closeBayiYetkiliModal()">&times;</button>
                </div>
                <div class="yetkili-add-form">
                    <div class="form-row">
                        <input type="text" id="new-bayi-yetkili-name" placeholder="<?php echo __('Yetkili Adı', 'komtera'); ?>" class="yetkili-input">
                        <input type="text" id="new-bayi-yetkili-phone" placeholder="<?php echo __('Telefon', 'komtera'); ?>" class="yetkili-input">
                        <input type="email" id="new-bayi-yetkili-email" placeholder="<?php echo __('E-posta', 'komtera'); ?>" class="yetkili-input">
                        <button class="btn btn-primary" onclick="saveBayiYetkili()"><?php echo __('Ekle', 'komtera'); ?></button>
                    </div>
                </div>
                <div class="modal-list" id="bayi-yetkili-list">
                    <div style="text-align: center; padding: 20px;"><?php echo __('Yükleniyor...', 'komtera'); ?></div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadBayiYetkililer(BAYI_CHKODU);
}

function closeBayiYetkiliModal(event) {
    if (event && event.target.id !== 'bayi-yetkili-modal') return;
    const modal = document.getElementById('bayi-yetkili-modal');
    if (modal) {
        modal.remove();
    }
}

function loadBayiYetkililer(bayiKodu) {
    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=get_bayi_yetkililer&bayi_kodu=' + encodeURIComponent(bayiKodu))
        .then(r => r.json())
        .then(response => {
            console.log('Bayi yetkililer response:', response);

            if (response && response.hasOwnProperty('data')) {
                displayBayiYetkililer(response.data);
            } else if (Array.isArray(response)) {
                displayBayiYetkililer(response);
            } else {
                console.error('Unexpected response format:', response);
                document.getElementById('bayi-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Beklenmeyen veri formatı.', 'komtera'); ?></div>';
            }
        })
        .catch(error => {
            console.error('Bayi yetkililer fetch error:', error);
            document.getElementById('bayi-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Yetkili listesi yüklenirken hata oluştu', 'komtera'); ?>: ' + error.message + '</div>';
        });
}

// Bayi yetkililer için geçici data store
let bayiYetkililerData = [];

function displayBayiYetkililer(data) {
    const listDiv = document.getElementById('bayi-yetkili-list');

    if (!Array.isArray(data)) {
        console.error('Bayi yetkili data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Veri formatı hatalı.', 'komtera'); ?></div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;"><?php echo __('Yetkili bulunamadı.', 'komtera'); ?></div>';
        return;
    }

    // Veriyi store'a kaydet
    bayiYetkililerData = data;

    let html = '<table class="yetkili-table"><thead><tr><th><?php echo __('Yetkili', 'komtera'); ?></th><th><?php echo __('Telefon', 'komtera'); ?></th><th><?php echo __('E-posta', 'komtera'); ?></th><th><?php echo __('İşlemler', 'komtera'); ?></th></tr></thead><tbody>';
    data.forEach(function(yetkili, index) {
        html += `
            <tr id="bayi-yetkili-row-${yetkili.id}" data-yetkili-index="${index}">
                <td onclick="selectBayiYetkiliFromRow(this)" style="cursor: pointer;" class="yetkili-name-cell">${yetkili.yetkili || 'N/A'}</td>
                <td class="yetkili-phone-cell">${yetkili.telefon || ''}</td>
                <td class="yetkili-email-cell">${yetkili.eposta || ''}</td>
                <td>
                    <div class="yetkili-buttons">
                        <button class="btn-small btn-edit" onclick="editBayiYetkiliRowFromBtn(this)"><?php echo __('Düzenle', 'komtera'); ?></button>
                        <button class="btn-small btn-delete" onclick="deleteBayiYetkiliFromBtn(this)"><?php echo __('Sil', 'komtera'); ?></button>
                    </div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';

    listDiv.innerHTML = html;
}

// Data attribute'lardan veri çeken yardımcı fonksiyonlar
function selectBayiYetkiliFromRow(td) {
    const row = td.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = bayiYetkililerData[index];
    if (yetkili) {
        selectBayiYetkili(yetkili.id, yetkili.yetkili || '', yetkili.telefon || '', yetkili.eposta || '');
    }
}

function editBayiYetkiliRowFromBtn(btn) {
    const row = btn.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = bayiYetkililerData[index];
    if (yetkili) {
        editBayiYetkiliRow(yetkili.id, yetkili.yetkili || '', yetkili.telefon || '', yetkili.eposta || '');
    }
}

function deleteBayiYetkiliFromBtn(btn) {
    const row = btn.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = bayiYetkililerData[index];
    if (yetkili) {
        deleteBayiYetkili(yetkili.id);
    }
}

function selectBayiYetkili(yetkiliId, yetkiliAdi, yetkiliTelefon, yetkiliEposta) {
    const formData = new FormData();
    formData.append('firsat_no', FIRSAT_NO);
    formData.append('field', 'bayi_yetkili');
    formData.append('yetkili_isim', yetkiliAdi);
    formData.append('yetkili_tel', yetkiliTelefon);
    formData.append('yetkili_eposta', yetkiliEposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/update_firsat_field.php', {
        method: 'POST',
        body: formData
    })
    .then(async r => {
        const text = await r.text();
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Response:', text);
            throw new Error('Sunucudan geçersiz yanıt: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + err.message);
    });
}

function saveBayiYetkili() {
    const yetkili = document.getElementById('new-bayi-yetkili-name').value.trim();
    const telefon = document.getElementById('new-bayi-yetkili-phone').value.trim();
    const eposta = document.getElementById('new-bayi-yetkili-email').value.trim();

    if (!yetkili) {
        alert('<?php echo __('Yetkili adı gereklidir.', 'komtera'); ?>');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'save_bayi_yetkili');
    formData.append('bayi_kodu', BAYI_CHKODU);
    formData.append('yetkili', yetkili);
    formData.append('telefon', telefon);
    formData.append('eposta', eposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=save_bayi_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            document.getElementById('new-bayi-yetkili-name').value = '';
            document.getElementById('new-bayi-yetkili-phone').value = '';
            document.getElementById('new-bayi-yetkili-email').value = '';
            loadBayiYetkililer(BAYI_CHKODU);
            alert('<?php echo __('Yetkili başarıyla eklendi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + response.error);
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function editBayiYetkiliRow(yetkiliId, currentName, currentPhone, currentEmail) {
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);
    const index = parseInt(row.dataset.yetkiliIndex);

    // HTML için escape
    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    row.innerHTML = `
        <td><input type="text" id="edit-bayi-yetkili-name-${yetkiliId}" value="${escapeHtml(currentName)}" class="yetkili-edit-input"></td>
        <td><input type="text" id="edit-bayi-yetkili-phone-${yetkiliId}" value="${escapeHtml(currentPhone)}" class="yetkili-edit-input"></td>
        <td><input type="email" id="edit-bayi-yetkili-email-${yetkiliId}" value="${escapeHtml(currentEmail)}" class="yetkili-edit-input"></td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="updateBayiYetkili(${yetkiliId})"><?php echo __('Kaydet', 'komtera'); ?></button>
                <button class="btn-small btn-delete" onclick="cancelEditBayiYetkili(${yetkiliId})"><?php echo __('İptal', 'komtera'); ?></button>
            </div>
        </td>
    `;
}

function updateBayiYetkili(yetkiliId) {
    const yetkili = document.getElementById(`edit-bayi-yetkili-name-${yetkiliId}`).value.trim();
    const telefon = document.getElementById(`edit-bayi-yetkili-phone-${yetkiliId}`).value.trim();
    const eposta = document.getElementById(`edit-bayi-yetkili-email-${yetkiliId}`).value.trim();

    if (!yetkili) {
        alert('<?php echo __('Yetkili adı gereklidir.', 'komtera'); ?>');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update_bayi_yetkili');
    formData.append('id', yetkiliId);
    formData.append('yetkili', yetkili);
    formData.append('telefon', telefon);
    formData.append('eposta', eposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=update_bayi_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        console.log('Update bayi yetkili response:', response);
        if (response.success) {
            loadBayiYetkililer(BAYI_CHKODU);
            alert('<?php echo __('Yetkili başarıyla güncellendi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + (response.error || response.message || '<?php echo __('Bilinmeyen hata', 'komtera'); ?>'));
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        alert('<?php echo __('Güncelleme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function cancelEditBayiYetkili(yetkiliId) {
    loadBayiYetkililer(BAYI_CHKODU);
}

function deleteBayiYetkili(yetkiliId) {
    const row = document.getElementById(`bayi-yetkili-row-${yetkiliId}`);
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = bayiYetkililerData[index];

    if (!yetkili) return;

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    row.className = 'yetkili-delete-row';
    row.innerHTML = `
        <td colspan="3" style="text-align: center; font-weight: bold; color: #721c24;">
            <?php echo __('Bu yetkiliyi silmek istediğinizden emin misiniz', 'komtera'); ?>: "${escapeHtml(yetkili.yetkili || '')}"?
        </td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-delete" onclick="confirmDeleteBayiYetkili(${yetkiliId})"><?php echo __('Sil', 'komtera'); ?></button>
                <button class="btn-small btn-edit" onclick="cancelDeleteBayiYetkili(${yetkiliId})"><?php echo __('Vazgeç', 'komtera'); ?></button>
            </div>
        </td>
    `;
}

function confirmDeleteBayiYetkili(yetkiliId) {
    const formData = new FormData();
    formData.append('action', 'delete_bayi_yetkili');
    formData.append('id', yetkiliId);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=delete_bayi_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            loadBayiYetkililer(BAYI_CHKODU);
            alert('<?php echo __('Yetkili başarıyla silindi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + response.error);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('<?php echo __('Silme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function cancelDeleteBayiYetkili(yetkiliId) {
    loadBayiYetkililer(BAYI_CHKODU);
}

// ============== MÜŞTERI MODAL ==============
function editMusteri(event) {
    event.preventDefault();

    const modalHtml = `
        <div id="musteri-modal" class="modal-overlay" onclick="closeMusteriModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3><?php echo __('Müşteri Seçimi (100 Kayıt)', 'komtera'); ?></h3>
                    <button class="modal-close" onclick="closeMusteriModal()">&times;</button>
                </div>
                <div class="modal-search">
                    <div class="search-container">
                        <input type="text" id="musteri-search" placeholder="<?php echo __('Müşteri adı ile ara...', 'komtera'); ?>" onkeyup="searchMusteriler(this.value)">
                        <div class="search-options">
                            <button class="search-option active" data-mode="startswith" title="<?php echo __('İle Başlıyor', 'komtera'); ?>">
                                <span class="dashicons dashicons-editor-alignleft"></span>
                            </button>
                            <button class="search-option" data-mode="contains" title="<?php echo __('İçeriyor', 'komtera'); ?>">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-list" id="musteri-list">
                    <div style="text-align: center; padding: 20px;"><?php echo __('Yükleniyor...', 'komtera'); ?></div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Search option event listeners
    document.querySelectorAll('#musteri-modal .search-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#musteri-modal .search-option').forEach(function(b) {
                b.classList.remove('active');
            });

            this.classList.add('active');

            const searchValue = document.getElementById('musteri-search').value;
            if (searchValue.length >= 2) {
                searchMusteriler(searchValue);
            } else {
                loadAllMusteriler();
            }
        });
    });

    loadAllMusteriler();

    setTimeout(function() {
        const searchInput = document.getElementById('musteri-search');
        if (searchInput) {
            searchInput.focus();
        }
    }, 100);
}

function closeMusteriModal(event) {
    if (event && event.target.id !== 'musteri-modal') return;
    const modal = document.getElementById('musteri-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAllMusteriler() {
    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=get_musteriler')
        .then(r => r.json())
        .then(data => {
            displayMusteriler(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('musteri-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Müşteri listesi yüklenirken hata oluştu.', 'komtera'); ?></div>';
        });
}

function searchMusteriler(query) {
    if (query.length < 2) {
        loadAllMusteriler();
        return;
    }

    const activeOption = document.querySelector('#musteri-modal .search-option.active');
    const searchMode = activeOption ? activeOption.getAttribute('data-mode') : 'startswith';

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=search_musteriler&query=' + encodeURIComponent(query) + '&mode=' + searchMode)
        .then(r => r.json())
        .then(data => {
            displayMusteriler(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('musteri-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Arama sırasında hata oluştu.', 'komtera'); ?></div>';
        });
}

// Müşteriler için geçici data store
let musterilerData = [];

function displayMusteriler(data) {
    const listDiv = document.getElementById('musteri-list');

    if (!Array.isArray(data) || data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;"><?php echo __('Müşteri bulunamadı.', 'komtera'); ?></div>';
        return;
    }

    // Veriyi store'a kaydet
    musterilerData = data;

    let html = '';
    data.forEach(function(musteri, index) {
        html += `
            <div class="musteri-item" onclick="selectMusteriByIndex(${index})">
                ${musteri.musteri}
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

function selectMusteriByIndex(index) {
    const musteri = musterilerData[index];
    if (musteri) {
        selectMusteri(musteri.id, musteri.musteri || '');
    }
}

function selectMusteri(musteriId, musteriAdi) {
    // Müşteri ID'yi global değişkene kaydet
    MUSTERI_ID = musteriId;

    const formData = new FormData();
    formData.append('firsat_no', FIRSAT_NO);
    formData.append('field', 'musteri');
    formData.append('musteri_adi', musteriAdi);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/update_firsat_field.php', {
        method: 'POST',
        body: formData
    })
    .then(async r => {
        const text = await r.text();
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Response:', text);
            throw new Error('Sunucudan geçersiz yanıt: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + err.message);
    });
}

// ============== MÜŞTERI YETKILI MODAL ==============
function editMusteriYetkili(event) {
    event.preventDefault();

    // Müşteri adını PHP'den alalım
    const musteriAdi = '<?php echo htmlspecialchars($firsat_data['MUSTERI_ADI'] ?? ''); ?>';

    if (!musteriAdi) {
        alert('<?php echo __('Önce bir müşteri seçiniz.', 'komtera'); ?>');
        return;
    }

    // Müşteri adından ID'yi çek
    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=get_musteri_id&musteri_adi=' + encodeURIComponent(musteriAdi))
        .then(r => r.json())
        .then(data => {
            if (data.success && data.musteri_id) {
                MUSTERI_ID = data.musteri_id;
                openMusteriYetkiliModalWithId(data.musteri_id);
            } else {
                alert('<?php echo __('Müşteri ID bulunamadı.', 'komtera'); ?>');
            }
        })
        .catch(error => {
            console.error('Müşteri ID çekme hatası:', error);
            alert('<?php echo __('Müşteri ID çekilemedi.', 'komtera'); ?>');
        });
}

function openMusteriYetkiliModalWithId(musteriId) {
    if (!musteriId) {
        alert('<?php echo __('Önce bir müşteri seçiniz.', 'komtera'); ?>');
        return;
    }

    // Global MUSTERI_ID'yi set et
    MUSTERI_ID = musteriId;

    const modalHtml = `
        <div id="musteri-yetkili-modal" class="modal-overlay" onclick="closeMusteriYetkiliModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3><?php echo __('Müşteri Yetkili Seçimi', 'komtera'); ?></h3>
                    <button class="modal-close" onclick="closeMusteriYetkiliModal()">&times;</button>
                </div>
                <div class="yetkili-add-form">
                    <div class="form-row">
                        <input type="text" id="new-musteri-yetkili-name" placeholder="<?php echo __('Yetkili Adı', 'komtera'); ?>" class="yetkili-input">
                        <input type="text" id="new-musteri-yetkili-phone" placeholder="<?php echo __('Telefon', 'komtera'); ?>" class="yetkili-input">
                        <input type="email" id="new-musteri-yetkili-email" placeholder="<?php echo __('E-posta', 'komtera'); ?>" class="yetkili-input">
                        <button class="btn btn-primary" onclick="saveMusteriYetkili()"><?php echo __('Ekle', 'komtera'); ?></button>
                    </div>
                </div>
                <div class="modal-list" id="musteri-yetkili-list">
                    <div style="text-align: center; padding: 20px;"><?php echo __('Yükleniyor...', 'komtera'); ?></div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadMusteriYetkililer(musteriId);
}

function closeMusteriYetkiliModal(event) {
    if (event && event.target.id !== 'musteri-yetkili-modal') return;
    const modal = document.getElementById('musteri-yetkili-modal');
    if (modal) {
        modal.remove();
    }
}

function loadMusteriYetkililer(musteriId) {
    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=get_musteri_yetkililer&musteri_id=' + encodeURIComponent(musteriId))
        .then(r => r.json())
        .then(response => {
            console.log('Musteri yetkililer response:', response);

            if (response && response.hasOwnProperty('data')) {
                displayMusteriYetkililer(response.data);
            } else if (Array.isArray(response)) {
                displayMusteriYetkililer(response);
            } else {
                console.error('Unexpected response format:', response);
                document.getElementById('musteri-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Beklenmeyen veri formatı.', 'komtera'); ?></div>';
            }
        })
        .catch(error => {
            console.error('Musteri yetkililer fetch error:', error);
            document.getElementById('musteri-yetkili-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Yetkili listesi yüklenirken hata oluştu', 'komtera'); ?>: ' + error.message + '</div>';
        });
}

// Müşteri yetkililer için geçici data store
let musteriYetkililerData = [];

function displayMusteriYetkililer(data) {
    const listDiv = document.getElementById('musteri-yetkili-list');

    if (!Array.isArray(data)) {
        console.error('Musteri yetkili data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Veri formatı hatalı.', 'komtera'); ?></div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;"><?php echo __('Yetkili bulunamadı.', 'komtera'); ?></div>';
        return;
    }

    // Veriyi store'a kaydet
    musteriYetkililerData = data;

    let html = '<table class="yetkili-table"><thead><tr><th><?php echo __('Yetkili', 'komtera'); ?></th><th><?php echo __('Telefon', 'komtera'); ?></th><th><?php echo __('E-posta', 'komtera'); ?></th><th><?php echo __('İşlemler', 'komtera'); ?></th></tr></thead><tbody>';
    data.forEach(function(yetkili, index) {
        html += `
            <tr id="musteri-yetkili-row-${yetkili.id}" data-yetkili-index="${index}">
                <td onclick="selectMusteriYetkiliFromRow(this)" style="cursor: pointer;" class="yetkili-name-cell">${yetkili.yetkili || 'N/A'}</td>
                <td class="yetkili-phone-cell">${yetkili.telefon || ''}</td>
                <td class="yetkili-email-cell">${yetkili.eposta || ''}</td>
                <td>
                    <div class="yetkili-buttons">
                        <button class="btn-small btn-edit" onclick="editMusteriYetkiliRowFromBtn(this)"><?php echo __('Düzenle', 'komtera'); ?></button>
                        <button class="btn-small btn-delete" onclick="deleteMusteriYetkiliFromBtn(this)"><?php echo __('Sil', 'komtera'); ?></button>
                    </div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';

    listDiv.innerHTML = html;
}

// Data attribute'lardan veri çeken yardımcı fonksiyonlar
function selectMusteriYetkiliFromRow(td) {
    const row = td.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = musteriYetkililerData[index];
    if (yetkili) {
        selectMusteriYetkili(yetkili.id, yetkili.yetkili || '', yetkili.telefon || '', yetkili.eposta || '');
    }
}

function editMusteriYetkiliRowFromBtn(btn) {
    const row = btn.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = musteriYetkililerData[index];
    if (yetkili) {
        editMusteriYetkiliRow(yetkili.id, yetkili.yetkili || '', yetkili.telefon || '', yetkili.eposta || '');
    }
}

function deleteMusteriYetkiliFromBtn(btn) {
    const row = btn.closest('tr');
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = musteriYetkililerData[index];
    if (yetkili) {
        deleteMusteriYetkili(yetkili.id);
    }
}

function selectMusteriYetkili(yetkiliId, yetkiliAdi, yetkiliTelefon, yetkiliEposta) {
    const formData = new FormData();
    formData.append('firsat_no', FIRSAT_NO);
    formData.append('field', 'musteri_yetkili');
    formData.append('yetkili_isim', yetkiliAdi);
    formData.append('yetkili_tel', yetkiliTelefon);
    formData.append('yetkili_eposta', yetkiliEposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/update_firsat_field.php', {
        method: 'POST',
        body: formData
    })
    .then(async r => {
        const text = await r.text();
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Response:', text);
            throw new Error('Sunucudan geçersiz yanıt: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + data.error);
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + err.message);
    });
}

function saveMusteriYetkili() {
    const yetkili = document.getElementById('new-musteri-yetkili-name').value.trim();
    const telefon = document.getElementById('new-musteri-yetkili-phone').value.trim();
    const eposta = document.getElementById('new-musteri-yetkili-email').value.trim();

    if (!yetkili) {
        alert('<?php echo __('Yetkili adı gereklidir.', 'komtera'); ?>');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'save_musteri_yetkili');
    formData.append('musteri_id', MUSTERI_ID);
    formData.append('yetkili', yetkili);
    formData.append('telefon', telefon);
    formData.append('eposta', eposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=save_musteri_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            document.getElementById('new-musteri-yetkili-name').value = '';
            document.getElementById('new-musteri-yetkili-phone').value = '';
            document.getElementById('new-musteri-yetkili-email').value = '';
            loadMusteriYetkililer(MUSTERI_ID);
            alert('<?php echo __('Yetkili başarıyla eklendi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + response.error);
        }
    })
    .catch(error => {
        console.error('Save error:', error);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function editMusteriYetkiliRow(yetkiliId, currentName, currentPhone, currentEmail) {
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);
    const index = parseInt(row.dataset.yetkiliIndex);

    // HTML için escape
    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    row.innerHTML = `
        <td><input type="text" id="edit-musteri-yetkili-name-${yetkiliId}" value="${escapeHtml(currentName)}" class="yetkili-edit-input"></td>
        <td><input type="text" id="edit-musteri-yetkili-phone-${yetkiliId}" value="${escapeHtml(currentPhone)}" class="yetkili-edit-input"></td>
        <td><input type="email" id="edit-musteri-yetkili-email-${yetkiliId}" value="${escapeHtml(currentEmail)}" class="yetkili-edit-input"></td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-edit" onclick="updateMusteriYetkili(${yetkiliId})"><?php echo __('Kaydet', 'komtera'); ?></button>
                <button class="btn-small btn-delete" onclick="cancelEditMusteriYetkili(${yetkiliId})"><?php echo __('İptal', 'komtera'); ?></button>
            </div>
        </td>
    `;
}

function updateMusteriYetkili(yetkiliId) {
    const yetkili = document.getElementById(`edit-musteri-yetkili-name-${yetkiliId}`).value.trim();
    const telefon = document.getElementById(`edit-musteri-yetkili-phone-${yetkiliId}`).value.trim();
    const eposta = document.getElementById(`edit-musteri-yetkili-email-${yetkiliId}`).value.trim();

    if (!yetkili) {
        alert('<?php echo __('Yetkili adı gereklidir.', 'komtera'); ?>');
        return;
    }

    const formData = new FormData();
    formData.append('action', 'update_musteri_yetkili');
    formData.append('id', yetkiliId);
    formData.append('yetkili', yetkili);
    formData.append('telefon', telefon);
    formData.append('eposta', eposta);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=update_musteri_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        console.log('Update musteri yetkili response:', response);
        if (response.success) {
            loadMusteriYetkililer(MUSTERI_ID);
            alert('<?php echo __('Yetkili başarıyla güncellendi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + (response.error || response.message || '<?php echo __('Bilinmeyen hata', 'komtera'); ?>'));
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        alert('<?php echo __('Güncelleme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function cancelEditMusteriYetkili(yetkiliId) {
    loadMusteriYetkililer(MUSTERI_ID);
}

function deleteMusteriYetkili(yetkiliId) {
    const row = document.getElementById(`musteri-yetkili-row-${yetkiliId}`);
    const index = parseInt(row.dataset.yetkiliIndex);
    const yetkili = musteriYetkililerData[index];

    if (!yetkili) return;

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    row.className = 'yetkili-delete-row';
    row.innerHTML = `
        <td colspan="3" style="text-align: center; font-weight: bold; color: #721c24;">
            <?php echo __('Bu yetkiliyi silmek istediğinizden emin misiniz', 'komtera'); ?>: "${escapeHtml(yetkili.yetkili || '')}"?
        </td>
        <td>
            <div class="yetkili-buttons">
                <button class="btn-small btn-delete" onclick="confirmDeleteMusteriYetkili(${yetkiliId})"><?php echo __('Sil', 'komtera'); ?></button>
                <button class="btn-small btn-edit" onclick="cancelDeleteMusteriYetkili(${yetkiliId})"><?php echo __('Vazgeç', 'komtera'); ?></button>
            </div>
        </td>
    `;
}

function confirmDeleteMusteriYetkili(yetkiliId) {
    const formData = new FormData();
    formData.append('action', 'delete_musteri_yetkili');
    formData.append('id', yetkiliId);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=delete_musteri_yetkili', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            loadMusteriYetkililer(MUSTERI_ID);
            alert('<?php echo __('Yetkili başarıyla silindi.', 'komtera'); ?>');
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + response.error);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('<?php echo __('Silme hatası', 'komtera'); ?>: ' + error.message);
    });
}

function cancelDeleteMusteriYetkili(yetkiliId) {
    loadMusteriYetkililer(MUSTERI_ID);
}

// ============== ETKİNLİK DÜZENLEME ==============
function editEtkinlik(event) {
    event.preventDefault();

    const selectedMarka = '<?php echo htmlspecialchars($firsat_data['MARKA'] ?? ''); ?>';

    if (!selectedMarka) {
        alert('<?php echo __('Marka bilgisi bulunamadı.', 'komtera'); ?>');
        return;
    }

    const modalHtml = `
        <div id="etkinlik-modal" class="modal-overlay" onclick="closeEtkinlikModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3><?php echo __('Etkinlik Seçimi', 'komtera'); ?> (${selectedMarka})</h3>
                    <button class="modal-close" onclick="closeEtkinlikModal()">&times;</button>
                </div>
                <div class="modal-list" id="etkinlik-list">
                    <div style="text-align: center; padding: 20px;"><?php echo __('Yükleniyor...', 'komtera'); ?></div>
                </div>
                <div class="modal-footer">
                    <div style="font-size: 12px; color: #666; text-align: center;">
                        * <?php echo __('Sadece aktif etkinlikler gösterilmektedir (tarih geçmemiş)', 'komtera'); ?>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadEtkinlikler(selectedMarka);
}

function closeEtkinlikModal(event) {
    if (event && event.target.id !== 'etkinlik-modal') return;
    const modal = document.getElementById('etkinlik-modal');
    if (modal) {
        modal.remove();
    }
}

function loadEtkinlikler(marka) {
    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/mod/yeni_firsat.php?action=get_etkinlikler&marka=' + encodeURIComponent(marka))
        .then(r => r.json())
        .then(response => {
            console.log('Etkinlik response:', response);
            if (response.success) {
                displayEtkinlikler(response.data);
            } else {
                document.getElementById('etkinlik-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Hata', 'komtera'); ?>: ' + response.message + '</div>';
            }
        })
        .catch(error => {
            console.error('Etkinlik AJAX Error:', error);
            document.getElementById('etkinlik-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Etkinlik listesi yüklenirken hata oluştu.', 'komtera'); ?></div>';
        });
}

function displayEtkinlikler(data) {
    const listDiv = document.getElementById('etkinlik-list');

    if (!Array.isArray(data)) {
        console.error('Etkinlik data is not an array:', data);
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: red;"><?php echo __('Veri formatı hatalı.', 'komtera'); ?></div>';
        return;
    }

    if (data.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;"><?php echo __('Bu marka için aktif etkinlik bulunamadı.', 'komtera'); ?></div>';
        return;
    }

    // Veriyi store'a kaydet
    etkinliklerData = data;

    let html = '';
    data.forEach(function(etkinlik, index) {
        const tarihBas = etkinlik.tarih_bas ? new Date(etkinlik.tarih_bas).toLocaleDateString('tr-TR') : '';
        const tarihBit = etkinlik.tarih_bit ? new Date(etkinlik.tarih_bit).toLocaleDateString('tr-TR') : '';
        const dateRange = tarihBas && tarihBit ? `(${tarihBas} - ${tarihBit})` : '';

        // baslik veya etkinlik alanını kullan
        const etkinlikAdi = etkinlik.baslik || etkinlik.etkinlik || 'N/A';

        html += `
            <div class="etkinlik-item" onclick="selectEtkinlikByIndex(${index})">
                <div style="font-weight: 500;">${etkinlikAdi}</div>
                ${dateRange ? `<div style="font-size: 12px; color: #666; margin-top: 4px;">${dateRange}</div>` : ''}
            </div>
        `;
    });

    listDiv.innerHTML = html;
}

// Etkinlik data store
let etkinliklerData = [];

function selectEtkinlikByIndex(index) {
    const etkinlik = etkinliklerData[index];
    if (etkinlik) {
        const etkinlikAdi = etkinlik.baslik || etkinlik.etkinlik || '';
        selectEtkinlik(etkinlikAdi);
    }
}

function selectEtkinlik(etkinlikAdi) {
    const formData = new FormData();
    formData.append('firsat_no', FIRSAT_NO);
    formData.append('field', 'etkinlik');
    formData.append('etkinlik', etkinlikAdi);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/update_firsat_field.php', {
        method: 'POST',
        body: formData
    })
    .then(async r => {
        const text = await r.text();
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Response:', text);
            throw new Error('Sunucudan geçersiz yanıt: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + (data.error || data.message || '<?php echo __('Bilinmeyen hata', 'komtera'); ?>'));
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + err.message);
    });
}

// ============== BİTİŞ TARİHİ DÜZENLEME ==============
function editBitisTarihi(event) {
    event.preventDefault();

    const currentDate = '<?php echo $firsat_data['BITIS_TARIHI'] ? (new DateTime($firsat_data['BITIS_TARIHI']))->format('Y-m-d') : ''; ?>';

    const modalHtml = `
        <div id="bitis-tarihi-modal" class="modal-overlay" onclick="closeBitisTarihiModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 400px;">
                <div class="modal-header">
                    <h3><?php echo __('Bitiş Tarihi Düzenle', 'komtera'); ?></h3>
                    <button class="modal-close" onclick="closeBitisTarihiModal()">&times;</button>
                </div>
                <div style="padding: 20px;">
                    <div class="field-group">
                        <label for="new-bitis-tarihi" style="display: block; margin-bottom: 8px; font-weight: 500;">
                            <?php echo __('Bitiş Tarihi', 'komtera'); ?>
                        </label>
                        <input type="date" id="new-bitis-tarihi" value="${currentDate}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button class="btn btn-primary" onclick="saveBitisTarihi()">
                            <?php echo __('Kaydet', 'komtera'); ?>
                        </button>
                        <button class="btn" onclick="closeBitisTarihiModal()"
                                style="background: #666; color: white;">
                            <?php echo __('İptal', 'komtera'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Focus on date input
    setTimeout(() => {
        const input = document.getElementById('new-bitis-tarihi');
        if (input) input.focus();
    }, 100);
}

function closeBitisTarihiModal(event) {
    if (event && event.target.id !== 'bitis-tarihi-modal') return;
    const modal = document.getElementById('bitis-tarihi-modal');
    if (modal) {
        modal.remove();
    }
}

function saveBitisTarihi() {
    const tarihi = document.getElementById('new-bitis-tarihi').value;

    if (!tarihi) {
        alert('<?php echo __('Lütfen bir tarih seçin.', 'komtera'); ?>');
        return;
    }

    const formData = new FormData();
    formData.append('firsat_no', FIRSAT_NO);
    formData.append('field', 'bitis_tarihi');
    formData.append('bitis_tarihi', tarihi);

    fetch('<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service/update_firsat_field.php', {
        method: 'POST',
        body: formData
    })
    .then(async r => {
        const text = await r.text();
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error. Response:', text);
            throw new Error('Sunucudan geçersiz yanıt: ' + text.substring(0, 100));
        }
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('<?php echo __('Hata', 'komtera'); ?>: ' + (data.error || data.message || '<?php echo __('Bilinmeyen hata', 'komtera'); ?>'));
        }
    })
    .catch(err => {
        console.error('Save error:', err);
        alert('<?php echo __('Kaydetme hatası', 'komtera'); ?>: ' + err.message);
    });
}
</script>

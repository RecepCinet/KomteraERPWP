<?php
// WordPress integration
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "wp-load.php bulunamadı.\n";
    echo "Başlangıç dizini: " . __DIR__ . "\n";
    exit;
}

// User authentication check
if (!is_user_logged_in()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttrequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Oturum süreniz dolmuş. Lütfen tekrar giriş yapın.']);
        exit;
    } else {
        wp_redirect(wp_login_url());
        exit;
    }
}

// Database connection
include dirname(__DIR__) . '/_conn.php';

// Get current user info
$current_user = wp_get_current_user();
$user = $current_user->user_login;

// Form submit işlemi
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'save_firsat') {
    header('Content-Type: application/json');
    try {
        $marka = $_POST['marka'] ?? '';
        $gelis_kanali = $_POST['gelis_kanali'] ?? '';
        $olasilik = $_POST['olasilik'] ?? '';
        $proje_adi = $_POST['proje_adi'] ?? '';
        $firsat_aciklama = $_POST['firsat_aciklama'] ?? '';
        $bayi = $_POST['bayi'] ?? '';
        $bayi_kodu = $_POST['bayi_kodu'] ?? '';
        $musteri = $_POST['musteri'] ?? '';
        $accman = $_POST['accman'] ?? '';
        $etkinlik = $_POST['etkinlik'] ?? '';
        $register = isset($_POST['register']) ? '1' : '0';
        $register_dr_no = $_POST['register_dr_no'] ?? '';

        // Fırsat numarası oluşturma - basit bir yöntem
        $firsat_no = 'F' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO aa_erp_kt_firsatlar (
            FIRSAT_NO,
            MARKA,
            GELIS_KANALI,
            OLASILIK,
            MUSTERI_TEMSILCISI,
            PROJE_ADI,
            FIRSAT_ACIKLAMA,
            BAYI_ADI,
            BAYI_KODU,
            MUSTERI_ADI,
            ETKINLIK,
            REGISTER,
            KAYIDI_ACAN,
            BASLANGIC_TARIHI,
            REVIZE_TARIHI,
            DURUM
        ) VALUES (
            :firsat_no,
            :marka,
            :gelis_kanali,
            :olasilik,
            :musteri_temsilcisi,
            :proje_adi,
            :firsat_aciklama,
            :bayi,
            :bayi_kodu,
            :musteri,
            :etkinlik,
            :register,
            :kayidi_acan,
            GETDATE(),
            GETDATE(),
            '0'
        )";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firsat_no', $firsat_no);
        $stmt->bindParam(':marka', $marka);
        $stmt->bindParam(':gelis_kanali', $gelis_kanali);
        $stmt->bindParam(':olasilik', $olasilik);
        $stmt->bindParam(':musteri_temsilcisi', $user);
        $stmt->bindParam(':proje_adi', $proje_adi);
        $stmt->bindParam(':firsat_aciklama', $firsat_aciklama);
        $stmt->bindParam(':bayi', $bayi);
        $stmt->bindParam(':bayi_kodu', $bayi_kodu);
        $stmt->bindParam(':musteri', $musteri);
        $stmt->bindParam(':etkinlik', $etkinlik);
        $stmt->bindParam(':register', $register);
        $stmt->bindParam(':kayidi_acan', $user);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Fırsat başarıyla oluşturuldu.', 'firsat_no' => $firsat_no]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu.']);
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        exit;
    }
}

// AJAX veri çekme işlemleri
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    try {
        if ($_GET['action'] === 'get_markalar') {
            $sql = "SELECT MARKA FROM aa_erp_kt_fiyat_listesi WHERE MARKA IS NOT NULL GROUP BY MARKA ORDER BY MARKA";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'get_bayiler') {
            $sql = "SELECT b.CH_KODU, b.CH_UNVANI FROM aaa_erp_kt_bayiler b ORDER BY b.CH_UNVANI";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'search_bayiler') {
            $query = $_GET['query'] ?? '';
            $sql = "SELECT b.CH_KODU, b.CH_UNVANI FROM aaa_erp_kt_bayiler b WHERE b.CH_UNVANI LIKE :query ORDER BY b.CH_UNVANI";
            $stmt = $conn->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->bindParam(':query', $searchTerm);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }

        if ($_GET['action'] === 'get_musteriler') {
            $sql = "SELECT m.musteri FROM aa_erp_kt_musteriler m WHERE musteri IS NOT NULL ORDER BY m.musteri";
            $stmt = $conn->query($sql);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>

<style>
.form-container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #007cba;
}

.form-header h2 {
    color: #007cba;
    margin: 0;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    align-items: start;
}

.form-group {
    flex: 1;
}

.form-group.full-width {
    flex: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007cba;
    box-shadow: 0 0 5px rgba(0,124,186,0.3);
}

.radio-group {
    display: flex;
    gap: 15px;
    margin-top: 5px;
}

.radio-group label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: normal;
    cursor: pointer;
}

.radio-group input[type="radio"] {
    width: auto;
    margin: 0;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 5px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.checkbox-group label {
    font-weight: normal;
    margin: 0;
    cursor: pointer;
}

.btn-container {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 12px 30px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    margin: 0 10px;
}

.btn-primary {
    background-color: #007cba;
    color: white;
}

.btn-primary:hover {
    background-color: #005a87;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.loading {
    display: none;
    text-align: center;
    padding: 20px;
}

.required {
    color: red;
}

.input-with-button {
    display: flex;
    gap: 5px;
}

.input-with-button input {
    flex: 1;
    cursor: pointer;
}

.btn-search {
    padding: 10px 15px;
    border: 1px solid #007cba;
    background-color: #007cba;
    color: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn-search:hover {
    background-color: #005a87;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 90vw;
    height: 80vh;
    max-width: 800px;
    max-height: 600px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007cba;
}

.modal-header h3 {
    margin: 0;
    color: #007cba;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: #333;
}

.modal-search {
    margin-bottom: 15px;
}

.modal-search input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.modal-list {
    flex: 1;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.bayi-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.bayi-item:hover {
    background-color: #f5f5f5;
}

.bayi-item:last-child {
    border-bottom: none;
}
</style>

<div class="form-container">
    <div class="form-header">
        <h2>Yeni Fırsat Oluştur</h2>
    </div>

    <div id="alert-container"></div>
    <div class="loading" id="loading">İşlem yapılıyor...</div>

    <form id="firsat-form">
        <div class="form-row">
            <div class="form-group">
                <label for="marka">Marka <span class="required">*</span></label>
                <select id="marka" name="marka" required>
                    <option value="">Marka Seçiniz</option>
                </select>
            </div>
            <div class="form-group">
                <label>Geliş Kanalı <span class="required">*</span></label>
                <div class="radio-group">
                    <label><input type="radio" name="gelis_kanali" value="Bayiden" required> Bayiden</label>
                    <label><input type="radio" name="gelis_kanali" value="Üreticiden" required> Üreticiden</label>
                    <label><input type="radio" name="gelis_kanali" value="Komtera" required> Komtera</label>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="olasilik">Olasılık <span class="required">*</span></label>
                <select id="olasilik" name="olasilik" required>
                    <option value="">Olasılık Seçiniz</option>
                    <option value="1-Discovery">1-Discovery</option>
                    <option value="2-Solution Mapping">2-Solution Mapping</option>
                    <option value="3-Demo/POC">3-Demo/POC</option>
                    <option value="4-Negotiation">4-Negotiation</option>
                    <option value="5-Confirmed/Waiting for End-User PO">5-Confirmed/Waiting for End-User PO</option>
                    <option value="6-Run Rate">6-Run Rate</option>
                </select>
            </div>
            <div class="form-group">
                <label for="proje_adi">Proje Adı <span class="required">*</span></label>
                <input type="text" id="proje_adi" name="proje_adi" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="firsat_aciklama">Fırsat Açıklama</label>
                <textarea id="firsat_aciklama" name="firsat_aciklama" rows="3"></textarea>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bayi">Bayi</label>
                <div class="input-with-button">
                    <input type="text" id="bayi" name="bayi" placeholder="Bayi seçmek için tıklayın" readonly onclick="openBayiModal()">
                    <input type="hidden" id="bayi_kodu" name="bayi_kodu">
                    <button type="button" class="btn-search" onclick="openBayiModal()">🔍</button>
                </div>
            </div>
            <div class="form-group">
                <label for="musteri">Müşteri</label>
                <select id="musteri" name="musteri">
                    <option value="">Müşteri Seçiniz</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="accman">AccMan</label>
                <select id="accman" name="accman">
                    <option value="">AccMan Seçiniz</option>
                </select>
            </div>
            <div class="form-group">
                <label for="etkinlik">Etkinlik</label>
                <input type="text" id="etkinlik" name="etkinlik">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="register" name="register">
                    <label for="register">Register</label>
                </div>
            </div>
            <div class="form-group">
                <label for="register_dr_no">Register DR No</label>
                <input type="text" id="register_dr_no" name="register_dr_no">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Müşteri Temsilcisi</label>
                <input type="text" value="<?php echo esc_html($user); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Oluşturan</label>
                <input type="text" value="<?php echo esc_html($user); ?>" disabled>
            </div>
        </div>

        <div class="btn-container">
            <button type="submit" class="btn btn-primary">Fırsat Oluştur</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">İptal</button>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Dropdown'ları doldur
    loadMarkalar();
    loadMusteriler();

    // Form submit
    $('#firsat-form').on('submit', function(e) {
        e.preventDefault();
        submitForm();
    });
});

function loadMarkalar() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_markalar',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = jQuery('#marka');
            data.forEach(function(item) {
                select.append('<option value="' + item.MARKA + '">' + item.MARKA + '</option>');
            });
        },
        error: function() {
            showAlert('Marka listesi yüklenirken hata oluştu.', 'danger');
        }
    });
}

// Bayi Modal Functions
function openBayiModal() {
    const modalHtml = `
        <div id="bayi-modal" class="modal-overlay" onclick="closeBayiModal(event)">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>Bayi Seçimi</h3>
                    <button class="modal-close" onclick="closeBayiModal()">&times;</button>
                </div>
                <div class="modal-search">
                    <input type="text" id="bayi-search" placeholder="Bayi adı ile ara..." onkeyup="searchBayiler(this.value)">
                </div>
                <div class="modal-list" id="bayi-list">
                    <div style="text-align: center; padding: 20px;">Yükleniyor...</div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    loadAllBayiler();
}

function closeBayiModal(event) {
    if (event && event.target.id !== 'bayi-modal') return;
    const modal = document.getElementById('bayi-modal');
    if (modal) {
        modal.remove();
    }
}

function loadAllBayiler() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_bayiler',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayBayiler(data);
        },
        error: function() {
            document.getElementById('bayi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Bayi listesi yüklenirken hata oluştu.</div>';
        }
    });
}

function searchBayiler(query) {
    if (query.length < 2) {
        loadAllBayiler();
        return;
    }

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=search_bayiler&query=' + encodeURIComponent(query),
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            displayBayiler(data);
        },
        error: function() {
            document.getElementById('bayi-list').innerHTML = '<div style="text-align: center; padding: 20px; color: red;">Arama sırasında hata oluştu.</div>';
        }
    });
}

function displayBayiler(bayiler) {
    const listDiv = document.getElementById('bayi-list');
    if (bayiler.length === 0) {
        listDiv.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Bayi bulunamadı.</div>';
        return;
    }

    let html = '';
    bayiler.forEach(function(bayi) {
        html += `<div class="bayi-item" onclick="selectBayi('${bayi.CH_KODU}', '${bayi.CH_UNVANI}')">${bayi.CH_UNVANI}</div>`;
    });

    listDiv.innerHTML = html;
}

function selectBayi(bayiKodu, bayiAdi) {
    document.getElementById('bayi').value = bayiAdi;
    document.getElementById('bayi_kodu').value = bayiKodu;
    closeBayiModal();
}

function loadMusteriler() {
    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php?action=get_musteriler',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = jQuery('#musteri, #accman');
            data.forEach(function(item) {
                select.append('<option value="' + item.musteri + '">' + item.musteri + '</option>');
            });
        },
        error: function() {
            console.log('Müşteri listesi yüklenirken hata oluştu.');
        }
    });
}

function submitForm() {
    jQuery('#loading').show();
    var formData = jQuery('#firsat-form').serialize();
    formData += '&action=save_firsat';

    jQuery.ajax({
        url: '<?php echo get_stylesheet_directory_uri(); ?>/erp/mod/yeni_firsat.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            jQuery('#loading').hide();
            if (response.success) {
                showAlert('Fırsat başarıyla oluşturuldu! Fırsat No: ' + response.firsat_no, 'success');
                jQuery('#firsat-form')[0].reset();
                setTimeout(function() {
                    window.location.href = '<?php echo admin_url('admin.php?page=firsatlar'); ?>';
                }, 2000);
            } else {
                showAlert(response.message, 'danger');
            }
        },
        error: function() {
            jQuery('#loading').hide();
            showAlert('Bir hata oluştu. Lütfen tekrar deneyin.', 'danger');
        }
    });
}

function showAlert(message, type) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var html = '<div class="alert ' + alertClass + '">' + message + '</div>';
    jQuery('#alert-container').html(html);
    setTimeout(function() {
        jQuery('#alert-container').html('');
    }, 5000);
}
</script>
<?php
// WordPress integration for user data
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

include '../_conn.php';
?>

<div style="max-width: 1200px; margin: 20px auto; padding: 20px; background: #f8f9fa; border-radius: 8px;">
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; margin-bottom: 30px;">
            <span class="dashicons dashicons-plus-alt2" style="font-size: 28px; color: #0073aa; margin-right: 15px;"></span>
            <h2 style="margin: 0; color: #333; font-size: 24px;"><?php echo __('yeni_firsat', 'komtera'); ?></h2>
        </div>

        <form id="yeni_firsat_form" method="post" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Sol Kolon -->
            <div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('firsat_no', 'komtera'); ?> *
                    </label>
                    <input type="text" name="firsat_no" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           placeholder="<?php echo __('firsat_no_placeholder', 'komtera'); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('musteri_adi', 'komtera'); ?> *
                    </label>
                    <input type="text" name="musteri_adi" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           placeholder="<?php echo __('musteri_adi_placeholder', 'komtera'); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('bayi_adi', 'komtera'); ?> *
                    </label>
                    <select name="bayi_adi" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value=""><?php echo __('bayi_seciniz', 'komtera'); ?></option>
                        <?php
                        // Bayi listesi çekme
                        try {
                            $stmt = $conn->query("SELECT DISTINCT BAYI_ADI FROM LKS.dbo.aa_erp_kt_firsatlar WHERE BAYI_ADI IS NOT NULL AND BAYI_ADI != '' ORDER BY BAYI_ADI");
                            $bayiler = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($bayiler as $bayi) {
                                echo '<option value="' . htmlspecialchars($bayi['BAYI_ADI']) . '">' . htmlspecialchars($bayi['BAYI_ADI']) . '</option>';
                            }
                        } catch (Exception $e) {
                            echo '<option value="">Bayi listesi yüklenemedi</option>';
                        }
                        ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('marka', 'komtera'); ?> *
                    </label>
                    <select name="marka" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value=""><?php echo __('marka_seciniz', 'komtera'); ?></option>
                        <?php
                        // Marka listesi kullanıcının yetkili olduğu markalardan
                        $brands = get_user_meta(get_current_user_id(), 'my_brands', true) ?: [];
                        if (is_array($brands)) {
                            foreach ($brands as $brand) {
                                echo '<option value="' . htmlspecialchars($brand) . '">' . htmlspecialchars($brand) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('baslangic_tarihi', 'komtera'); ?> *
                    </label>
                    <input type="date" name="baslangic_tarihi" required
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('bitis_tarihi', 'komtera'); ?>
                    </label>
                    <input type="date" name="bitis_tarihi"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           value="<?php echo date('Y-m-d', strtotime('+3 months')); ?>">
                </div>
            </div>

            <!-- Sağ Kolon -->
            <div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('olasilik', 'komtera'); ?>
                    </label>
                    <select name="olasilik"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="">Seçiniz</option>
                        <option value="1-Discovery">1-Discovery</option>
                        <option value="2-Solution Mapping">2-Solution Mapping</option>
                        <option value="3-Demo/POC">3-Demo/POC</option>
                        <option value="4-Negotiation">4-Negotiation</option>
                        <option value="5-Confirmed/Waiting for End-User PO">5-Confirmed/Waiting for End-User PO</option>
                        <option value="6-Run Rate">6-Run Rate</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('para_birimi', 'komtera'); ?>
                    </label>
                    <select name="para_birimi"
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="TRY">TRY</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('gelis_kanali', 'komtera'); ?>
                    </label>
                    <input type="text" name="gelis_kanali"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           placeholder="<?php echo __('gelis_kanali_placeholder', 'komtera'); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('proje_adi', 'komtera'); ?>
                    </label>
                    <input type="text" name="proje_adi"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                           placeholder="<?php echo __('proje_adi_placeholder', 'komtera'); ?>">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">
                        <?php echo __('firsat_aciklama', 'komtera'); ?>
                    </label>
                    <textarea name="firsat_aciklama" rows="4"
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;"
                              placeholder="<?php echo __('firsat_aciklama_placeholder', 'komtera'); ?>"></textarea>
                </div>
            </div>

            <!-- Butonlar - Full width -->
            <div style="grid-column: 1 / -1; display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                <button type="button" onclick="window.location.href='?page=firsatlar'"
                        style="padding: 12px 30px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <?php echo __('iptal', 'komtera'); ?>
                </button>
                <button type="submit"
                        style="padding: 12px 30px; background: #0073aa; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <span class="dashicons dashicons-saved" style="font-size: 16px; margin-right: 5px; vertical-align: middle;"></span>
                    <?php echo __('kaydet', 'komtera'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('yeni_firsat_form').addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('action', 'save_firsat');

    // Loading gösterimi
    var submitBtn = this.querySelector('[type="submit"]');
    var originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="dashicons dashicons-update" style="animation: rotation 1s infinite linear; font-size: 16px; margin-right: 5px; vertical-align: middle;"></span>Kaydediliyor...';
    submitBtn.disabled = true;

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Fırsat başarıyla kaydedildi!');
            window.location.href = '?page=firsatlar';
        } else {
            alert('Hata: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        alert('Hata: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<style>
@keyframes rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(359deg);
  }
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #0073aa;
    box-shadow: 0 0 0 2px rgba(0,115,170,0.1);
}

button:hover {
    opacity: 0.9;
}

form select option {
    padding: 8px;
}
</style>

<?php
// AJAX handler for saving firsat
if (!function_exists('save_firsat_ajax_handler')) {
    function save_firsat_ajax_handler() {
        // Verify user permissions
        if (!current_user_can('read')) {
            wp_die('Permission denied');
        }

        include '../_conn.php';

        try {
            // Get form data
            $firsat_no = sanitize_text_field($_POST['firsat_no']);
            $musteri_adi = sanitize_text_field($_POST['musteri_adi']);
            $bayi_adi = sanitize_text_field($_POST['bayi_adi']);
            $marka = sanitize_text_field($_POST['marka']);
            $baslangic_tarihi = sanitize_text_field($_POST['baslangic_tarihi']);
            $bitis_tarihi = sanitize_text_field($_POST['bitis_tarihi']);
            $olasilik = sanitize_text_field($_POST['olasilik']);
            $para_birimi = sanitize_text_field($_POST['para_birimi']);
            $gelis_kanali = sanitize_text_field($_POST['gelis_kanali']);
            $proje_adi = sanitize_text_field($_POST['proje_adi']);
            $firsat_aciklama = sanitize_textarea_field($_POST['firsat_aciklama']);

            // Current user info
            $kayidi_acan = wp_get_current_user()->user_login;

            // Insert query
            $sql = "INSERT INTO LKS.dbo.aa_erp_kt_firsatlar (
                FIRSAT_NO, MUSTERI_ADI, BAYI_ADI, MARKA,
                BASLANGIC_TARIHI, BITIS_TARIHI, OLASILIK, PARA_BIRIMI,
                GELIS_KANALI, PROJE_ADI, FIRSAT_ACIKLAMA, KAYIDI_ACAN,
                DURUM, SIL, OLUSTURMA_TARIHI, MUSTERI_TEMSILCISI
            ) VALUES (
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                '0', '0', GETDATE(), ?
            )";

            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([
                $firsat_no, $musteri_adi, $bayi_adi, $marka,
                $baslangic_tarihi, $bitis_tarihi ?: null, $olasilik, $para_birimi,
                $gelis_kanali, $proje_adi, $firsat_aciklama, $kayidi_acan,
                $kayidi_acan
            ]);

            if ($result) {
                wp_send_json_success(['message' => 'Fırsat başarıyla kaydedildi']);
            } else {
                wp_send_json_error(['message' => 'Veritabanı hatası']);
            }

        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    add_action('wp_ajax_save_firsat', 'save_firsat_ajax_handler');
}
?>
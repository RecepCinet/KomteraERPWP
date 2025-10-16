<?php
/**
 * Teklif Notu Modülü - MSSQL
 */

// MSSQL bağlantısı
require_once get_stylesheet_directory() . '/erp/_conn.php';
require_once get_stylesheet_directory() . '/inc/table_helper.php';

$table_name = getTableName('aa_erp_kt_values');

// Teklif notunu çek
try {
    $stmt = $conn->prepare("SELECT [value] FROM [$table_name] WHERE [key] = :key");
    $stmt->execute(['key' => 'teklif_notu']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $teklif_notu = $result ? $result['value'] : '';
} catch (PDOException $e) {
    $teklif_notu = '';
    error_log('Teklif notu okuma hatası: ' . $e->getMessage());
}
?>

<style>
.teklif-notu-container {
    padding: 20px;
    max-width: 1200px;
}

.teklif-notu-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.teklif-notu-textarea {
    width: 100%;
    min-height: 400px;
    padding: 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
    box-sizing: border-box;
}

.teklif-notu-textarea:focus {
    outline: none;
    border-color: #0073aa;
    box-shadow: 0 0 0 1px #0073aa;
}

.teklif-notu-button {
    margin-top: 15px;
    padding: 10px 30px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.teklif-notu-button:hover {
    background: #005a87;
}

.teklif-notu-button:active {
    background: #004a70;
}

.teklif-notu-message {
    margin-top: 10px;
    padding: 10px 15px;
    border-radius: 4px;
    display: none;
}

.teklif-notu-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.teklif-notu-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<div class="teklif-notu-container">
    <div class="teklif-notu-title">Teklif Notu</div>

    <textarea
        id="teklif_notu_textarea"
        class="teklif-notu-textarea"
        placeholder="Teklif notunu buraya yazın..."><?php echo htmlspecialchars($teklif_notu, ENT_QUOTES, 'UTF-8'); ?></textarea>

    <button id="teklif_notu_kaydet" class="teklif-notu-button">Kaydet</button>

    <div id="teklif_notu_message" class="teklif-notu-message"></div>
</div>

<script type="text/javascript">
(function($) {
    console.log('Teklif notu script yüklendi');
    console.log('jQuery:', typeof $);

    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
    console.log('ajaxurl:', ajaxurl);

    // Script çalıştığında veya AJAX yüklendiğinde butonu bul
    function initTeklifNotu() {
        console.log('initTeklifNotu çağrıldı');

        var button = $('#teklif_notu_kaydet');
        console.log('Buton bulundu mu:', button.length);

        if (button.length === 0) {
            console.log('Buton bulunamadı, 500ms sonra tekrar denenecek');
            setTimeout(initTeklifNotu, 500);
            return;
        }

        // Önceki event'leri temizle
        button.off('click');

        button.on('click', function(e) {
            e.preventDefault();
            console.log('Kaydet butonuna tıklandı');

            var textarea = $('#teklif_notu_textarea');
            var message = $('#teklif_notu_message');
            var value = textarea.val();

            console.log('Gönderilecek değer:', value);

            // Button'u disable et
            button.prop('disabled', true).text('Kaydediliyor...');
            message.hide();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_teklif_notu',
                    value: value,
                    nonce: '<?php echo wp_create_nonce("save_teklif_notu"); ?>'
                },
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.success) {
                        message.removeClass('error').addClass('success')
                            .text('Teklif notu başarıyla kaydedildi!').fadeIn();
                    } else {
                        var errorMsg = 'Hata: ' + (response.data || 'Bilinmeyen hata');
                        if (typeof response.data === 'object') {
                            errorMsg = 'Hata: ' + JSON.stringify(response.data);
                        }
                        console.error('Error details:', response.data);
                        message.removeClass('success').addClass('error')
                            .text(errorMsg).fadeIn();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr, status, error);
                    message.removeClass('success').addClass('error')
                        .text('Kayıt sırasında bir hata oluştu: ' + error).fadeIn();
                },
                complete: function() {
                    button.prop('disabled', false).text('Kaydet');
                    setTimeout(function() {
                        message.fadeOut();
                    }, 3000);
                }
            });
        });
    }

    // DOM hazır olduğunda veya hemen çalıştır
    if (document.readyState === 'loading') {
        $(document).ready(initTeklifNotu);
    } else {
        initTeklifNotu();
    }

})(jQuery);
</script>

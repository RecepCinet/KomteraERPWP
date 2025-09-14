<?php
// Debug mode - hataları göster
ini_set('display_errors', 1);
error_reporting(E_ALL);

// WordPress admin sayfası olarak çalıştır
require_once(dirname(__FILE__) . '/../../../wp-load.php');
require_once(dirname(__FILE__) . '/../../../wp-admin/admin.php');

// Admin header dahil et
require_once(ABSPATH . 'wp-admin/admin-header.php');

$search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Debug bilgisi
echo "<!-- DEBUG: GET array: " . print_r($_GET, true) . " -->";
echo "<!-- DEBUG: Search term: '$search_term' -->";
?>

<div class="wrap">
    <h1>🔍 Arama Sonuçları</h1>
    
    <!-- Debug çıktısı -->
    <div class="notice notice-info" style="background: #e7f3ff; border-left: 4px solid #007cba; padding: 15px;">
        <h3>🔍 Debug Bilgisi</h3>
        <p><strong>GET Parametreleri:</strong> <?php echo json_encode($_GET); ?></p>
        <p><strong>Search Terimi:</strong> <?php echo $search_term ? "'{$search_term}'" : "BOŞ"; ?></p>
        <p><strong>URL:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Bilinmiyor'; ?></p>
    </div>
    
    <?php if (!empty($search_term)) : ?>
        <div class="notice notice-info" style="background: #f0f8f0; border-left: 4px solid #46b450; padding: 15px;">
            <h2>✅ Arama Başarılı</h2>
            <p><strong>Aranan:</strong> "<?php echo esc_html($search_term); ?>"</p>
        </div>
        
        <div class="notice notice-warning" style="background: #fff8e1; border-left: 4px solid #ffb900; padding: 20px; margin: 20px 0;">
            <h2>🚧 YAPIM AŞAMASINDA</h2>
            <h3>Sonuç Bulunamadı</h3>
            <p><strong>Bu arama özelliği şu anda geliştirilme aşamasında</strong></p>
            <p><em>Çok yakında aktif olacak...</em></p>
            <hr style="margin: 20px 0;">
            <p><strong>Geliştirici Notu:</strong> Bu alana arama kodları eklenecek</p>
            <ul>
                <li>Fırsatlar tablosunda arama</li>
                <li>Siparişler tablosunda arama</li>
                <li>Müşteriler tablosunda arama</li>
                <li>Global arama</li>
            </ul>
        </div>
        
    <?php else : ?>
        <div class="notice notice-error" style="background: #ffebee; border-left: 4px solid #dc3232; padding: 15px;">
            <h3>❌ Arama Terimi Girilmedi</h3>
            <p>Lütfen arama terimi girin ve tekrar deneyin</p>
            <p><strong>URL'de 'search' parametresi bulunamadı!</strong></p>
        </div>
    <?php endif; ?>
    
    <p class="submit">
        <a href="javascript:history.back()" class="button button-secondary">← Geri Dön</a>
        <a href="<?php echo admin_url(); ?>" class="button button-primary" style="margin-left: 10px;">🏠 Ana Sayfa</a>
    </p>
    
    <hr>
    <div class="notice notice-info" style="margin-top: 20px;">
        <h4>Geliştirici Bilgisi</h4>
        <ul>
            <li><strong>Dosya:</strong> <code><?php echo basename(__FILE__); ?></code></li>
            <li><strong>GET Parametresi:</strong> <code>search=<?php echo esc_html($search_term); ?></code></li>
            <li><strong>Kullanıcı:</strong> <?php echo wp_get_current_user()->display_name; ?> (<?php echo wp_get_current_user()->user_login; ?>)</li>
            <li><strong>Zaman:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
            <li><strong>WordPress Admin URL:</strong> <code><?php echo admin_url(); ?></code></li>
        </ul>
    </div>
</div>

<?php
require_once(ABSPATH . 'wp-admin/admin-footer.php');
?>
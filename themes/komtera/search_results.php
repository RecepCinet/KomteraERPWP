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
    <h1>🔍 <?php echo __('Arama Sonuçları', 'komtera'); ?></h1>
    
    <!-- Debug çıktısı -->
    <div class="notice notice-info" style="background: #e7f3ff; border-left: 4px solid #007cba; padding: 15px;">
        <h3>🔍 <?php echo __('Debug Bilgisi', 'komtera'); ?></h3>
        <p><strong><?php echo __('GET Parametreleri', 'komtera'); ?>:</strong> <?php echo json_encode($_GET); ?></p>
        <p><strong><?php echo __('Search Terimi', 'komtera'); ?>:</strong> <?php echo $search_term ? "'{$search_term}'" : __('BOŞ', 'komtera'); ?></p>
        <p><strong><?php echo __('URL', 'komtera'); ?>:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? __('Bilinmiyor', 'komtera'); ?></p>
    </div>
    
    <?php if (!empty($search_term)) : ?>
        <div class="notice notice-info" style="background: #f0f8f0; border-left: 4px solid #46b450; padding: 15px;">
            <h2>✅ <?php echo __('Arama Başarılı', 'komtera'); ?></h2>
            <p><strong><?php echo __('Aranan', 'komtera'); ?>:</strong> "<?php echo esc_html($search_term); ?>"</p>
        </div>
        
        <div class="notice notice-warning" style="background: #fff8e1; border-left: 4px solid #ffb900; padding: 20px; margin: 20px 0;">
            <h2>🚧 <?php echo __('YAPIM AŞAMASINDA', 'komtera'); ?></h2>
            <h3><?php echo __('Sonuç Bulunamadı', 'komtera'); ?></h3>
            <p><strong><?php echo __('Bu arama özelliği şu anda geliştirilme aşamasında', 'komtera'); ?></strong></p>
            <p><em><?php echo __('Çok yakında aktif olacak...', 'komtera'); ?></em></p>
            <hr style="margin: 20px 0;">
            <p><strong><?php echo __('Geliştirici Notu', 'komtera'); ?>:</strong> <?php echo __('Bu alana arama kodları eklenecek', 'komtera'); ?></p>
            <ul>
                <li><?php echo __('Fırsatlar tablosunda arama', 'komtera'); ?></li>
                <li><?php echo __('Siparişler tablosunda arama', 'komtera'); ?></li>
                <li><?php echo __('Müşteriler tablosunda arama', 'komtera'); ?></li>
                <li><?php echo __('Global arama', 'komtera'); ?></li>
            </ul>
        </div>
        
    <?php else : ?>
        <div class="notice notice-error" style="background: #ffebee; border-left: 4px solid #dc3232; padding: 15px;">
            <h3>❌ <?php echo __('Arama Terimi Girilmedi', 'komtera'); ?></h3>
            <p><?php echo __('Lütfen arama terimi girin ve tekrar deneyin', 'komtera'); ?></p>
            <p><strong><?php echo __('URL\'de \'search\' parametresi bulunamadı!', 'komtera'); ?></strong></p>
        </div>
    <?php endif; ?>
    
    <p class="submit">
        <a href="javascript:history.back()" class="button button-secondary">← <?php echo __('Geri Dön', 'komtera'); ?></a>
        <a href="<?php echo admin_url(); ?>" class="button button-primary" style="margin-left: 10px;">🏠 <?php echo __('Ana Sayfa', 'komtera'); ?></a>
    </p>
    
    <hr>
    <div class="notice notice-info" style="margin-top: 20px;">
        <h4><?php echo __('Geliştirici Bilgisi', 'komtera'); ?></h4>
        <ul>
            <li><strong><?php echo __('Dosya', 'komtera'); ?>:</strong> <code><?php echo basename(__FILE__); ?></code></li>
            <li><strong><?php echo __('GET Parametresi', 'komtera'); ?>:</strong> <code>search=<?php echo esc_html($search_term); ?></code></li>
            <li><strong><?php echo __('Kullanıcı', 'komtera'); ?>:</strong> <?php echo wp_get_current_user()->display_name; ?> (<?php echo wp_get_current_user()->user_login; ?>)</li>
            <li><strong><?php echo __('Zaman', 'komtera'); ?>:</strong> <?php echo date('Y-m-d H:i:s'); ?></li>
            <li><strong>WordPress Admin URL:</strong> <code><?php echo admin_url(); ?></code></li>
        </ul>
    </div>
</div>

<?php
require_once(ABSPATH . 'wp-admin/admin-footer.php');
?>
<?php

/*
 *     'satis' => 'Satış',
    'satis_destek' => 'Satış Destek',
    'teknik' => 'Teknik',
    'finans' => 'Finans',
    'yonetim' => 'Yönetim',
    'lojistik' => 'Lojistik',
    'idari_isler' => 'İdari İşler'
 *
 */

function my_custom_admin_menus_for_roles()
{
    $u = wp_get_current_user();
    if (!array_intersect(['subscriber', 'administrator', 'jadmin', 'satis', 'satis_destek', 'teknik' , 'finans' , 'yonetim', 'lojistik', 'isari_isler'], (array)$u->roles)) {
        return;
    }

    $ana_yetkiler = get_user_meta(get_current_user_id(), 'my_permissions_matrix', true);
    if (!is_array($ana_yetkiler)) {
        $decoded = is_string($ana_yetkiler) ? json_decode($ana_yetkiler, true) : null;
        $ana_yetkiler = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    }

    if (array_key_exists('_opportunities_',    $ana_yetkiler)) {
        add_menu_page(__('Fırsatlar', 'komtera'), __('Fırsatlar', 'komtera'), 'read','firsatlar', 'firsatlar_cb','dashicons-visibility',2.01);
        add_submenu_page('firsatlar', __('Listeler', 'komtera'), __('Listeler', 'komtera'), 'read','firsatlar', 'firsatlar_cb');
        add_submenu_page('firsatlar', __('Yeni Fırsat', 'komtera'), __('Yeni Fırsat', 'komtera'), 'read','firsatlar_yeni', 'firsatlar_yeni_cb');
        // Gizli sayfa: parent null olduğu için menüde görünmez
        add_submenu_page(null, __('Fırsat Detayı', 'komtera'), __('Fırsat Detayı', 'komtera'), 'read','firsatlar_detay', 'firsatlar_detay_cb');
        // Gizli sayfa: Teklif Detayı
        add_submenu_page(null, __('Teklif Detayı', 'komtera'), __('Teklif Detayı', 'komtera'), 'read','teklifler_detay', 'teklifler_detay_cb');
    }
    if (array_key_exists('_orders_',   $ana_yetkiler)) add_menu_page(__('Siparişler', 'komtera'), __('Siparişler', 'komtera'), 'read','siparisler_slug',          'siparisler_cb','dashicons-cart',2.02);
    if (array_key_exists('_demos_',      $ana_yetkiler)) add_menu_page(__('Demolar', 'komtera'), __('Demolar', 'komtera'), 'read','demolar_slug',                   'demolar_cb','dashicons-screenoptions',2.03);
    if (array_key_exists('_activities_',  $ana_yetkiler)) add_menu_page(__('Aktiviteler', 'komtera'), __('Aktiviteler', 'komtera'), 'read','aktiviteler_slug',       'aktiviteler_cb','dashicons-clock',2.04);
    if (array_key_exists('_poc_',          $ana_yetkiler)) add_menu_page(__('POC', 'komtera'), __('POC', 'komtera'), 'read','poc_slug',                               'poc_cb','dashicons-networking',2.05);
    if (array_key_exists('_reports_',     $ana_yetkiler)) add_menu_page(__('Raporlar', 'komtera'), __('Raporlar', 'komtera'), 'read','raporlar_slug',                'raporlar_cb','dashicons-chart-pie',2.06);
    if (array_key_exists('_reports_management_', $ana_yetkiler)) add_menu_page(__('Raporlar Yönetimi', 'komtera'), __('Raporlar Yönetimi', 'komtera'), 'read','raporlar_yonetim_slug','raporlar_yonetim_cb','dashicons-chart-line',2.065);
    if (array_key_exists('_tools_',      $ana_yetkiler)) add_menu_page(__('Araçlar', 'komtera'), __('Araçlar', 'komtera'), 'read','araclar_slug',                   'araclar_cb','dashicons-admin-tools',2.07);
    if (array_key_exists('_pricelist_', $ana_yetkiler)) add_menu_page(__('Fiyat Listesi', 'komtera'), __('Fiyat Listesi', 'komtera'), 'read','fiyat_listesi_slug', 'fiyat_listesi_cb','dashicons-tag',2.08);
    if (array_key_exists('_renewals_',  $ana_yetkiler)) add_menu_page(__('Yenilemeler', 'komtera'), __('Yenilemeler', 'komtera'), 'read','yenilemeler_slug',       'yenilemeler_cb','dashicons-update',2.09);
    if (array_key_exists('_invoices_',    $ana_yetkiler)) add_menu_page(__('Faturalar', 'komtera'), __('Faturalar', 'komtera'), 'read','faturalar_slug',             'faturalar_cb','dashicons-text',2.10);
    if (array_key_exists('_stocks_',      $ana_yetkiler)) add_menu_page(__('Stoklar', 'komtera'), __('Stoklar', 'komtera'), 'read','stoklar_slug',                   'stoklar_cb','dashicons-database-add',2.11);
    if (array_key_exists('_dealers_',      $ana_yetkiler)) add_menu_page(__('Bayiler', 'komtera'), __('Bayiler', 'komtera'), 'read','bayiler_slug',                   'bayiler_cb','dashicons-building',2.12);
    if (array_key_exists('_customers_',   $ana_yetkiler)) add_menu_page(__('Müşteriler', 'komtera'), __('Müşteriler', 'komtera'), 'read','musteriler_slug',          'musteriler_cb','dashicons-groups',2.13);
    if (array_key_exists('_settings_',      $ana_yetkiler)) add_menu_page(__('Ayarlar', 'komtera'), __('Ayarlar', 'komtera'), 'read','ayarlar_slug',                   'ayarlar_cb','dashicons-admin-generic',2.14);

}

add_action('admin_menu', 'my_custom_admin_menus_for_roles');


function firsatlar_cb()
{
    // Get the table parameter from URL, default to 'firsatlar'
    $selected_table = isset($_GET['table']) ? sanitize_text_field($_GET['table']) : 'firsatlar';
    // Default table, JavaScript will update this dynamically
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=' . $selected_table;
    $locale = get_user_locale(); // Kullanıcının seçtiği locale (tr_TR, en_US, etc.)
    $lang = substr($locale, 0, 2); // İlk iki harf (tr, en, etc.)
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="opportunities-toolbar" style="
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
            <!-- Tarih Seçimi ve Fırsat Türü Butonları - Excel toolbar tarzı -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <!-- Yeni Fırsat Butonu -->
                <div class="opportunity-button table-btn" onclick="window.location.href='?page=firsatlar_yeni'" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#d4edda'; this.style.borderColor='#28a745'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-plus-alt2" style="font-size: 24px; color: #28a745; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('Yeni Fırsat', 'komtera'); ?></span>
                </div>

                <!-- Tarih Seçimi -->
                <div id="date_selector" style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        ">
                    <span class="dashicons dashicons-calendar-alt" style="font-size: 20px; color: #0073aa;"></span>
                    <input type="date" id="date1" name="date1" lang="<?php echo esc_attr($lang); ?>"
                           style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 130px;">
                    <span style="color: #666; font-weight: bold;">-</span>
                    <input type="date" id="date2" name="date2" lang="<?php echo esc_attr($lang); ?>"
                           style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 130px;">
                </div>

                <!-- Fırsat Türü Butonları -->
                <div id="firsat_buttons" class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar') ? 'active' : ''; ?>" data-table="firsatlar" data-icon-color="#0073aa" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-unlock" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar') ? 'white' : '#0073aa'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar') ? 'white' : '#333'; ?> !important;"><?php echo __('Açık Fırsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar_tek') ? 'active' : ''; ?>" data-table="firsatlar_tek" data-icon-color="#ff9800" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar_tek') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar_tek') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar_tek') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-media-document" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar_tek') ? 'white' : '#ff9800'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar_tek') ? 'white' : '#333'; ?>;"><?php echo __('Açık Ana Teklifler', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar_kaz') ? 'active' : ''; ?>" data-table="firsatlar_kaz" data-icon-color="#4caf50" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar_kaz') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar_kaz') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar_kaz') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar_kaz') ? 'white' : '#4caf50'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar_kaz') ? 'white' : '#333'; ?>;"><?php echo __('Kazanılan', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar_kay') ? 'active' : ''; ?>" data-table="firsatlar_kay" data-icon-color="#f44336" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar_kay') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar_kay') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar_kay') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-dismiss" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar_kay') ? 'white' : '#f44336'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar_kay') ? 'white' : '#333'; ?>;"><?php echo __('Kaybedilen Fırsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar2') ? 'active' : ''; ?>" data-table="firsatlar2" data-icon-color="#9c27b0" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar2') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar2') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar2') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-list-view" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar2') ? 'white' : '#9c27b0'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar2') ? 'white' : '#333'; ?>;"><?php echo __('Tüm Fırsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn <?php echo ($selected_table === 'firsatlar_yanfir') ? 'active' : ''; ?>" data-table="firsatlar_yanfir" data-icon-color="#ff5722" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'firsatlar_yanfir') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'firsatlar_yanfir') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'firsatlar_yanfir') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff5722'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-networking" style="font-size: 24px; color: <?php echo ($selected_table === 'firsatlar_yanfir') ? 'white' : '#ff5722'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'firsatlar_yanfir') ? 'white' : '#333'; ?>;"><?php echo __('Yan Fırsatlar', 'komtera'); ?></span>
                </div>
            </div>
        </div>

        <!-- Iframe container -->
        <div style="position: relative; height: calc(100vh - 280px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; border-radius: 4px; position: absolute; top: 0; left: 0;">
            </iframe>
        </div>
    </div>
    <script>
        (function () {
            const input1 = document.getElementById('date1');
            const input2 = document.getElementById('date2');
            const iframe = document.getElementById('erp_iframe');
            const baseDir = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php";
            const defaultTable = "firsatlar"; // Varsayılan tablo
            const locale = "<?php echo esc_js($locale); ?>"; // WordPress locale
            const lang = "<?php echo esc_js($lang); ?>"; // Dil kodu
            // Sayfanın dilini ayarla (takvim için)
            if (document.documentElement.lang !== locale) {
                document.documentElement.lang = locale;
            }
            // Input'lara da dil ayarını uygula ve format ayarla
            input1.setAttribute('lang', locale);
            input2.setAttribute('lang', locale);
            // CSS ile date input'larının formatını ayarla
            const style = document.createElement('style');
            style.textContent = `
                input[type="date"]::-webkit-calendar-picker-indicator {
                    background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>') no-repeat;
                    background-size: 16px;
                }
                input[type="date"] {
                    font-family: inherit;
                    direction: ${lang === 'tr' ? 'ltr' : 'ltr'};
                }
            `;
            document.head.appendChild(style);
            // YYYY-MM-DD format helper (local time)
            function fmt(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const a = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${a}`;
            }
            // İlk gelişte: bugün ve 1 ay öncesi (daha önce seçilmemişse)
            const today = new Date();
            const oneMonthAgo = new Date(today);
            oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1); // 1 ay öncesi
            // Tarihleri localStorage'dan al (eğer daha önce seçilmişse)
            const savedDate1 = localStorage.getItem('firsatlar_date1');
            const savedDate2 = localStorage.getItem('firsatlar_date2');
            // Sadece boşsa veya geçersizse varsayılan değerleri ata
            if (!input1.value && !savedDate1) {
                input1.value = fmt(oneMonthAgo);
            } else if (savedDate1) {
                input1.value = savedDate1;
            }
            if (!input2.value && !savedDate2) {
                input2.value = fmt(today);
            } else if (savedDate2) {
                input2.value = savedDate2;
            }
            function loadIframe() {
                const v1 = input1.value;
                const v2 = input2.value;
                if (!v1 || !v2) {
                    const msg = lang === 'tr' ? '<?php echo __('Lütfen iki tarihi de seçin.','komtera'); ?>' : 'Please select both dates.';
                    alert(msg);
                    return;
                }
                if (v1 > v2) {
                    const msg = lang === 'tr' ? '<?php echo __('Başlangıç tarihi, bitiş tarihinden büyük olamaz.','komtera'); ?>' : 'Start date cannot be greater than end date.';
                    alert(msg);
                    return;
                }
                // Seçilen tarihleri localStorage'a kaydet
                localStorage.setItem('firsatlar_date1', v1);
                localStorage.setItem('firsatlar_date2', v2);
                // Aktif butondan tablo adını al
                const activeBtn = document.querySelector('.table-btn.active');
                const tableName = activeBtn ? activeBtn.getAttribute('data-table') : defaultTable;

                const url = `${baseDir}?t=${tableName}&date1=${encodeURIComponent(v1)}&date2=${encodeURIComponent(v2)}`;
                iframe.src = url;
            }
            // İlk yüklemede otomatik getir
            loadIframe();
            // Enter ile tetikleme (tarih kutularındayken)
            [input1, input2].forEach(el => {
                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        loadIframe();
                    }
                });
            });

            // Tablo değiştirme butonları
            document.querySelectorAll('.table-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const tableName = this.getAttribute('data-table');
                    const v1 = input1.value;
                    const v2 = input2.value;

                    // Aktif buton stilini değiştir
                    document.querySelectorAll('.table-btn').forEach(function(b) {
                        b.classList.remove('active');
                        b.style.border = '1px solid #ccc';
                        b.style.background = 'white';
                        b.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';

                        // İkon ve yazı rengini sıfırla - data-icon-color'dan al
                        const spans = b.querySelectorAll('span');
                        const iconColor = b.getAttribute('data-icon-color');
                        if (spans.length >= 2 && iconColor) {
                            spans[0].style.color = iconColor; // İkon
                            spans[1].style.color = '#333'; // Yazı
                        }
                    });
                    this.classList.add('active');
                    this.style.border = '2px solid #0073aa';
                    this.style.background = '#0073aa';
                    this.style.boxShadow = '0 2px 4px rgba(0,115,170,0.2)';
                    // İkon ve yazı rengini beyaz yap
                    const spans = this.querySelectorAll('span');
                    if (spans.length >= 2) {
                        spans[0].style.color = 'white'; // İkon
                        spans[1].style.color = 'white'; // Yazı
                    }

                    // Update URL with table parameter
                    const url = new URL(window.location.href);
                    url.searchParams.set('table', tableName);
                    window.history.pushState({}, '', url);

                    // iframe src'sini güncelle
                    let newSrc = `${baseDir}?t=${tableName}`;

                    // Tarih parametrelerini ekle
                    if (v1 && v2) {
                        newSrc += `&date1=${encodeURIComponent(v1)}&date2=${encodeURIComponent(v2)}`;
                    }

                    iframe.src = newSrc;
                });
            });
        })();
    </script>

    <!-- Buton Stilleri -->
    <style>
        .table-btn {
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .table-btn:hover {
            background-color: #0056b3 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-btn.active {
            background-color: #0073aa !important;
            box-shadow: 0 2px 6px rgba(0,115,170,0.3);
        }
        @media (max-width: 768px) {
            .table-btn {
                font-size: 9px !important;
                padding: 4px 6px !important;
                margin-right: 4px !important;
                margin-bottom: 4px;
            }
        }
    </style>
    <?php
}

function firsatlar_yeni_cb()
{
    ?>
    <div class="wrap">
        <?php include get_stylesheet_directory() . '/erp/mod/yeni_firsat.php'; ?>
    </div>
    <?php
}

function siparisler_cb()
{
    // Default table, JavaScript will update this dynamically
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=siparisler';
    $locale = get_user_locale(); // Kullanıcının seçtiği locale (tr_TR, en_US, etc.)
    $lang = substr($locale, 0, 2); // İlk iki harf (tr, en, etc.)
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="orders-toolbar" style="
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
            <!-- Tarih Seçimi - Excel toolbar tarzı -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <!-- Tarih Seçimi -->
                <div id="date_selector" style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        ">
                    <span class="dashicons dashicons-calendar-alt" style="font-size: 20px; color: #0073aa;"></span>
                    <input type="date" id="date1_sip" name="date1_sip" lang="<?php echo esc_attr($lang); ?>"
                           style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 130px;">
                    <span style="color: #666; font-weight: bold;">-</span>
                    <input type="date" id="date2_sip" name="date2_sip" lang="<?php echo esc_attr($lang); ?>"
                           style="padding: 6px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; width: 130px;">
                </div>

                <!-- Getir Butonu -->
                <div class="order-button" id="getir_btn_sip" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: #0073aa;
                        border: 2px solid #0073aa;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 2px 4px rgba(0,115,170,0.2);
                        " onmouseover="this.style.backgroundColor='#005a8b'; this.style.borderColor='#005a8b';" onmouseout="this.style.backgroundColor='#0073aa'; this.style.borderColor='#0073aa';">
                    <span class="dashicons dashicons-download" style="font-size: 24px; color: white; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: white !important;"><?php echo __('Getir', 'komtera'); ?></span>
                </div>
            </div>
        </div>

        <!-- Iframe container -->
        <div style="position: relative; height: calc(100vh - 280px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; border-radius: 4px; position: absolute; top: 0; left: 0;">
            </iframe>
        </div>
    </div>
    <script>
        (function () {
            const input1 = document.getElementById('date1_sip');
            const input2 = document.getElementById('date2_sip');
            const iframe = document.getElementById('erp_iframe');
            const baseDir = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php";
            const defaultTable = "siparisler"; // Varsayılan tablo
            const locale = "<?php echo esc_js($locale); ?>"; // WordPress locale
            const lang = "<?php echo esc_js($lang); ?>"; // Dil kodu

            // Sayfanın dilini ayarla (takvim için)
            if (document.documentElement.lang !== locale) {
                document.documentElement.lang = locale;
            }

            // Input'lara da dil ayarını uygula ve format ayarla
            input1.setAttribute('lang', locale);
            input2.setAttribute('lang', locale);

            // CSS ile date input'larının formatını ayarla
            const style = document.createElement('style');
            style.textContent = `
                input[type="date"]::-webkit-calendar-picker-indicator {
                    background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>') no-repeat;
                    background-size: 16px;
                }
                input[type="date"] {
                    font-family: inherit;
                    direction: ${lang === 'tr' ? 'ltr' : 'ltr'};
                }
            `;
            document.head.appendChild(style);

            // YYYY-MM-DD format helper (local time)
            function fmt(d) {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const a = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${a}`;
            }

            // İlk gelişte: bugün ve 1 ay öncesi (daha önce seçilmemişse)
            const today = new Date();
            const oneMonthAgo = new Date(today);
            oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1); // 1 ay öncesi

            // Tarihleri localStorage'dan al (eğer daha önce seçilmişse)
            const savedDate1 = localStorage.getItem('siparisler_date1');
            const savedDate2 = localStorage.getItem('siparisler_date2');

            // Sadece boşsa veya geçersizse varsayılan değerleri ata
            if (!input1.value && !savedDate1) {
                input1.value = fmt(oneMonthAgo);
            } else if (savedDate1) {
                input1.value = savedDate1;
            }
            if (!input2.value && !savedDate2) {
                input2.value = fmt(today);
            } else if (savedDate2) {
                input2.value = savedDate2;
            }

            function loadIframe() {
                const v1 = input1.value;
                const v2 = input2.value;
                if (!v1 || !v2) {
                    const msg = lang === 'tr' ? '<?php echo __('Lütfen iki tarihi de seçin.','komtera'); ?>' : 'Please select both dates.';
                    alert(msg);
                    return;
                }
                if (v1 > v2) {
                    const msg = lang === 'tr' ? '<?php echo __('Başlangıç tarihi, bitiş tarihinden büyük olamaz.','komtera'); ?>' : 'Start date cannot be greater than end date.';
                    alert(msg);
                    return;
                }

                // Seçilen tarihleri localStorage'a kaydet
                localStorage.setItem('siparisler_date1', v1);
                localStorage.setItem('siparisler_date2', v2);

                const url = `${baseDir}?t=${defaultTable}&date1=${encodeURIComponent(v1)}&date2=${encodeURIComponent(v2)}`;
                iframe.src = url;
            }

            // İlk yüklemede otomatik getir
            loadIframe();

            // Getir butonu event listener
            document.getElementById('getir_btn_sip').addEventListener('click', function() {
                loadIframe();
            });

            // Enter ile tetikleme (tarih kutularındayken)
            [input1, input2].forEach(el => {
                el.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        loadIframe();
                    }
                });
            });
        })();
    </script>

    <!-- Buton Stilleri -->
    <style>
        .order-button {
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .order-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .order-button {
                font-size: 9px !important;
                padding: 4px 6px !important;
                margin-right: 4px !important;
                margin-bottom: 4px;
            }
        }
    </style>
    <?php
}
function demolar_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=demolar';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function aktiviteler_cb()   {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=aktiviteler';
    ?>
    <div class="wrap">
        <div class="activities-toolbar" style="
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        ">
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="baslangic_tarihi" style="font-weight: 600;"><?php echo __('Başlangıç Tarihi:','komtera'); ?></label>
                <input type="date" id="baslangic_tarihi" name="baslangic_tarihi"
                       style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <label for="bitis_tarihi" style="font-weight: 600;"><?php echo __('Bitiş Tarihi:','komtera'); ?></label>
                <input type="date" id="bitis_tarihi" name="bitis_tarihi"
                       style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;">
            </div>
            <button type="button" id="filtrele_btn" onclick="aktiviteleriFiltrele()" style="
                background: #0073aa;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
            ">Filtrele</button>
        </div>
        <div style="position: relative; height: calc(100vh - 220px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <script>
    // Sayfa yüklendiğinde varsayılan tarihleri ayarla
    document.addEventListener('DOMContentLoaded', function() {
        const bugün = new Date();
        const birAyÖnce = new Date();
        birAyÖnce.setMonth(bugün.getMonth() - 1);

        // Tarih formatını YYYY-MM-DD olarak ayarla
        const bugünStr = bugün.toISOString().split('T')[0];
        const birAyÖnceStr = birAyÖnce.toISOString().split('T')[0];

        document.getElementById('baslangic_tarihi').value = birAyÖnceStr;
        document.getElementById('bitis_tarihi').value = bugünStr;

        // Sayfa ilk yüklendiğinde filtreyi uygula
        aktiviteleriFiltrele();
    });

    function aktiviteleriFiltrele() {
        const baslangicTarihi = document.getElementById('baslangic_tarihi').value;
        const bitisTarihi = document.getElementById('bitis_tarihi').value;

        let src = '<?php echo esc_url($src); ?>';
        let params = [];

        if (baslangicTarihi) {
            params.push('baslangic_tarihi=' + encodeURIComponent(baslangicTarihi));
        }
        if (bitisTarihi) {
            params.push('bitis_tarihi=' + encodeURIComponent(bitisTarihi));
        }

        if (params.length > 0) {
            src += (src.includes('?') ? '&' : '?') + params.join('&');
        }

        document.getElementById('erp_iframe').src = src;
    }
    </script>
    <?php
}
function poc_cb()           {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=poc';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function raporlar_cb() {
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="reports-toolbar" style="
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            <div class="report-button" onclick="loadReport('satis-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-chart-bar" style="font-size: 24px; color: #0073aa; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Satış Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('musteri-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-groups" style="font-size: 24px; color: #28a745; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Müşteri Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('gelir-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-money-alt" style="font-size: 24px; color: #ffc107; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Gelir Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('performans-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-awards" style="font-size: 24px; color: #dc3545; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Performans Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('stok-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-admin-page" style="font-size: 24px; color: #6f42c1; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Stok Raporu</span>
            </div>
        </div>

        <!-- Iframe container -->
        <div style="position: relative; height: calc(100vh - 280px); margin-top: 20px;">
            <iframe id="report_iframe"
                    src=""
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; border-radius: 4px; display: none;">
            </iframe>
            <div id="report-placeholder" style="
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                background: #f8f9fa;
                border: 2px dashed #dee2e6;
                border-radius: 4px;
                color: #6c757d;
                font-size: 16px;
            ">
                Bir rapor seçin
            </div>
        </div>
    </div>

    <script>
        function loadReport(reportType) {
            var iframe = document.getElementById('report_iframe');
            var placeholder = document.getElementById('report-placeholder');

            // Placeholder'ı gizle, iframe'i göster
            placeholder.style.display = 'none';
            iframe.style.display = 'block';

            // URL'yi burada güncelleyeceğiz - şimdilik placeholder
            iframe.src = 'data:text/html,<html><body style="font-family:Arial;padding:40px;text-align:center;"><h2>' + reportType + '</h2><p>Rapor yükleniyor...</p></body></html>';
        }
    </script>
    <?php
}
function raporlar_yonetim_cb() {
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="reports-toolbar" style="
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            <div class="report-button" onclick="loadReport('satis-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-chart-bar" style="font-size: 24px; color: #0073aa; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Satış Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('musteri-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-groups" style="font-size: 24px; color: #28a745; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Müşteri Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('gelir-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-money-alt" style="font-size: 24px; color: #ffc107; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Gelir Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('performans-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-awards" style="font-size: 24px; color: #dc3545; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Performans Raporu</span>
            </div>

            <div class="report-button" onclick="loadReport('stok-raporu')" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 10px;
                background: white;
                border: 1px solid #ccc;
                border-radius: 6px;
                cursor: pointer;
                min-width: 80px;
                transition: all 0.2s;
            " onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='white'">
                <span class="dashicons dashicons-admin-page" style="font-size: 24px; color: #6f42c1; margin-bottom: 5px;"></span>
                <span style="font-size: 11px; text-align: center; font-weight: 500;">Stok Raporu</span>
            </div>
        </div>

        <!-- Iframe container -->
        <div style="position: relative; height: calc(100vh - 280px); margin-top: 20px;">
            <iframe id="report_iframe"
                    src=""
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; border-radius: 4px; display: none;">
            </iframe>
            <div id="report-placeholder" style="
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                background: #f8f9fa;
                border: 2px dashed #dee2e6;
                border-radius: 4px;
                color: #6c757d;
                font-size: 16px;
            ">
                Bir rapor seçin
            </div>
        </div>
    </div>

    <script>
        function loadReport(reportType) {
            var iframe = document.getElementById('report_iframe');
            var placeholder = document.getElementById('report-placeholder');

            // Placeholder'ı gizle, iframe'i göster
            placeholder.style.display = 'none';
            iframe.style.display = 'block';

            // URL'yi burada güncelleyeceğiz - şimdilik placeholder
            iframe.src = 'data:text/html,<html><body style="font-family:Arial;padding:40px;text-align:center;"><h2>' + reportType + '</h2><p>Rapor yükleniyor...</p></body></html>';
        }
    </script>
    <?php
}
function araclar_cb()
{
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="tools-toolbar" style="
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            <!-- Tools Buttons -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">

                <!-- Sophos Siparişler -->
                <div class="tools-button" onclick="loadToolModule('sophos_siparisler')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/logos/sophos.png" style="width: 24px; height: 24px; margin-bottom: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="dashicons dashicons-cart" style="font-size: 24px; color: #ff6b35; margin-bottom: 6px; display: none;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">Sophos<br>Siparişler</span>
                </div>

                <!-- Marka Account Manager Silme -->
                <div class="tools-button" onclick="loadToolModule('marka_account_manager_silme')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-trash" style="font-size: 24px; color: #f44336; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">Marka Account<br>Manager Silme</span>
                </div>

                <!-- Acronis Faturalama -->
                <div class="tools-button" onclick="loadToolModule('acronis_faturalama')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/logos/acronis.png" style="width: 24px; height: 24px; margin-bottom: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="dashicons dashicons-money-alt" style="font-size: 24px; color: #28a745; margin-bottom: 6px; display: none;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">Acronis<br>Faturalama</span>
                </div>

                <!-- Sophos Faturalama -->
                <div class="tools-button" onclick="loadToolModule('sophos_faturalama')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff9800';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/logos/sophos.png" style="width: 24px; height: 24px; margin-bottom: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="dashicons dashicons-money" style="font-size: 24px; color: #ff6b35; margin-bottom: 6px; display: none;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">Sophos<br>Faturalama</span>
                </div>

                <!-- MediaMarkt Faturalama -->
                <div class="tools-button" onclick="loadToolModule('mediamarkt_faturalama')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/logos/mediamarkt.png" style="width: 24px; height: 24px; margin-bottom: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="dashicons dashicons-store" style="font-size: 24px; color: #e60012; margin-bottom: 6px; display: none;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">MediaMarkt<br>Faturalama</span>
                </div>

                <!-- Vatan Faturalama -->
                <div class="tools-button" onclick="loadToolModule('vatan_faturalama')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e1f5fe'; this.style.borderColor='#2196f3';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/logos/vatan.png" style="width: 24px; height: 24px; margin-bottom: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="dashicons dashicons-building" style="font-size: 24px; color: #2196f3; margin-bottom: 6px; display: none;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; line-height: 1.3;">Vatan<br>Faturalama</span>
                </div>

            </div>
        </div>

        <!-- Content Area -->
        <div id="tools-content-area" style="position: relative; height: calc(100vh - 280px); padding: 40px; text-align: center; color: #666; font-size: 16px;">
<?php echo __('Bir araç modülü seçin','komtera'); ?>
        </div>
    </div>

    <script>
        function loadToolModule(moduleName) {
            var contentArea = document.getElementById('tools-content-area');
            contentArea.innerHTML = '<div style="padding: 40px; text-align: center;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div><br><br>Yükleniyor...</div>';

            // AJAX request to load module content
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        contentArea.innerHTML = xhr.responseText;

                        // Script taglarını çalıştır
                        var scripts = contentArea.querySelectorAll('script');
                        scripts.forEach(function(oldScript) {
                            var newScript = document.createElement('script');
                            if (oldScript.src) {
                                newScript.src = oldScript.src;
                            } else {
                                newScript.textContent = oldScript.textContent;
                            }
                            if (oldScript.type) {
                                newScript.type = oldScript.type;
                            }
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                    } else {
                        contentArea.innerHTML = '<div style="padding: 40px; text-align: center; color: #f44336;"><?php echo __('Modül yüklenirken hata oluştu','komtera'); ?></div>';
                    }
                }
            };

            xhr.send('action=load_tools_module&module=' + encodeURIComponent(moduleName) + '&nonce=<?php echo wp_create_nonce("load_tool_module_nonce"); ?>');
        }
    </script>

    <!-- Button Styles -->
    <style>
        .tools-button {
            transition: all 0.2s ease;
            white-space: nowrap;
            height: 60px;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }
        .tools-button:hover {
            transform: translateY(-1px);
        }
        .tools-button .dashicons {
            font-size: 24px !important;
            margin-bottom: 6px !important;
        }
        .tools-button span:last-child {
            font-size: 11px !important;
            text-align: center !important;
            font-weight: 500 !important;
            color: #333 !important;
            height: 24px !important;
            line-height: 1.3 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        @media (max-width: 768px) {
            .tools-button {
                font-size: 9px !important;
                padding: 8px !important;
                margin-right: 8px !important;
                margin-bottom: 8px;
                height: 40px !important;
            }
            .tools-button .dashicons {
                font-size: 20px !important;
                margin-bottom: 8px !important;
            }
        }
    </style>
    <?php
}
function fiyat_listesi_cb() {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=fiyat_listesi';
    ?>
    <script>
        let selectedMarka = '';
        const baseUrl = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php";
        const serviceUrl = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service";

        // Sayfa yüklendiğinde son seçili markayı yükle
        window.addEventListener('DOMContentLoaded', function() {
            const lastMarka = localStorage.getItem('fiyat_listesi_son_marka');
            if (lastMarka) {
                selectMarka(lastMarka);
            }
        });

        function showMarkaPopup() {
            document.getElementById('marka_popup').style.display = 'block';
            loadMarkalar();
        }

        function closeMarkaPopup() {
            document.getElementById('marka_popup').style.display = 'none';
        }

        function loadMarkalar() {
            const container = document.getElementById('marka_list_container');

            fetch(serviceUrl + '/get_markalar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let html = '';

                        // Marka listesi (kayıt sayısı ile)
                        data.markalar.forEach(marka => {
                            html += `<div class="marka-item" onclick="selectMarka('${marka.MARKA}')" style="
                                padding: 12px 15px;
                                margin-bottom: 8px;
                                background: #f8f9fa;
                                border: 1px solid #dee2e6;
                                border-radius: 6px;
                                cursor: pointer;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                transition: all 0.2s;
                                " onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#0073aa';" onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.borderColor='#dee2e6';">
                                <span style="font-weight: 500; color: #333;">${marka.MARKA}</span>
                                <span style="color: #666; font-size: 12px; background: white; padding: 4px 10px; border-radius: 12px;">${marka.KAYIT_SAYISI} kayıt</span>
                            </div>`;
                        });

                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">Marka bulunamadı</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading markalar:', error);
                    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #f44336;">Hata: Markalar yüklenemedi</div>';
                });
        }

        function selectMarka(marka) {
            selectedMarka = marka;
            updateIframeSrc();
            closeMarkaPopup();

            // localStorage'a kaydet
            if (marka) {
                localStorage.setItem('fiyat_listesi_son_marka', marka);
            }

            // Seçili marka göstergesini güncelle
            const display = document.getElementById('selected_marka_display');
            const text = document.getElementById('selected_marka_text');
            if (marka) {
                text.textContent = marka;
                display.style.display = 'flex';
            } else {
                display.style.display = 'none';
            }
        }

        function updateIframeSrc() {
            const iframe = document.getElementById('erp_iframe');
            let url = baseUrl + '?t=fiyat_listesi';

            if (selectedMarka) {
                url += '&marka=' + encodeURIComponent(selectedMarka);
            }

            iframe.src = url;
        }

        function exceldenAl() {
            if (!selectedMarka) {
                alert('Lütfen önce bir marka seçiniz!');
                return;
            }

            if (!confirm('Excel dosyası "' + selectedMarka + '" markasına yüklenecek. Devam etmek istiyor musunuz?')) {
                return;
            }

            const iframe = document.getElementById('erp_iframe');
            try {
                const iframeWindow = iframe.contentWindow;
                if (iframeWindow && iframeWindow.ExceldenAl) {
                    iframeWindow.ExceldenAl(selectedMarka);
                } else {
                    console.error('ExceldenAl fonksiyonu iframe içinde bulunamadı');
                }
            } catch (e) {
                console.error('Iframe erişim hatası:', e);
            }
        }

        function exceleyeGonder() {
            if (!selectedMarka) {
                alert('Lütfen önce bir marka seçiniz!');
                return;
            }

            const iframe = document.getElementById('erp_iframe');
            try {
                const iframeWindow = iframe.contentWindow;
                if (iframeWindow && iframeWindow.ExcelKaydet) {
                    iframeWindow.ExcelKaydet(selectedMarka);
                } else {
                    console.error('ExcelKaydet fonksiyonu iframe içinde bulunamadı');
                }
            } catch (e) {
                console.error('Iframe erişim hatası:', e);
            }
        }

        function markaEkle() {
            document.getElementById('add_marka_modal').style.display = 'block';
            document.getElementById('new_marka_input').value = '';
            document.getElementById('marka_warning').style.display = 'none';
            document.getElementById('new_marka_input').focus();
        }

        function closeMarkaEkleModal() {
            document.getElementById('add_marka_modal').style.display = 'none';
        }

        function checkMarkaInput() {
            const input = document.getElementById('new_marka_input');
            const warning = document.getElementById('marka_warning');
            const value = input.value;

            // Küçük harf var mı kontrol et
            if (value !== value.toUpperCase()) {
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }
        }

        function submitYeniMarka() {
            const input = document.getElementById('new_marka_input');
            const marka = input.value.trim();

            if (!marka) {
                alert('Lütfen marka adı giriniz!');
                return;
            }

            // Küçük harf kontrolü
            if (marka !== marka.toUpperCase()) {
                alert('Hata: Marka adı BÜYÜK HARF olmalıdır!');
                return;
            }

            // Markayı ekle
            fetch(serviceUrl + '/add_marka.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({marka: marka})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeMarkaEkleModal();
                    alert('Marka başarıyla eklendi: ' + marka + '\n\nŞimdi "Excel\'den Al" ile fiyat listesini yükleyebilirsiniz.');
                    selectMarka(marka);
                    // Markalar popup'ını aç ve yeni markayı göster
                    setTimeout(function() {
                        showMarkaPopup();
                    }, 300);
                } else {
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Marka ekleme hatası:', error);
                alert('Marka eklenirken hata oluştu: ' + error);
            });
        }

        function markaSil() {
            document.getElementById('delete_marka_modal').style.display = 'block';
            loadMarkalarForDelete();
        }

        function closeMarkaSilModal() {
            document.getElementById('delete_marka_modal').style.display = 'none';
        }

        function loadMarkalarForDelete() {
            const container = document.getElementById('delete_marka_list_container');
            container.innerHTML = '<div style="text-align: center; padding: 20px;"><div style="display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #d32f2f; border-radius: 50%; animation: spin 1s linear infinite;"></div><br><br>Markalar yükleniyor...</div>';

            fetch(serviceUrl + '/get_markalar.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.markalar.length > 0) {
                        let html = '';
                        data.markalar.forEach(marka => {
                            html += `<div class="marka-delete-item" onclick="confirmDeleteMarka('${marka.MARKA}', ${marka.KAYIT_SAYISI})" style="
                                padding: 12px 15px;
                                margin-bottom: 8px;
                                background: #fff;
                                border: 1px solid #ddd;
                                border-radius: 6px;
                                cursor: pointer;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                transition: all 0.2s;
                                " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#d32f2f';" onmouseout="this.style.backgroundColor='#fff'; this.style.borderColor='#ddd';">
                                <span style="font-weight: 500; color: #333;">${marka.MARKA}</span>
                                <span style="color: #666; font-size: 12px;">${marka.KAYIT_SAYISI} kayıt</span>
                            </div>`;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;">Marka bulunamadı</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading markalar:', error);
                    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #d32f2f;">Hata: Markalar yüklenemedi</div>';
                });
        }

        function confirmDeleteMarka(marka, kayitSayisi) {
            if (confirm(`"${marka}" markasını silmek istediğinize emin misiniz?\n\n${kayitSayisi} adet kayıt silinecektir!\n\nBu işlem geri alınamaz!`)) {
                deleteMarka(marka);
            }
        }

        function deleteMarka(marka) {
            fetch(serviceUrl + '/delete_marka.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({marka: marka})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Marka başarıyla silindi: ' + marka + '\n\n' + data.deleted + ' kayıt silindi.');
                    closeMarkaSilModal();
                    // Eğer silinen marka seçiliyse, seçimi temizle
                    if (selectedMarka === marka) {
                        selectedMarka = '';
                        document.getElementById('selected_marka_display').style.display = 'none';
                        document.getElementById('erp_iframe').src = '';
                    }
                    // Markalar listesini güncelle
                    loadMarkalar();
                } else {
                    alert('Hata: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Marka silme hatası:', error);
                alert('Marka silinirken hata oluştu: ' + error);
            });
        }

        // Popup dışına tıklandığında kapat
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('marka_popup').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeMarkaPopup();
                }
            });
        });

        function showHelpModal() {
            document.getElementById('help_modal').style.display = 'block';
        }

        function closeHelpModal() {
            document.getElementById('help_modal').style.display = 'none';
        }

        // ESC ile help modal'ı kapat
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('help_modal').style.display === 'block') {
                closeHelpModal();
            }
        });
    </script>
    <style>
        .help-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .help-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 70%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        .help-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            line-height: 20px;
            cursor: pointer;
        }

        .help-modal-close:hover,
        .help-modal-close:focus {
            color: #000;
        }

        .help-modal h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .help-modal h3 {
            color: #555;
            margin-top: 20px;
        }

        .help-modal ul {
            line-height: 1.8;
        }

        .help-modal code {
            background-color: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="pricelist-toolbar" style="
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
            <!-- Excel tarzı butonlar -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">

                <!-- Markalar Popup Butonu -->
                <div class="pricelist-button" onclick="showMarkaPopup()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#f0f8ff'; this.style.borderColor='#0073aa';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-list-view" style="font-size: 24px; color: #0073aa; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Markalar</span>
                </div>

                <!-- Excel'e Gönder Butonu -->
                <div class="pricelist-button" onclick="exceleyeGonder()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-upload" style="font-size: 24px; color: #4caf50; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('Excel\'e Gönder','komtera'); ?></span>
                </div>

                <!-- Excel'den Al Butonu -->
                <div class="pricelist-button" onclick="exceldenAl()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-download" style="font-size: 24px; color: #f44336; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('Excel\'den Al','komtera'); ?></span>
                </div>

                <!-- Marka Ekle Butonu -->
                <div class="pricelist-button" onclick="markaEkle()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff9800';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-plus-alt2" style="font-size: 24px; color: #ff9800; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Marka Ekle</span>
                </div>

                <!-- Marka Sil Butonu -->
                <div class="pricelist-button" onclick="markaSil()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#d32f2f';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-trash" style="font-size: 24px; color: #d32f2f; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Marka Sil</span>
                </div>

                <!-- Spacer Sol -->
                <div style="flex: 1;"></div>

                <!-- Seçili Marka Göstergesi -->
                <div id="selected_marka_display" style="
                        display: none;
                        align-items: center;
                        justify-content: center;
                        padding: 12px 15px;
                        background: #e8f5e8;
                        border: 1px solid #4caf50;
                        border-radius: 6px;
                        color: #2e7d32;
                        font-weight: bold;
                        font-size: 13px;
                        height: 70px;
                        width: 200px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        box-sizing: border-box;
                        ">
                    <span class="dashicons dashicons-yes" style="margin-right: 8px; font-size: 20px;"></span>
                    <span id="selected_marka_text">Tüm Markalar</span>
                </div>

                <!-- Spacer Sağ -->
                <div style="flex: 1;"></div>

                <!-- Yardım Butonu (En Sağda) -->
                <div class="pricelist-button" onclick="showHelpModal()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#2196f3';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-editor-help" style="font-size: 24px; color: #2196f3; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('Yardım','komtera'); ?></span>
                </div>
            </div>
        </div>

        <!-- Marka Popup -->
        <div id="marka_popup" style="
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 10000;
                ">
            <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 8px;
                    padding: 20px;
                    max-width: 400px;
                    width: 90%;
                    max-height: 80vh;
                    overflow-y: auto;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                    ">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <h3 style="margin: 0; color: #333;">Marka Seçiniz</h3>
                    <button onclick="closeMarkaPopup()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">&times;</button>
                </div>
                <div id="marka_list_container">
                    <div style="text-align: center; padding: 20px; color: #666;">
                        <div style="display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                        <br><br>Markalar yükleniyor...
                    </div>
                </div>
            </div>
        </div>

        <!-- Marka Ekle Modal -->
        <div id="add_marka_modal" style="
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 10001;
                " onclick="if(event.target === this) closeMarkaEkleModal();">
            <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 8px;
                    padding: 25px;
                    max-width: 450px;
                    width: 90%;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                    " onclick="event.stopPropagation();">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #ff9800; padding-bottom: 15px;">
                    <h3 style="margin: 0; color: #333;"><span class="dashicons dashicons-plus-alt2" style="color: #ff9800;"></span> Yeni Marka Ekle</h3>
                    <button onclick="closeMarkaEkleModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">&times;</button>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="new_marka_input" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Marka Adı:</label>
                    <input type="text"
                           id="new_marka_input"
                           oninput="checkMarkaInput()"
                           onkeypress="if(event.key === 'Enter') submitYeniMarka()"
                           style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;"
                           placeholder="Örn: ACRONIS">

                    <div id="marka_warning" style="
                            display: none;
                            margin-top: 10px;
                            padding: 10px;
                            background: #fff3cd;
                            border: 1px solid #ffc107;
                            border-radius: 4px;
                            color: #856404;
                            font-size: 13px;
                            ">
                        <span class="dashicons dashicons-warning" style="color: #ff9800; font-size: 16px; vertical-align: middle;"></span>
                        <strong>UYARI:</strong> Marka adı BÜYÜK HARF olmalıdır!
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button onclick="closeMarkaEkleModal()" style="
                            padding: 10px 20px;
                            background: #6c757d;
                            color: white;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            ">İptal</button>
                    <button onclick="submitYeniMarka()" style="
                            padding: 10px 20px;
                            background: #ff9800;
                            color: white;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            font-weight: 500;
                            ">Ekle</button>
                </div>
            </div>
        </div>

        <!-- Marka Sil Modal -->
        <div id="delete_marka_modal" style="
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 10001;
                " onclick="if(event.target === this) closeMarkaSilModal();">
            <div style="
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 8px;
                    padding: 25px;
                    max-width: 450px;
                    width: 90%;
                    max-height: 70vh;
                    overflow-y: auto;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                    " onclick="event.stopPropagation();">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid #d32f2f; padding-bottom: 15px;">
                    <h3 style="margin: 0; color: #333;"><span class="dashicons dashicons-trash" style="color: #d32f2f;"></span> Marka Sil</h3>
                    <button onclick="closeMarkaSilModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">&times;</button>
                </div>

                <div style="margin-bottom: 15px; padding: 8px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; font-size: 13px;">
                    <span class="dashicons dashicons-warning" style="color: #ff9800; font-size: 16px; vertical-align: middle;"></span>
                    <strong>UYARI:</strong> Silinecek markayı listeden seçiniz. Markaya ait tüm kayıtlar silinecektir!
                </div>

                <div id="delete_marka_list_container" style="margin-bottom: 15px;">
                    <div style="text-align: center; padding: 20px; color: #666;">
                        Markalar yükleniyor...
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button onclick="closeMarkaSilModal()" style="
                            padding: 10px 20px;
                            background: #6c757d;
                            color: white;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 14px;
                            ">İptal</button>
                </div>
            </div>
        </div>

        <!-- Help Modal -->
        <div id="help_modal" class="help-modal" onclick="if(event.target===this) closeHelpModal()">
            <div class="help-modal-content">
                <span class="help-modal-close" onclick="closeHelpModal()">&times;</span>
                <h2><?php echo __('Fiyat Listesi Yardım','komtera'); ?></h2>

                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                    <strong style="color: #856404; display: flex; align-items: center; margin-bottom: 8px;">
                        <span class="dashicons dashicons-warning" style="color: #ff9800; font-size: 20px; margin-right: 8px;"></span>
                        <?php echo __('Önemli Not','komtera'); ?>
                    </strong>
                    <p style="margin: 0; color: #856404; line-height: 1.6;">
                        <?php echo __('Excel uyuşmazlıklarını engellemek için, markayı buradan export edin, o Excel içine yapıştırın, düzenleyin, silin, ekleyin, sonra o Excel\'i import edin.','komtera'); ?>
                    </p>
                </div>

                <h3><?php echo __('Sayfa Hakkında','komtera'); ?></h3>
                <p><?php echo __('Bu sayfa seçilen markanın fiyat listesini görüntülemek ve yönetmek için kullanılır.','komtera'); ?></p>

                <h3><?php echo __('Butonlar','komtera'); ?></h3>
                <ul>
                    <li><strong><?php echo __('Markalar','komtera'); ?>:</strong> <?php echo __('Marka listesini görüntülemek ve bir marka seçmek için kullanılır.','komtera'); ?></li>
                    <li><strong><?php echo __('Excel\'e Gönder','komtera'); ?>:</strong> <?php echo __('Seçili markanın fiyat listesini Excel dosyası olarak indirir.','komtera'); ?></li>
                    <li><strong><?php echo __('Excel\'den Al','komtera'); ?>:</strong> <?php echo __('Excel dosyasından seçili markaya fiyat listesi yükler.','komtera'); ?></li>
                    <li><strong><?php echo __('Marka Ekle','komtera'); ?>:</strong> <?php echo __('Sisteme yeni bir marka ekler.','komtera'); ?></li>
                    <li><strong><?php echo __('Marka Sil','komtera'); ?>:</strong> <?php echo __('Seçili markayı ve tüm kayıtlarını siler.','komtera'); ?></li>
                </ul>

                <h3><?php echo __('Grid Özellikleri','komtera'); ?></h3>
                <ul>
                    <li><strong><?php echo __('Sayfalama','komtera'); ?>:</strong> <?php echo __('Sayfa başına 100, 1000 veya 10000 kayıt görüntüleyebilirsiniz. Büyük veri setleri için hızlı yükleme sağlar.','komtera'); ?></li>
                    <li><strong><?php echo __('Filtreleme','komtera'); ?>:</strong> <?php echo __('Her kolon başlığının altında filtreleme alanları bulunur. SKU, ürün açıklaması, tip vb. alanlara göre filtreleme yapabilirsiniz.','komtera'); ?></li>
                    <li><strong><?php echo __('Sıralama','komtera'); ?>:</strong> <?php echo __('Kolon başlıklarına tıklayarak artan veya azalan sıralama yapabilirsiniz.','komtera'); ?></li>
                    <li><strong><?php echo __('Sabit Kolon','komtera'); ?>:</strong> <?php echo __('SKU kolonu sabit tutulmuştur, yatay kaydırma sırasında her zaman görünür.','komtera'); ?></li>
                </ul>

                <h3><?php echo __('Kolonlar','komtera'); ?></h3>
                <ul>
                    <li><strong>SKU:</strong> <?php echo __('Ürün stok kodu','komtera'); ?></li>
                    <li><strong><?php echo __('Açıklama','komtera'); ?>:</strong> <?php echo __('Ürün açıklaması','komtera'); ?></li>
                    <li><strong><?php echo __('Tip','komtera'); ?>:</strong> <?php echo __('Ürün tipi (Hardware ürünler sarı arka plan ile gösterilir)','komtera'); ?></li>
                    <li><strong><?php echo __('Çözüm','komtera'); ?>:</strong> <?php echo __('Ürün çözüm kategorisi','komtera'); ?></li>
                    <li><strong><?php echo __('Süre','komtera'); ?>:</strong> <?php echo __('Lisans süresi','komtera'); ?></li>
                    <li><strong><?php echo __('Fiyat','komtera'); ?>:</strong> <?php echo __('Liste fiyatı','komtera'); ?></li>
                    <li><strong>UpLift:</strong> <?php echo __('UpLift fiyatı','komtera'); ?></li>
                    <li><strong>PB:</strong> <?php echo __('Para birimi','komtera'); ?></li>
                </ul>

                <h3><?php echo __('İpuçları','komtera'); ?></h3>
                <ul>
                    <li><?php echo __('SKU filtresinde "begin" koşulu kullanılır, başlangıç karakterlerini yazarak arama yapabilirsiniz.','komtera'); ?></li>
                    <li><?php echo __('Açıklama filtresinde "contain" koşulu kullanılır, herhangi bir kelime ile arama yapabilirsiniz.','komtera'); ?></li>
                    <li><?php echo __('Kolon genişliklerini sürükleyerek ayarlayabilirsiniz.','komtera'); ?></li>
                    <li><?php echo __('Marka ekleme sırasında marka adı BÜYÜK HARF olmalıdır.','komtera'); ?></li>
                </ul>
            </div>
        </div>

        <!-- Iframe container -->
        <div style="position: relative; height: calc(100vh - 280px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; border-radius: 4px; position: absolute; top: 0; left: 0;">
            </iframe>
        </div>
    </div>

    <!-- Button Styles -->
    <style>
        .pricelist-button {
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .pricelist-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .pricelist-button {
                font-size: 9px !important;
                padding: 4px 6px !important;
                margin-right: 4px !important;
                margin-bottom: 4px;
            }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <?php
}
function yenilemeler_cb() {
    $base_src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=';
    // Get the table parameter from URL, default to 'yenilemeler'
    $selected_table = isset($_GET['table']) ? sanitize_text_field($_GET['table']) : 'yenilemeler';
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="renewals-toolbar" style="
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                ">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                <!-- Yenilemeler Butonu -->
                <div class="renewals-button table-btn <?php echo ($selected_table === 'yenilemeler') ? 'active' : ''; ?>" data-table="yenilemeler" data-icon-color="#1976d2" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'yenilemeler') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'yenilemeler') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'yenilemeler') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-update" style="font-size: 24px; color: <?php echo ($selected_table === 'yenilemeler') ? 'white' : '#1976d2'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'yenilemeler') ? 'white' : '#333'; ?> !important;"><?php echo __('Yenilemeler', 'komtera'); ?></span>
                </div>

                <!-- Yenilemeler Liste Butonu -->
                <div class="renewals-button table-btn <?php echo ($selected_table === 'yenilemeler_liste') ? 'active' : ''; ?>" data-table="yenilemeler_liste" data-icon-color="#9c27b0" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === 'yenilemeler_liste') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === 'yenilemeler_liste') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === 'yenilemeler_liste') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-list-view" style="font-size: 24px; color: <?php echo ($selected_table === 'yenilemeler_liste') ? 'white' : '#9c27b0'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === 'yenilemeler_liste') ? 'white' : '#333'; ?>;"><?php echo __('Yenilemeler Liste', 'komtera'); ?></span>
                </div>

                <!-- 60 Gün Liste Butonu -->
                <div class="renewals-button table-btn <?php echo ($selected_table === '60gun_liste') ? 'active' : ''; ?>" data-table="60gun_liste" data-icon-color="#ff9800" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 8px;
                        background: <?php echo ($selected_table === '60gun_liste') ? '#0073aa' : 'white'; ?>;
                        border: <?php echo ($selected_table === '60gun_liste') ? '2px solid #0073aa' : '1px solid #ccc'; ?>;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: <?php echo ($selected_table === '60gun_liste') ? '0 2px 4px rgba(0,115,170,0.2)' : '0 1px 3px rgba(0,0,0,0.1)'; ?>;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff9800'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-calendar-alt" style="font-size: 24px; color: <?php echo ($selected_table === '60gun_liste') ? 'white' : '#ff9800'; ?>; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: <?php echo ($selected_table === '60gun_liste') ? 'white' : '#333'; ?>;"><?php echo __('60 Gün Liste', 'komtera'); ?></span>
                </div>
            </div>
        </div>
        <div style="position: relative; height: calc(100vh - 180px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($base_src . $selected_table); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <script>
        (function() {
            const iframe = document.getElementById('erp_iframe');
            const baseDir = "<?php echo esc_js($base_src); ?>";
            const buttons = document.querySelectorAll('.table-btn');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const table = this.getAttribute('data-table');

                    // Update button states - Excel style
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.style.background = 'white';
                        btn.style.border = '1px solid #ccc';
                        btn.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';

                        // Reset icon color from data attribute
                        const icon = btn.querySelector('.dashicons');
                        const text = btn.querySelector('span:last-child');
                        const iconColor = btn.getAttribute('data-icon-color');

                        if (icon && iconColor) {
                            icon.style.color = iconColor;
                        }
                        if (text) {
                            text.style.color = '#333';
                        }
                    });

                    // Set active button style
                    this.classList.add('active');
                    this.style.background = '#0073aa';
                    this.style.border = '2px solid #0073aa';
                    this.style.boxShadow = '0 2px 4px rgba(0,115,170,0.2)';
                    const activeIcon = this.querySelector('.dashicons');
                    const activeText = this.querySelector('span:last-child');
                    if (activeIcon) activeIcon.style.color = 'white';
                    if (activeText) activeText.style.color = 'white';

                    // Update URL with table parameter
                    const url = new URL(window.location.href);
                    url.searchParams.set('table', table);
                    window.history.pushState({}, '', url);

                    // Update iframe source
                    iframe.src = baseDir + table;
                });
            });
        })();
    </script>
    <?php
}
function faturalar_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=faturalar';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border: 1px solid #ccc; position: absolute; top: 0; left: 0;">
            </iframe>
        </div>
    </div>
    <?php
}
function stoklar_cb()       {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=stoklar_satis';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function bayiler_cb()       {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=_bayiler';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function musteriler_cb()    {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=musteriler';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function ayarlar_cb()
{
    ?>
    <div class="wrap">
        <!-- Excel style toolbar -->
        <div class="settings-toolbar" style="
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            <!-- Settings Buttons -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">

                <!-- Marka Bazlı Bayi Seviyeleri -->
                <div class="settings-button" onclick="loadModule('marka_bazli_bayi_seviyeleri')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="if(!this.classList.contains('active-module')){this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2';}" onmouseout="if(!this.classList.contains('active-module')){this.style.backgroundColor='white'; this.style.borderColor='#ccc';}">
                    <span class="dashicons dashicons-building" style="font-size: 24px; color: #0073aa; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; height: 24px; line-height: 1.3; display: flex; align-items: center;">Marka Bazlı<br>Bayi Seviyeleri</span>
                </div>

                <!-- Etkinlikler -->
                <div class="settings-button" onclick="loadModule('etkinlikler')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="if(!this.classList.contains('active-module')){this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';}" onmouseout="if(!this.classList.contains('active-module')){this.style.backgroundColor='white'; this.style.borderColor='#ccc';}">
                    <span class="dashicons dashicons-calendar-alt" style="font-size: 24px; color: #4caf50; margin-bottom: 4px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Etkinlikler</span>
                </div>

                <!-- Kampanyalar -->
                <div class="settings-button" onclick="loadModule('kampanyalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff9800';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-megaphone" style="font-size: 24px; color: #ff9800; margin-bottom: 4px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Kampanyalar</span>
                </div>

                <!-- Teklif Notu -->
                <div class="settings-button" onclick="loadModule('teklif_notu')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-edit-page" style="font-size: 24px; color: #9c27b0; margin-bottom: 4px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Teklif Notu</span>
                </div>

                <!-- Teklif Reklamı - Geçici olarak kaldırıldı -->
                <!--
                <div class="settings-button" onclick="loadModule('teklif_reklami')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-format-image" style="font-size: 24px; color: #f44336; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Teklif Reklamı</span>
                </div>
                -->

                <!-- Bankalar -->
                <div class="settings-button" onclick="loadModule('bankalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e1f5fe'; this.style.borderColor='#2196f3';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-money-alt" style="font-size: 24px; color: #2196f3; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Bankalar</span>
                </div>

                <!-- Marka Satış Hedefleri -->
                <div class="settings-button" onclick="loadModule('marka_satis_hedefleri')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#f1f8e9'; this.style.borderColor='#8bc34a';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-chart-line" style="font-size: 24px; color: #8bc34a; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Marka Satış<br>Hedefleri</span>
                </div>

                <!-- Onaylar (Kar Oranları) -->
                <div class="settings-button" onclick="loadModule('onaylar_kar_oranlari')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#fce4ec'; this.style.borderColor='#e91e63';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 24px; color: #e91e63; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Onaylar<br>(Kar Oranları)</span>
                </div>

                <!-- Onaylar (Diğer) -->
                <div class="settings-button" onclick="loadModule('onaylar_diger')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-admin-generic" style="font-size: 24px; color: #795548; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Onaylar<br>(Diğer)</span>
                </div>

                <!-- Müşteri Temsilcisi Değiştir -->
                <div class="settings-button" onclick="loadModule('musteri_temsilcisi_degistir')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#fff8e1'; this.style.borderColor='#ffc107';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-admin-users" style="font-size: 24px; color: #ffc107; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Müşteri Temsilcisi<br>Değiştir</span>
                </div>

                <!-- Sophos Cari Kod EDI Eşleşmesi - Geçici olarak kaldırıldı -->
                <!--
                <div class="settings-button" onclick="loadModule('sophos_cari_kod_edi_eslesmesi')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-networking" style="font-size: 24px; color: #607d8b; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Sophos Cari Kod<br>EDI Eşleşmesi</span>
                </div>
                -->

                <!-- TL Faturalanmayacak Markalar -->
                <div class="settings-button" onclick="loadModule('tl_faturalanmayacak_markalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 8px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-dismiss" style="font-size: 24px; color: #f44336; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">TL Faturalanacak<br>Markalar</span>
                </div>

            </div>
        </div>

        <!-- Content Area -->
        <div id="content-area" style="position: relative; height: calc(100vh - 280px); padding: 0; text-align: center; color: #666; font-size: 16px;">
<?php echo __('Bir ayar modülü seçin','komtera'); ?>
        </div>
    </div>

    <script>
        function loadModule(moduleName) {
            var contentArea = document.getElementById('content-area');
            contentArea.innerHTML = '<div style="padding: 40px; text-align: center;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div><br><br>Yükleniyor...</div>';

            // Remove active class from all buttons
            document.querySelectorAll('.settings-button').forEach(function(btn) {
                btn.classList.remove('active-module');
                btn.style.background = 'white';
                btn.style.border = '1px solid #ccc';
            });

            // Add active class to clicked button
            event.currentTarget.classList.add('active-module');
            event.currentTarget.style.background = '#e8f5e9';
            event.currentTarget.style.border = '2px solid #4caf50';

            // Update URL with module parameter
            const url = new URL(window.location.href);
            url.searchParams.set('module', moduleName);
            window.history.pushState({}, '', url);

            // AJAX request to load module content
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        contentArea.innerHTML = xhr.responseText;

                        // Script taglarını çalıştır
                        var scripts = contentArea.querySelectorAll('script');
                        scripts.forEach(function(oldScript) {
                            var newScript = document.createElement('script');
                            if (oldScript.src) {
                                newScript.src = oldScript.src;
                            } else {
                                newScript.textContent = oldScript.textContent;
                            }
                            if (oldScript.type) {
                                newScript.type = oldScript.type;
                            }
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                    } else {
                        contentArea.innerHTML = '<div style="padding: 40px; text-align: center; color: #f44336;"><?php echo __('Modül yüklenirken hata oluştu','komtera'); ?></div>';
                    }
                }
            };

            xhr.send('action=load_settings_module&module=' + encodeURIComponent(moduleName) + '&nonce=<?php echo wp_create_nonce("load_module_nonce"); ?>');
        }

        // Load module from URL on page load
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const module = urlParams.get('module');
            if (module) {
                // Find and highlight the button for this module
                const buttons = document.querySelectorAll('.settings-button');
                buttons.forEach(function(btn) {
                    const onclick = btn.getAttribute('onclick');
                    if (onclick && onclick.includes(module)) {
                        btn.classList.add('active-module');
                        btn.style.background = '#e8f5e9';
                        btn.style.border = '2px solid #4caf50';
                    }
                });

                // Load the module without triggering onclick
                var contentArea = document.getElementById('content-area');
                contentArea.innerHTML = '<div style="padding: 40px; text-align: center;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div><br><br>Yükleniyor...</div>';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', ajaxurl, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        contentArea.innerHTML = xhr.responseText;
                    }
                };

                xhr.send('action=load_settings_module&module=' + encodeURIComponent(module) + '&nonce=<?php echo wp_create_nonce("load_module_nonce"); ?>');
            }
        });
    </script>

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Button Styles -->
    <style>
        .settings-button {
            transition: all 0.2s ease;
            white-space: nowrap;
            height: 60px; /* Sabit yükseklik */
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }
        .settings-button:hover {
            transform: translateY(-1px);
        }
        .settings-button .dashicons {
            font-size: 24px !important;
            margin-bottom: 10px !important;
        }
        .settings-button span:last-child {
            font-size: 11px !important;
            text-align: center !important;
            font-weight: 500 !important;
            color: #333 !important;
            height: 24px !important;
            line-height: 1.3 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        @media (max-width: 768px) {
            .settings-button {
                font-size: 9px !important;
                padding: 8px !important;
                margin-right: 8px !important;
                margin-bottom: 8px;
                height: 40px !important;
            }
            .settings-button .dashicons {
                font-size: 20px !important;
                margin-bottom: 8px !important;
            }
        }
    </style>
    <?php
}

// AJAX handler for loading settings modules
add_action('wp_ajax_load_settings_module', 'handle_load_settings_module');
add_action('wp_ajax_nopriv_load_settings_module', 'handle_load_settings_module');

function handle_load_settings_module() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'load_module_nonce')) {
        wp_die('Güvenlik hatası');
    }

    $module = sanitize_text_field($_POST['module']);
    $allowed_mods = [
        'marka_bazli_bayi_seviyeleri',
        'etkinlikler',
        'kampanyalar',
        'teklif_notu',
        'teklif_reklami',
        'bankalar',
        'marka_satis_hedefleri',
        'onaylar_kar_oranlari',
        'onaylar_diger',
        'musteri_temsilcisi_degistir',
        'sophos_cari_kod_edi_eslesmesi',
        'tl_faturalanmayacak_markalar'
    ];

    if (in_array($module, $allowed_mods)) {
        $file_path = get_stylesheet_directory() . '/erp/mod/' . $module . '.php';
        if (file_exists($file_path)) {
            ob_start();
            try {
                include $file_path;
                $content = ob_get_clean();
                echo $content;
            } catch (Exception $e) {
                ob_end_clean();
                echo '<div style="padding: 40px; text-align: center; color: red;">Hata: ' . esc_html($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div style="padding: 40px; text-align: center; color: #666;">' . __('Modül bulunamadı: ','komtera') . esc_html($module) . '</div>';
        }
    } else {
        echo '<div style="padding: 40px; text-align: center; color: #666;">' . __('Geçersiz modül','komtera') . '</div>';
    }

    wp_die();
}

// AJAX handler for saving marka hedefleri
add_action('wp_ajax_save_marka_hedefleri', 'handle_save_marka_hedefleri');

function handle_save_marka_hedefleri() {
    global $wpdb;

    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'save_marka_hedefleri')) {
        wp_send_json_error('Güvenlik hatası');
        return;
    }

    $data = json_decode(stripslashes($_POST['data']), true);

    if (!$data) {
        wp_send_json_error('Geçersiz veri');
        return;
    }

    require_once get_stylesheet_directory() . '/inc/table_helper.php';
    $table_name = getTableName('aa_erp_kt_mt_hedefler');

    try {
        foreach ($data as $row) {
            $wpdb->update(
                $table_name,
                [
                    'q1' => floatval($row['q1']),
                    'q2' => floatval($row['q2']),
                    'q3' => floatval($row['q3']),
                    'q4' => floatval($row['q4'])
                ],
                ['id' => intval($row['id'])],
                ['%f', '%f', '%f', '%f'],
                ['%d']
            );
        }
        wp_send_json_success('Veriler kaydedildi');
    } catch (Exception $e) {
        wp_send_json_error('Kayıt hatası: ' . $e->getMessage());
    }
}

// AJAX handler for saving teklif notu - MSSQL (DEBUG)
add_action('wp_ajax_save_teklif_notu', 'handle_save_teklif_notu');

function handle_save_teklif_notu() {
    $debug = [];
    $debug['post_data'] = $_POST;

    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'save_teklif_notu')) {
        $debug['nonce_error'] = 'Nonce geçersiz';
        $debug['nonce_received'] = $_POST['nonce'] ?? 'yok';
        wp_send_json_error($debug);
        return;
    }

    $value = isset($_POST['value']) ? $_POST['value'] : '';
    $debug['value'] = $value;

    // MSSQL bağlantısı
    require_once get_stylesheet_directory() . '/erp/_conn.php';
    require_once get_stylesheet_directory() . '/inc/table_helper.php';

    // Global $conn değişkenini kullan
    global $conn;

    if (!$conn) {
        wp_send_json_error('Veritabanı bağlantısı yok');
        return;
    }

    $table_name = getTableName('aa_erp_kt_values');
    $debug['table_name'] = $table_name;

    try {
        // Önce kayıt var mı kontrol et
        $check_sql = "SELECT COUNT(*) as cnt FROM [$table_name] WHERE [key] = :key";
        $debug['check_sql'] = $check_sql;

        $stmt = $conn->prepare($check_sql);
        $stmt->execute(['key' => 'teklif_notu']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $exists = $result['cnt'] ?? 0;
        $debug['exists'] = $exists;

        if ($exists > 0) {
            // UPDATE
            $update_sql = "UPDATE [$table_name] SET [value] = :value WHERE [key] = :key";
            $debug['sql'] = $update_sql;
            $debug['action'] = 'UPDATE';

            $stmt = $conn->prepare($update_sql);
            $stmt->execute([
                'value' => $value,
                'key' => 'teklif_notu'
            ]);
        } else {
            // INSERT
            $insert_sql = "INSERT INTO [$table_name] ([key], [value]) VALUES (:key, :value)";
            $debug['sql'] = $insert_sql;
            $debug['action'] = 'INSERT';

            $stmt = $conn->prepare($insert_sql);
            $stmt->execute([
                'key' => 'teklif_notu',
                'value' => $value
            ]);
        }

        $debug['success'] = true;
        wp_send_json_success($debug);

    } catch (PDOException $e) {
        $debug['error'] = $e->getMessage();
        $debug['success'] = false;
        wp_send_json_error($debug);
    }
}

// AJAX handler for loading tools modules
add_action('wp_ajax_load_tools_module', 'handle_load_tools_module');
add_action('wp_ajax_nopriv_load_tools_module', 'handle_load_tools_module');

function handle_load_tools_module() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'load_tool_module_nonce')) {
        wp_die('Güvenlik hatası');
    }

    $module = sanitize_text_field($_POST['module']);
    $allowed_mods = [
        'sophos_siparisler',
        'marka_account_manager_silme',
        'acronis_faturalama',
        'sophos_faturalama',
        'mediamarkt_faturalama',
        'vatan_faturalama'
    ];

    if (in_array($module, $allowed_mods)) {
        $file_path = get_stylesheet_directory() . '/erp/tools/' . $module . '.php';
        if (file_exists($file_path)) {
            ob_start();
            include $file_path;
            $content = ob_get_clean();
            echo $content;
        } else {
            echo '<div style="padding: 40px; text-align: center; color: #666;">' . __('Araç modülü bulunamadı: ','komtera') . esc_html($module) . '</div>';
        }
    } else {
        echo '<div style="padding: 40px; text-align: center; color: #666;">' . __('Geçersiz araç modülü','komtera') . '</div>';
    }

    wp_die();
}

function firsatlar_detay_cb()
{
    // Sadece PHP dosyasını include et, iframe yok
    include get_template_directory() . '/erp/mod/firsatlar_detay.php';
}

function teklifler_detay_cb()
{
    // Teklif detay sayfasını include et
    include get_template_directory() . '/erp/mod/teklifler_detay.php';
}
?>

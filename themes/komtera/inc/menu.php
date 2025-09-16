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
        add_menu_page(__('firsatlar', 'komtera'), __('firsatlar', 'komtera'), 'read','firsatlar', 'firsatlar_cb','dashicons-visibility',2.01);
        add_submenu_page('firsatlar', __('listeler', 'komtera'), __('listeler', 'komtera'), 'read','firsatlar', 'firsatlar_cb');
        add_submenu_page('firsatlar', __('yeni_firsat', 'komtera'), __('yeni_firsat', 'komtera'), 'read','firsatlar_yeni', 'firsatlar_yeni_cb');
    }
    if (array_key_exists('_orders_',   $ana_yetkiler)) add_menu_page(__('siparisler', 'komtera'), __('siparisler', 'komtera'), 'read','siparisler_slug',          'siparisler_cb','dashicons-cart',2.02);
    if (array_key_exists('_demos_',      $ana_yetkiler)) add_menu_page(__('demolar', 'komtera'), __('demolar', 'komtera'), 'read','demolar_slug',                   'demolar_cb','dashicons-screenoptions',2.03);
    if (array_key_exists('_activities_',  $ana_yetkiler)) add_menu_page(__('aktiviteler', 'komtera'), __('aktiviteler', 'komtera'), 'read','aktiviteler_slug',       'aktiviteler_cb','dashicons-clock',2.04);
    if (array_key_exists('_poc_',          $ana_yetkiler)) add_menu_page(__('poc', 'komtera'), __('poc', 'komtera'), 'read','poc_slug',                               'poc_cb','dashicons-networking',2.05);
    if (array_key_exists('_reports_',     $ana_yetkiler)) add_menu_page(__('raporlar', 'komtera'), __('raporlar', 'komtera'), 'read','raporlar_slug',                'raporlar_cb','dashicons-chart-pie',2.06);
    if (array_key_exists('_reports_management_', $ana_yetkiler)) add_menu_page(__('raporlar yonetim', 'komtera'), __('raporlar yonetim', 'komtera'), 'read','raporlar_yonetim_slug','raporlar_yonetim_cb','dashicons-chart-line',2.065);
    if (array_key_exists('_tools_',      $ana_yetkiler)) add_menu_page(__('araclar', 'komtera'), __('araclar', 'komtera'), 'read','araclar_slug',                   'araclar_cb','dashicons-admin-tools',2.07);
    if (array_key_exists('_pricelist_', $ana_yetkiler)) add_menu_page(__('fiyat listesi', 'komtera'), __('fiyat listesi', 'komtera'), 'read','fiyat_listesi_slug', 'fiyat_listesi_cb','dashicons-tag',2.08);
    if (array_key_exists('_renewals_',  $ana_yetkiler)) add_menu_page(__('yenilemeler', 'komtera'), __('yenilemeler', 'komtera'), 'read','yenilemeler_slug',       'yenilemeler_cb','dashicons-update',2.09);
    if (array_key_exists('_invoices_',    $ana_yetkiler)) add_menu_page(__('faturalar', 'komtera'), __('faturalar', 'komtera'), 'read','faturalar_slug',             'faturalar_cb','dashicons-text',2.10);
    if (array_key_exists('_stocks_',      $ana_yetkiler)) add_menu_page(__('stoklar', 'komtera'), __('stoklar', 'komtera'), 'read','stoklar_slug',                   'stoklar_cb','dashicons-database-add',2.11);
    if (array_key_exists('_dealers_',      $ana_yetkiler)) add_menu_page(__('bayiler', 'komtera'), __('bayiler', 'komtera'), 'read','bayiler_slug',                   'bayiler_cb','dashicons-building',2.12);
    if (array_key_exists('_customers_',   $ana_yetkiler)) add_menu_page(__('musteriler', 'komtera'), __('musteriler', 'komtera'), 'read','musteriler_slug',          'musteriler_cb','dashicons-groups',2.13);
    if (array_key_exists('_settings_',      $ana_yetkiler)) add_menu_page(__('ayarlar', 'komtera'), __('ayarlar', 'komtera'), 'read','ayarlar_slug',                   'ayarlar_cb','dashicons-admin-generic',2.14);

}

add_action('admin_menu', 'my_custom_admin_menus_for_roles');

function firsatlar_cb()
{
    // Default table, JavaScript will update this dynamically
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=firsatlar';
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
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#d4edda'; this.style.borderColor='#28a745'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-plus-alt2" style="font-size: 24px; color: #28a745; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('yeni_firsat', 'komtera'); ?></span>
                </div>

                <!-- Tarih Seçimi -->
                <div id="date_selector" style="
                        display: flex;
                        align-items: center;
                        gap: 10px;
                        padding: 12px;
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
                <div id="firsat_buttons" class="opportunity-button table-btn active" data-table="firsatlar" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: #0073aa;
                        border: 2px solid #0073aa;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 2px 4px rgba(0,115,170,0.2);
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-unlock" style="font-size: 24px; color: white; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: white !important;"><?php echo __('acik_firsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn" data-table="firsatlar_tek" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-media-document" style="font-size: 24px; color: #ff9800; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('acik_ana_teklifler', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn" data-table="firsatlar_kaz" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 24px; color: #4caf50; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('kazanilan', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn" data-table="firsatlar_kay" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#ffebee'; this.style.borderColor='#f44336'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-dismiss" style="font-size: 24px; color: #f44336; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('kaybedilen_firsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn" data-table="firsatlar2" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-list-view" style="font-size: 24px; color: #9c27b0; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('tum_firsatlar', 'komtera'); ?></span>
                </div>

                <div class="opportunity-button table-btn" data-table="firsatlar_yanfir" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        " onmouseover="if(!this.classList.contains('active')) { this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff5722'; }" onmouseout="if(!this.classList.contains('active')) { this.style.backgroundColor='white'; this.style.borderColor='#ccc'; }">
                    <span class="dashicons dashicons-networking" style="font-size: 24px; color: #ff5722; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;"><?php echo __('yan_firsatlar', 'komtera'); ?></span>
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
                    const msg = lang === 'tr' ? 'Lütfen iki tarihi de seçin.' : 'Please select both dates.';
                    alert(msg);
                    return;
                }
                if (v1 > v2) {
                    const msg = lang === 'tr' ? 'Başlangıç tarihi, bitiş tarihinden büyük olamaz.' : 'Start date cannot be greater than end date.';
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
                        b.style.boxShadow = 'none';
                        // İkon ve yazı rengini sıfırla - her butonun orijinal rengine döndür
                        const spans = b.querySelectorAll('span');
                        if (spans.length >= 2) {
                            // İkonun orijinal rengini data attribute'tan al veya table'a göre belirle
                            const tableName = b.getAttribute('data-table');
                            let originalColor = '#333';
                            if (tableName === 'firsatlar') originalColor = '#0073aa';
                            else if (tableName === 'firsatlar_tek') originalColor = '#ff9800';
                            else if (tableName === 'firsatlar_kaz') originalColor = '#4caf50';
                            else if (tableName === 'firsatlar_kay') originalColor = '#f44336';
                            else if (tableName === 'firsatlar2') originalColor = '#9c27b0';
                            else if (tableName === 'firsatlar_yanfir') originalColor = '#ff5722';

                            spans[0].style.color = originalColor; // İkon
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
                        padding: 12px;
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
                        padding: 12px;
                        background: #0073aa;
                        border: 2px solid #0073aa;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 2px 4px rgba(0,115,170,0.2);
                        " onmouseover="this.style.backgroundColor='#005a8b'; this.style.borderColor='#005a8b';" onmouseout="this.style.backgroundColor='#0073aa'; this.style.borderColor='#0073aa';">
                    <span class="dashicons dashicons-download" style="font-size: 24px; color: white; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: white !important;"><?php echo __('getir', 'komtera'); ?></span>
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
                    const msg = lang === 'tr' ? 'Lütfen iki tarihi de seçin.' : 'Please select both dates.';
                    alert(msg);
                    return;
                }
                if (v1 > v2) {
                    const msg = lang === 'tr' ? 'Başlangıç tarihi, bitiş tarihinden büyük olamaz.' : 'Start date cannot be greater than end date.';
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
            Bir araç modülü seçin
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
                    } else {
                        contentArea.innerHTML = '<div style="padding: 40px; text-align: center; color: #f44336;">Modül yüklenirken hata oluştu</div>';
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
                        padding: 12px;
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

                <!-- Excel'den Al Butonu -->
                <div class="pricelist-button" onclick="exceldenAl()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-upload" style="font-size: 24px; color: #4caf50; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Excel'den Al</span>
                </div>

                <!-- Excel'e Gönder Butonu -->
                <div class="pricelist-button" onclick="exceleyeGonder()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
                        background: white;
                        border: 1px solid #ccc;
                        border-radius: 6px;
                        cursor: pointer;
                        min-width: 90px;
                        transition: all 0.2s;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        " onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#2196f3';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-download" style="font-size: 24px; color: #2196f3; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Excel'e Gönder</span>
                </div>

                <!-- Marka Ekle Butonu -->
                <div class="pricelist-button" onclick="markaEkle()" style="
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 12px;
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

                <!-- Seçili Marka Göstergesi -->
                <div id="selected_marka_display" style="
                        padding: 12px;
                        background: #e8f5e8;
                        border: 1px solid #4caf50;
                        border-radius: 6px;
                        color: #2e7d32;
                        font-weight: bold;
                        font-size: 13px;
                        display: none;
                        ">
                    <span class="dashicons dashicons-yes" style="margin-right: 5px;"></span>
                    <span id="selected_marka_text">Tüm Markalar</span>
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
        let selectedMarka = '';
        const baseUrl = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php";
        const serviceUrl = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/_service";

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
                                padding: 12px;
                                border: 1px solid #ddd;
                                border-radius: 4px;
                                margin-bottom: 8px;
                                cursor: pointer;
                                background: white;
                                transition: all 0.2s;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            " onmouseover="this.style.backgroundColor='#f0f8ff'" onmouseout="this.style.backgroundColor='white'">
                                <div style="display: flex; align-items: center;">
                                    <span class="dashicons dashicons-tag" style="margin-right: 8px; color: #666;"></span>
                                    <span>${marka.MARKA}</span>
                                </div>
                                <span style="background: #e3f2fd; color: #1976d2; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: bold;">
                                    ${marka.kayit_sayisi}
                                </span>
                            </div>`;
                        });

                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div style="text-align: center; padding: 20px; color: #f44336;">Markalar yüklenirken hata oluştu</div>';
                    }
                })
                .catch(error => {
                    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #f44336;">Markalar yüklenirken hata oluştu</div>';
                });
        }

        function selectMarka(marka) {
            selectedMarka = marka;
            const display = document.getElementById('selected_marka_display');
            const text = document.getElementById('selected_marka_text');

            text.textContent = marka;
            display.style.display = 'block';

            // iframe'i güncelle
            const iframe = document.getElementById('erp_iframe');
            let newSrc = baseUrl + '?t=fiyat_listesi&marka=' + encodeURIComponent(marka);
            iframe.src = newSrc;

            closeMarkaPopup();
        }

        function exceldenAl() {
            alert('Excel\'den Al özelliği yakında eklenecek');
        }

        function exceleyeGonder() {
            alert('Excel\'e Gönder özelliği yakında eklenecek');
        }

        function markaEkle() {
            const marka = prompt('Yeni marka adını giriniz:');
            if (marka && marka.trim()) {
                alert('Marka Ekle özelliği yakında eklenecek\nEklenecek marka: ' + marka.trim());
            }
        }

        // Popup dışına tıklandığında kapat
        document.getElementById('marka_popup').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMarkaPopup();
            }
        });
    </script>

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
    ?>
    <div class="wrap">
        <div style="margin-bottom: 15px; padding: 10px; background: #f1f1f1; border-radius: 5px;">
            <button class="table-btn active" data-table="yenilemeler" style="margin-right: 8px; height: 34px; padding: 0 13px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-update" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span>Yenilemeler</button>
            <button class="table-btn" data-table="yenilemeler_liste" style="margin-right: 8px; height: 34px; padding: 0 13px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-list-view" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span>Yenilemeler Liste</button>
            <button class="table-btn" data-table="60gun_liste" style="margin-right: 8px; height: 34px; padding: 0 13px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-calendar-alt" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span>60 Gün Liste</button>
        </div>
        <div style="position: relative; height: calc(100vh - 180px);">
            <iframe id="erp_iframe"
                    src="<?php echo esc_url($base_src . 'yenilemeler'); ?>"
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

                    // Update button states
                    buttons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.style.background = '#6c757d';
                    });
                    this.classList.add('active');
                    this.style.background = '#0073aa';

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
            padding: 15px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        ">
            <!-- Settings Buttons -->
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">

                <!-- Marka Bazlı Bayi Seviyeleri -->
                <div class="settings-button" onclick="loadModule('marka_bazli_bayi_seviyeleri')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.borderColor='#1976d2';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-building" style="font-size: 24px; color: #0073aa; margin-bottom: 10px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333; height: 24px; line-height: 1.3; display: flex; align-items: center;">Marka Bazlı<br>Bayi Seviyeleri</span>
                </div>

                <!-- Etkinlikler -->
                <div class="settings-button" onclick="loadModule('etkinlikler')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#e8f5e8'; this.style.borderColor='#4caf50';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-calendar-alt" style="font-size: 24px; color: #4caf50; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Etkinlikler</span>
                </div>

                <!-- Kampanyalar -->
                <div class="settings-button" onclick="loadModule('kampanyalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#fff3e0'; this.style.borderColor='#ff9800';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-megaphone" style="font-size: 24px; color: #ff9800; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Kampanyalar</span>
                </div>

                <!-- Teklif Notu -->
                <div class="settings-button" onclick="loadModule('teklif_notu')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
                    background: white;
                    border: 1px solid #ccc;
                    border-radius: 6px;
                    cursor: pointer;
                    min-width: 90px;
                    transition: all 0.2s;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                " onmouseover="this.style.backgroundColor='#f3e5f5'; this.style.borderColor='#9c27b0';" onmouseout="this.style.backgroundColor='white'; this.style.borderColor='#ccc';">
                    <span class="dashicons dashicons-edit-page" style="font-size: 24px; color: #9c27b0; margin-bottom: 6px;"></span>
                    <span style="font-size: 11px; text-align: center; font-weight: 500; color: #333;">Teklif Notu</span>
                </div>

                <!-- Teklif Reklamı -->
                <div class="settings-button" onclick="loadModule('teklif_reklami')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
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

                <!-- Bankalar -->
                <div class="settings-button" onclick="loadModule('bankalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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
                    padding: 12px;
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

                <!-- Sophos Cari Kod EDI Eşleşmesi -->
                <div class="settings-button" onclick="loadModule('sophos_cari_kod_edi_eslesmesi')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
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

                <!-- TL Faturalanmayacak Markalar -->
                <div class="settings-button" onclick="loadModule('tl_faturalanmayacak_markalar')" style="
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    padding: 12px;
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
        <div id="content-area" style="position: relative; height: calc(100vh - 280px); padding: 40px; text-align: center; color: #666; font-size: 16px;">
            Bir ayar modülü seçin
        </div>
    </div>

    <script>
        function loadModule(moduleName) {
            var contentArea = document.getElementById('content-area');
            contentArea.innerHTML = '<div style="padding: 40px; text-align: center;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #0073aa; border-radius: 50%; animation: spin 1s linear infinite;"></div><br><br>Yükleniyor...</div>';

            // AJAX request to load module content
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajaxurl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        contentArea.innerHTML = xhr.responseText;
                    } else {
                        contentArea.innerHTML = '<div style="padding: 40px; text-align: center; color: #f44336;">Modül yüklenirken hata oluştu</div>';
                    }
                }
            };

            xhr.send('action=load_settings_module&module=' + encodeURIComponent(moduleName) + '&nonce=<?php echo wp_create_nonce("load_module_nonce"); ?>');
        }
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
            include $file_path;
            $content = ob_get_clean();
            echo $content;
        } else {
            echo '<div style="padding: 40px; text-align: center; color: #666;">Modül bulunamadı: ' . esc_html($module) . '</div>';
        }
    } else {
        echo '<div style="padding: 40px; text-align: center; color: #666;">Geçersiz modül</div>';
    }

    wp_die();
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
            echo '<div style="padding: 40px; text-align: center; color: #666;">Araç modülü bulunamadı: ' . esc_html($module) . '</div>';
        }
    } else {
        echo '<div style="padding: 40px; text-align: center; color: #666;">Geçersiz araç modülü</div>';
    }

    wp_die();
}

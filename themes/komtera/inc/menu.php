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

    if (array_key_exists('_opportunities_',    $ana_yetkiler)) add_menu_page(__('firsatlar', 'komtera'), __('firsatlar', 'komtera'), 'read','firsatlar',                  'firsatlar_cb','dashicons-visibility',2.01);
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
        <div style="margin-bottom: 15px; padding: 10px; background: #f1f1f1; border-radius: 5px;">
            <label for="date1" style="margin-right: 10px;"><?php echo __('tarih', 'komtera'); ?>:</label>
            <input type="date" id="date1" name="date1" lang="<?php echo esc_attr($lang); ?>"
                   style="margin-right: 20px; padding: 5px; height: 34px; box-sizing: border-box; vertical-align: top;">
            
            <label for="date2" style="margin-right: 10px; line-height: 34px; vertical-align: top;">-</label>
            <input type="date" id="date2" name="date2" lang="<?php echo esc_attr($lang); ?>"
                   style="margin-right: 20px; padding: 5px; height: 34px; box-sizing: border-box; vertical-align: top;">
            
            <!-- Fırsat Türü Butonları -->
            <button class="table-btn active" data-table="firsatlar" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-unlock" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('acik_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_tek" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-media-document" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('acik_ana_teklifler', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_kaz" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-yes-alt" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('kazanilan', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_kay" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-dismiss" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('kaybedilen_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar2" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-list-view" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('tum_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_yanfir" style="margin-right: 8px; height: 34px; padding: 0 12px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; vertical-align: top; box-sizing: border-box;"><span class="dashicons dashicons-networking" style="margin-right: 4px; font-size: 21px; line-height: 1;"></span><?php echo __('yan_firsatlar', 'komtera'); ?></button>
        </div>
        <div style="position: relative; height: calc(100vh - 200px);">
        <iframe id="erp_iframe"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
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
                        b.style.background = '#6c757d';
                    });
                    this.classList.add('active');
                    this.style.background = '#0073aa';
                    
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
function siparisler_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=siparisler';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function demolar_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=demolar';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe_other"
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
            <iframe id="erp_iframe_other"
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
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function raporlar_cb()      { echo '<div class="wrap"><h1>Raporlar</h1><p>Yapım aşamasında</p></div>'; }
function raporlar_yonetim_cb(){ echo '<div class="wrap"><h1>Raporlar Yönetim</h1><p>Yapım aşamasında</p></div>'; }
function araclar_cb()       { echo '<div class="wrap"><h1>Araçlar</h1><p>Yapım aşamasında</p></div>'; }
function fiyat_listesi_cb() {
    //Ticket: Marka popup olacak, ve marka secilince o marka listesi gelecek!
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=fiyat_listesi';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function yenilemeler_cb() {
    $base_src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=';
    ?>
    <div class="wrap">
        <p>
            <button type="button" onclick="changeIframe('yenilemeler')">Yenilemeler</button>
            <button type="button" onclick="changeIframe('yenilemeler_liste')">Yenilemeler Liste</button>
            <button type="button" onclick="changeIframe('60gun_liste')">60 Gün Liste</button>
        </p>
        <div style="position: relative; height: calc(100vh - 180px);">
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($base_src . 'yenilemeler'); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <script>
        function changeIframe(target) {
            var iframe = document.getElementById('erp_iframe');
            iframe.src = "<?php echo esc_url($base_src); ?>" + target;
        }
    </script>
    <?php
}
function faturalar_cb()     {
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=faturalar';
    ?>
    <div class="wrap">
        <div style="position: relative; height: calc(100vh - 140px);">
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
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
            <iframe id="erp_iframe_other"
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
            <iframe id="erp_iframe_other"
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
            <iframe id="erp_iframe_other"
                    src="<?php echo esc_url($src); ?>"
                    width="100%"
                    height="100%"
                    style="border:1px solid #ccc; position:absolute; top:0; left:0;">
            </iframe>
        </div>
    </div>
    <?php
}
function ayarlar_cb()       { echo '<div class="wrap"><h1>Ayarlar</h1><p>Yapım aşamasında</p></div>'; }

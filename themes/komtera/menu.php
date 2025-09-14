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
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=firsatlar';
    $locale = get_user_locale(); // Kullanıcının seçtiği locale (tr_TR, en_US, etc.)
    $lang = substr($locale, 0, 2); // İlk iki harf (tr, en, etc.)
    ?>
    <div class="wrap">
        <div style="margin-bottom: 15px; padding: 10px; background: #f1f1f1; border-radius: 5px;">
            <label for="date1" style="margin-right: 10px;"><?php echo __('baslangic_tarihi', 'komtera'); ?>:</label>
            <input type="text" id="date1" name="date1" class="datepicker"
                   placeholder="<?php echo ($lang == 'tr') ? 'gg.aa.yyyy' : 'mm/dd/yyyy'; ?>" 
                   title="<?php echo __('tarih_formati', 'komtera'); ?>"
                   style="margin-right: 20px; padding: 5px; width: 120px;">
            
            <label for="date2" style="margin-right: 10px;"><?php echo __('bitis_tarihi', 'komtera'); ?>:</label>
            <input type="text" id="date2" name="date2" class="datepicker"
                   placeholder="<?php echo ($lang == 'tr') ? 'gg.aa.yyyy' : 'mm/dd/yyyy'; ?>" 
                   title="<?php echo __('tarih_formati', 'komtera'); ?>"
                   style="margin-right: 20px; padding: 5px; width: 120px;">
            
            <button id="btnGetir" style="padding: 6px 12px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer;"><?php echo __('getir', 'komtera'); ?></button>
            
            <!-- Fırsat Türü Butonları -->
            <span style="margin-left: 20px; font-weight: bold; color: #333; margin-right: 8px;">|</span>
            <button class="table-btn active" data-table="firsatlar" style="margin-right: 8px; padding: 6px 10px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('acik_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_tek" style="margin-right: 8px; padding: 6px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('acik_ana_teklifler', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_kaz" style="margin-right: 8px; padding: 6px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('kazanilan', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_kay" style="margin-right: 8px; padding: 6px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('kaybedilen_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar2" style="margin-right: 8px; padding: 6px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('tum_firsatlar', 'komtera'); ?></button>
            <button class="table-btn" data-table="firsatlar_yanfir" style="margin-right: 8px; padding: 6px 10px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;"><?php echo __('yan_firsatlar', 'komtera'); ?></button>
        </div>
        
        <!-- jQuery UI CSS -->
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
        <script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        
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
                    font-size: 10px !important;
                    padding: 6px 8px !important;
                    margin-right: 4px !important;
                    margin-bottom: 4px;
                }
            }
        </style>
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
        jQuery(document).ready(function($) {
            const iframe = document.getElementById('erp_iframe');
            const base = "<?php echo esc_js($src); ?>";
            const locale = "<?php echo esc_js($locale); ?>"; // WordPress locale
            const lang = "<?php echo esc_js($lang); ?>"; // Dil kodu
            const isTurkish = locale.startsWith('tr');
            
            // jQuery UI Datepicker locale ayarları
            if (isTurkish) {
                $.datepicker.regional['tr'] = {
                    closeText: 'Kapat',
                    prevText: '&#x3C;geri',
                    nextText: 'ileri&#x3E;',
                    currentText: 'bugün',
                    monthNames: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
                    monthNamesShort: ['Oca','Şub','Mar','Nis','May','Haz','Tem','Ağu','Eyl','Eki','Kas','Ara'],
                    dayNames: ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'],
                    dayNamesShort: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
                    dayNamesMin: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
                    weekHeader: 'Hf',
                    dateFormat: 'dd.mm.yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''
                };
                $.datepicker.setDefaults($.datepicker.regional['tr']);
            } else {
                $.datepicker.setDefaults($.datepicker.regional['en']);
            }
            
            // Datepicker'ları başlat
            $('#date1, #date2').datepicker({
                dateFormat: isTurkish ? 'dd.mm.yy' : 'mm/dd/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: 'c-10:c+10'
            });
            
            // Varsayılan tarihleri ayarla
            const today = new Date();
            const oneMonthAgo = new Date(today);
            oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
            
            // Kaydedilen tarihler varsa kullan
            const savedDate1 = localStorage.getItem('firsatlar_date1_display');
            const savedDate2 = localStorage.getItem('firsatlar_date2_display');
            
            if (savedDate1) {
                $('#date1').val(savedDate1);
            } else {
                $('#date1').datepicker('setDate', oneMonthAgo);
            }
            
            if (savedDate2) {
                $('#date2').val(savedDate2);
            } else {
                $('#date2').datepicker('setDate', today);
            }
            
            function loadIframe() {
                const date1Str = $('#date1').val();
                const date2Str = $('#date2').val();
                
                if (!date1Str || !date2Str) {
                    const msg = lang === 'tr' ? 'Lütfen iki tarihi de seçin.' : 'Please select both dates.';
                    alert(msg);
                    return;
                }
                
                // Tarihleri Date objesine çevir
                const date1 = $('#date1').datepicker('getDate');
                const date2 = $('#date2').datepicker('getDate');
                
                if (date1 > date2) {
                    const msg = lang === 'tr' ? 'Başlangıç tarihi, bitiş tarihinden büyük olamaz.' : 'Start date cannot be greater than end date.';
                    alert(msg);
                    return;
                }
                
                // Display formatını kaydet
                localStorage.setItem('firsatlar_date1_display', date1Str);
                localStorage.setItem('firsatlar_date2_display', date2Str);
                
                // URL formatına çevir (YYYY-MM-DD)
                const urlDate1 = date1.getFullYear() + '-' + 
                    String(date1.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(date1.getDate()).padStart(2, '0');
                const urlDate2 = date2.getFullYear() + '-' + 
                    String(date2.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(date2.getDate()).padStart(2, '0');
                
                const url = `${base}&date1=${encodeURIComponent(urlDate1)}&date2=${encodeURIComponent(urlDate2)}`;
                iframe.src = url;
            }
            
            // İlk yüklemede otomatik getir
            loadIframe();
            
            // Event listener'lar
            $('#btnGetir').click(loadIframe);
            $('#date1, #date2').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    loadIframe();
                }
            });
            
            // Tablo değiştirme butonları
            $('.table-btn').click(function() {
                const tableName = $(this).data('table');
                const currentDate1 = $('#date1').val();
                const currentDate2 = $('#date2').val();
                
                // Aktif buton stilini değiştir
                $('.table-btn').removeClass('active').css('background', '#6c757d');
                $(this).addClass('active').css('background', '#0073aa');
                
                // iframe src'sini güncelle
                let newSrc = "<?php echo esc_js(get_stylesheet_directory_uri()); ?>/erp/tablo_render.php?t=" + tableName;
                
                // Tarih parametrelerini ekle
                if (currentDate1 && currentDate2) {
                    const date1 = $('#date1').datepicker('getDate');
                    const date2 = $('#date2').datepicker('getDate');
                    
                    if (date1 && date2) {
                        const urlDate1 = date1.getFullYear() + '-' + 
                            String(date1.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(date1.getDate()).padStart(2, '0');
                        const urlDate2 = date2.getFullYear() + '-' + 
                            String(date2.getMonth() + 1).padStart(2, '0') + '-' + 
                            String(date2.getDate()).padStart(2, '0');
                        
                        newSrc += `&date1=${encodeURIComponent(urlDate1)}&date2=${encodeURIComponent(urlDate2)}`;
                    }
                }
                
                iframe.src = newSrc;
            });
        });
    </script>
    <?php
}
function siparisler_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=siparisler';
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
function raporlar_cb()      { echo '<div class="wrap"><h1>Raporlar</h1><p>Yapım aşamasında</p></div>'; }
function raporlar_yonetim_cb(){ echo '<div class="wrap"><h1>Raporlar Yönetim</h1><p>Yapım aşamasında</p></div>'; }
function araclar_cb()       { echo '<div class="wrap"><h1>Araçlar</h1><p>Yapım aşamasında</p></div>'; }
function fiyat_listesi_cb() {
    //Ticket: Marka popup olacak, ve marka secilince o marka listesi gelecek!
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=fiyat_listesi';
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
            <iframe id="erp_iframe"
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
function ayarlar_cb()       { echo '<div class="wrap"><h1>Ayarlar</h1><p>Yapım aşamasında</p></div>'; }

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
    if (array_key_exists('_invoices_',    $ana_yetkiler)) add_menu_page(__('faturalar', 'komtera'), __('faturalar', 'komtera'), 'read','faturalar_slug',             'faturalar_cb','dashicons-list-view
',2.10);
    if (array_key_exists('_stocks_',      $ana_yetkiler)) add_menu_page(__('stoklar', 'komtera'), __('stoklar', 'komtera'), 'read','stoklar_slug',                   'stoklar_cb','dashicons-database-add',2.11);
    if (array_key_exists('_dealers_',      $ana_yetkiler)) add_menu_page(__('bayiler', 'komtera'), __('bayiler', 'komtera'), 'read','bayiler_slug',                   'bayiler_cb','dashicons-building',2.12);
    if (array_key_exists('_customers_',   $ana_yetkiler)) add_menu_page(__('musteriler', 'komtera'), __('musteriler', 'komtera'), 'read','musteriler_slug',          'musteriler_cb','dashicons-groups',2.13);
    if (array_key_exists('_settings_',      $ana_yetkiler)) add_menu_page(__('ayarlar', 'komtera'), __('ayarlar', 'komtera'), 'read','ayarlar_slug',                   'ayarlar_cb','dashicons-admin-generic',2.14);

}

add_action('admin_menu', 'my_custom_admin_menus_for_roles');

function firsatlar_cb()
{
    $src = get_stylesheet_directory_uri() . '/erp/tablo_render.php?t=firsatlar';
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

    <script>
        //(function () {
        //    const input1 = document.getElementById('date1');
        //    const input2 = document.getElementById('date2');
        //    const iframe = document.getElementById('erp_iframe');
        //    const base = "<?php //echo esc_js($src); ?>//";
        //
        //    // YYYY-MM-DD format helper (local time)
        //    function fmt(d) {
        //        const y = d.getFullYear();
        //        const m = String(d.getMonth() + 1).padStart(2, '0');
        //        const a = String(d.getDate()).padStart(2, '0');
        //        return `${y}-${m}-${a}`;
        //    }
        //
        //    // İlk gelişte: bugün ve 30 gün öncesi
        //    const today = new Date();
        //    const d1 = new Date(today);
        //    d1.setDate(d1.getDate() - 30); // son 1 ay (30 gün)
        //    input1.value = fmt(d1);
        //    input2.value = fmt(today);
        //
        //    function loadIframe() {
        //        const v1 = input1.value;
        //        const v2 = input2.value;
        //        if (!v1 || !v2) {
        //            alert('Lütfen iki tarihi de seçin.');
        //            return;
        //        }
        //        if (v1 > v2) {
        //            alert('Başlangıç tarihi, bitiş tarihinden büyük olamaz.');
        //            return;
        //        }
        //        const url = `${base}?date1=${encodeURIComponent(v1)}&date2=${encodeURIComponent(v2)}`;
        //        iframe.src = url;
        //    }
        //
        //    // İlk yüklemede otomatik getir
        //    loadIframe();
        //
        //    // Buton
        //    document.getElementById('btnGetir').addEventListener('click', loadIframe);
        //
        //    // Enter ile tetikleme (tarih kutularındayken)
        //    [input1, input2].forEach(el => {
        //        el.addEventListener('keydown', function (e) {
        //            if (e.key === 'Enter') {
        //                loadIframe();
        //            }
        //        });
        //    });
        //})();
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

<?php

function my_profile_permissions_config() {
    // permission => [sub-permissions...]
    $perms = [
        '_opportunities_'   => [],
        '_orders_' => [
            'OR-101_edit',
            'OR-102_detail',
            'OR-103_manual_close',
            'OR-104_delete',
            'OR-105_export'
        ],
        '_demos_' => [
            'DM-101_create',
            'DM-102_update',
            'DM-103_shipping',
            'DM-104_info',
            'DM-105_assign'
        ],
        '_activities_' => [
            'AC-101_new',
            'AC-102_add_note'
        ],
        '_poc_' => [
            'PO-101_export'
        ],
        '_reports_' => [
            'RP-101_commission',
            'RP-102_sales_person',
            'RP-103_sales_all',
            'RP-104_opportunities',
            'RP-105_profitability'
        ],
        '_reports_management_' => [
            'RM-101_sales_brand',
            'RM-102_sales_mt',
            'RM-103_sales_commission',
            'RM-104_open_orders',
            'RM-105_orders_minus15'
        ],
        '_tools_'     => [
            'TO-101_acronis_invoice',
            'TO-102_mediamarkt_invoice',
            'TO-103_vatan_invoice'
        ],
        '_pricelist_' => [
            'PR-101_excel',
            'PR-102_import',
            'PR-103_rates_export',
            'PR-104_rates_import',
            'PR-105_brand_add'
        ],
        '_renewals_' => [
            'RN-101_entry',
            'RN-102_list',
            'RN-103_60days_left'
        ],
        '-' => [],
        '_invoices_' => [
            'IN-101_delete_errors'
        ],
        '_stocks_' => [
            'ST-101_export'
        ],
        '_dealers_' => [
            'DR-101_export',
            'DR-102_edit',
            'DR-103_authorized_edit',
            'DR-104_blacklist'
        ],
        '_customers_' => [
            'CU-101_export'
        ],
        '_settings_' => [
            'SE-101_dealer_level_list',
            'SE-102_dealer_level_export',
            'SE-103_dealer_level_import',
            'SE-104_campaigns_list'
        ]
    ];
    /**
     * İstersen filtreyle dışarıdan genişletebilirsin:
     * add_filter('my_profile_permissions', function($perms){ ...; return $perms; });
     */
    return apply_filters('my_profile_permissions', $perms);
}
add_action('personal_options_update', 'my_user_disable_save');
add_action('edit_user_profile_update', 'my_user_disable_save');
function my_user_disable_save($user_id){
    $u = wp_get_current_user();
    if (!array_intersect(['manage_options', 'administrator' ,'jadmin'], (array)$u->roles)) {
        return;
    }

    if ( ! current_user_can('edit_user', $user_id) ) return;
    if ( empty($_POST['my_user_disable_nonce']) || ! wp_verify_nonce($_POST['my_user_disable_nonce'], 'my_user_disable_save') ) return;

    $was_disabled = get_user_meta($user_id, 'account_disabled', true) === '1';
    $now_disabled = isset($_POST['account_disabled']) && $_POST['account_disabled'] === '1';

    update_user_meta($user_id, 'account_disabled', $now_disabled ? '1' : '0');

    // Yeni pasifleştirildiyse: tüm oturumlarını düşür
    if ( ! $was_disabled && $now_disabled ) {
        if ( class_exists('WP_Session_Tokens') ) {
            $sessions = WP_Session_Tokens::get_instance($user_id);
            if ( $sessions ) $sessions->destroy_all();
        }
    }
}
add_filter('authenticate', 'my_block_disabled_user_login', 30, 3);
function my_block_disabled_user_login($user, $username, $password){
    if ( $user instanceof WP_User ) {
        if ( get_user_meta($user->ID, 'account_disabled', true) === '1' ) {
            return new WP_Error('account_disabled', __('Hesabınız pasifleştirildi. Lütfen yönetici ile iletişime geçin.', 'textdomain'));
        }
    }
    return $user;
}
add_action('init', function(){
    if ( is_user_logged_in() ) {
        $uid = get_current_user_id();
        if ( $uid && get_user_meta($uid, 'account_disabled', true) === '1' ) {
            if ( class_exists('WP_Session_Tokens') ) {
                $sessions = WP_Session_Tokens::get_instance($uid);
                if ( $sessions ) $sessions->destroy_all();
            }
            wp_clear_auth_cookie();
            wp_safe_redirect( wp_login_url( add_query_arg('disabled','1') ) );
            exit;
        }
    }
});
add_action('show_user_profile', 'my_custom_user_fields');
add_action('edit_user_profile', 'my_custom_user_fields');
add_action('personal_options_update', 'my_save_custom_user_fields');
add_action('edit_user_profile_update', 'my_save_custom_user_fields');
add_action('user_register', 'my_save_custom_user_fields');
function my_profile_brands_config() {
    global $conn;
    $sql = "select marka from aa_erp_kt_fiyat_listesi where marka is not null group by marka;";
    try {
        $stmt = $conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = json_encode($data);
        $values = array_column($data, 'marka');
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
        BotMesaj($e->getMessage());
    }
    return apply_filters('my_profile_brands', $values);
}

add_action('show_user_profile', 'my_render_custom_user_fields');      // Kendi profili
add_action('edit_user_profile', 'my_render_custom_user_fields');      // Admin başkasını düzenlerken
function my_render_custom_user_fields($user) {
    $u = wp_get_current_user();
    if (!array_intersect(['manage_options','administrator', 'jadmin'], (array)$u->roles)) {
        return;
    }

    // Tüm kullanıcıları al (sadece belirli rollere sahip olanlar için filtre uygulanabilir)
    $all_users = get_users(array(
        'fields' => array('ID', 'display_name', 'user_login'),
        'orderby' => 'display_name'
    ));

    // Kayıtlı verileri çek
    $saved        = get_user_meta($user->ID, 'my_permissions_matrix', true);
    $saved = is_array($saved) ? $saved : []; // ['perm' => ['sub1','sub2']]
    $savedBrands  = get_user_meta($user->ID, 'my_brands', true);
    $savedBrands  = is_array($savedBrands) ? $savedBrands : [];

    $perms  = my_profile_permissions_config();
    $brands = my_profile_brands_config();

    // Nonce
    wp_nonce_field('my_save_user_fields', 'my_user_fields_nonce');
    ?>

    <!-- Kullanıcı Seçim Arayüzü -->
    <h2><?php echo __('Yetki Alt Yetkiler','komtera'); ?>
        <select id="user-permission-copy" style="margin-left: 20px; width: 200px;">
            <option value=""><?php echo __('Kopyalanacak Kullanıcıyı Seçin', 'komtera'); ?></option>
            <?php foreach ($all_users as $usr) : ?>
                <?php if ($usr->ID == $user->ID) continue; // Mevcut kullanıcıyı listeden çıkar ?>
                <option value="<?php echo $usr->ID; ?>">
                    <?php echo esc_html($usr->display_name) . ' (' . $usr->user_login . ')'; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" id="apply-user-permissions" class="button" style="margin-left: 10px;">
            <?php echo __('Yetkileri Uygula', 'komtera'); ?>
        </button>
    </h2>

    <div id="copy-permissions-feedback" style="display:none; margin-top:10px; padding:10px; background-color:#f0f0f0; border-left:4px solid #0073aa;"></div>

    <table class="form-table" role="presentation">
        <tr>
            <th><div class="my-perm-col">
                    <strong><?php __('Yetki', 'komtera'); ?></strong>
                    <ul class="my-perm-list">
                        <?php foreach ($perms as $permKey => $subs): ?>
                            <?php
                            // Skip entries with '-'
                            if (strpos($permKey, '-') !== false) {
                                echo '<li><strong>' . esc_html($permKey) . '</strong></li>';
                                continue;
                            }

                            $checked = array_key_exists($permKey, $saved);
                            ?>
                            <li>
                                <label>
                                    <input type="checkbox"
                                           class="my-perm-master"
                                           data-target="#my-perm-<?php echo esc_attr($permKey); ?>"
                                           name="my_permissions[<?php echo esc_attr($permKey); ?>][_enabled]"
                                           value="1"
                                        <?php checked($checked); ?>>
                                    <?php echo esc_html($permKey); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div></th>
            <td>
                <div class="my-perm-grid">
                    <div class="my-perm">
                        <strong><?php __('Yetki', 'komtera'); ?></strong>
                        <?php foreach ($perms as $permKey => $subs): ?>
                            <?php
                            // Skip entries with '-'
                            if (strpos($permKey, '-') !== false) {
                                continue;
                            }
                            $isActive = array_key_exists($permKey, $saved);
                            ?>
                            <fieldset id="my-perm-<?php echo esc_attr($permKey); ?>"
                                      class="my-subperm-group"
                                      style="<?php echo $isActive ? '' : 'display:none;'; ?>">
                                <legend>
                                    <?php echo esc_html($permKey); ?>
                                    <button type="button" class="select-all-btn" data-group="my-perm-<?php echo esc_attr($permKey); ?>">
                                        <?php echo __('Tümünü Seç', 'komtera'); ?>
                                    </button>
                                </legend>
                                <?php foreach ($subs as $sub): ?>
                                    <?php
                                    $subChecked = !empty($saved[$permKey]) && in_array($sub, (array)$saved[$permKey], true);
                                    ?>
                                    <label style="display:inline-block;margin:0 16px 6px 0;">
                                        <input type="checkbox"
                                               name="my_permissions[<?php echo esc_attr($permKey); ?>][subs][]"
                                               value="<?php echo esc_attr($sub); ?>"
                                            <?php checked($subChecked); ?>>
                                        <?php echo esc_html($sub); ?>
                                    </label><br />
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- AJAX için gerekli script -->
    <script>
        jQuery(document).ready(function($) {
            $('#apply-user-permissions').on('click', function() {
                var selectedUserId = $('#user-permission-copy').val();
                if (!selectedUserId) {
                    alert('<?php echo __('Lütfen kullanıcı seçiniz', 'komtera'); ?>');
                    return;
                }

                // Feedback alanını göster
                $('#copy-permissions-feedback').show().html('<?php echo __('Yüklü..', 'komtera'); ?>...');

                // AJAX isteği
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'get_user_permissions',
                        user_id: selectedUserId,
                        nonce: '<?php echo wp_create_nonce('get_user_permissions_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;

                            // İzinleri uygula
                            if (data.permissions) {
                                // Tüm ana izin checkbox'larını temizle
                                $('.my-perm-master').prop('checked', false);

                                // Her bir izin için
                                $.each(data.permissions, function(permKey, subs) {
                                    // Ana izin checkbox'ını işaretle ve göster
                                    var masterCheckbox = $('.my-perm-master[name="my_permissions[' + permKey + '][_enabled]"]');
                                    masterCheckbox.prop('checked', true);

                                    // İlgili alt izin grubunu göster
                                    $(masterCheckbox.data('target')).show();

                                    // Alt izinleri temizle ve yeni değerleri işaretle
                                    var groupCheckboxes = $(masterCheckbox.data('target') + ' input[type="checkbox"]');
                                    groupCheckboxes.prop('checked', false);

                                    $.each(subs, function(index, subValue) {
                                        groupCheckboxes.filter('[value="' + subValue + '"]').prop('checked', true);
                                    });
                                });
                            }

                            // Markaları uygula
                            if (data.brands) {
                                // Tüm marka checkbox'larını temizle
                                $('.brand-checkbox').prop('checked', false);

                                // Yeni markaları işaretle
                                $.each(data.brands, function(index, brand) {
                                    $('.brand-checkbox[value="' + brand + '"]').prop('checked', true);
                                });

                                // Tümünü seç checkbox'ını güncelle
                                $('#brands-check-all').prop('checked',
                                    $('.brand-checkbox').length === $('.brand-checkbox:checked').length
                                );
                            }

                            $('#copy-permissions-feedback').html('<?php echo __('Yetkiler başarıyla uygulandı', 'komtera'); ?>').css({
                                'border-left-color': '#46b450',
                                'color': '#2e4453'
                            });
                        } else {
                            $('#copy-permissions-feedback').html('<?php echo __('Yetkiler yüklenirken hata', 'komtera'); ?>: ' + response.data).css({
                                'border-left-color': '#dc3232',
                                'color': '#d63638'
                            });
                        }
                    },
                    error: function() {
                        $('#copy-permissions-feedback').html('<?php echo __('Sunucu Hatası', 'komtera'); ?>').css({
                            'border-left-color': '#dc3232',
                            'color': '#d63638'
                        });
                    }
                });
            });
        });
    </script>

    <!-- Geri kalan aynı (markalar ve stil/script kısımları) -->
    <h2><?php echo __('Markalar', 'komtera'); ?></h2><label><b>
            <input type="checkbox" id="brands-check-all">
            <?php esc_html_e('select_all', 'textdomain'); ?>
        </b></label>
    <table class="form-table" role="presentation">
        <tr>
            <td>
                <p style="margin-bottom:8px;">
                </p>
                <div class="brands-grid">
                    <?php foreach ($brands as $brand): ?>
                        <label class="brand-item">
                            <input type="checkbox"
                                   class="brand-checkbox"
                                   name="my_brands[]"
                                   value="<?php echo esc_attr($brand); ?>"
                                <?php checked(in_array($brand, $savedBrands, true)); ?>>
                            <?php echo esc_html($brand); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </td>
        </tr>
    </table>

    <style>
        .brands-grid{
            display:grid;
            grid-template-columns:repeat(3, minmax(0,1fr));
            gap:8px 16px;
        }
        @media (max-width: 1024px){
            .brands-grid{ grid-template-columns:repeat(2, minmax(0,1fr)); }
        }
        @media (max-width: 480px){
            .brands-grid{ grid-template-columns:1fr; }
        }
        .brand-item{
            display:flex; align-items:center; gap:8px;
            padding:0px 0;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const master = document.getElementById('brands-check-all');
            const checkboxes = document.querySelectorAll('.brand-checkbox');

            master.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = master.checked);
            });
        });
    </script>

    <style>
        .my-perm-grid { display:flex; gap:24px; align-items:flex-start; }
        .my-perm-col { width:50%; min-width:280px; }
        .my-perm-list { margin:8px 0 0; padding-left:0; list-style:none; }
        .my-subperm-group { border:1px solid #ccd0d4; padding:10px 12px; border-radius:6px; margin:0 0 12px; }
        .my-subperm-group > legend { padding:0 6px; font-weight:600; }
        @media (max-width: 900px){
            .my-perm-grid { flex-direction:column; }
            .my-perm-col { width:100%; }
        }
        .select-all-btn {
            font-size: 11px;
            padding: 2px 6px;
            margin-left: 8px;
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-radius: 3px;
            cursor: pointer;
        }
        .select-all-btn:hover {
            background: #e2e2e2;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.select-all-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const groupId = this.getAttribute('data-group');
                    const checkboxes = document.querySelectorAll('#' + groupId + ' input[type="checkbox"]');
                    checkboxes.forEach(cb => cb.checked = true);
                });
            });
        });
    </script>

    <script>
        (function(){
            // Yetki seçimine göre alt yetki kutularını göster/gizle
            document.querySelectorAll('.my-perm-master').forEach(function(chk){
                chk.addEventListener('change', function(){
                    var target = document.querySelector(this.dataset.target);
                    if (!target) return;
                    target.style.display = this.checked ? '' : 'none';
                    // Kapatınca alt yetkileri temizlemek istersen:
                    if (!this.checked) {
                        target.querySelectorAll('input[type=checkbox]').forEach(function(box){ box.checked = false; });
                    }
                });
            });
        })();
    </script>
    <?php
}
add_action('personal_options_update', 'my_save_custom_user_fields'); // Kendi profili kaydedince
add_action('edit_user_profile_update', 'my_save_custom_user_fields'); // Admin başkasını kaydedince
function my_save_custom_user_fields($user_id) {
    $u = wp_get_current_user();
    if (!array_intersect(['manage_options','administrator', 'jadmin'], (array)$u->roles)) {
        return;
    }


    // hedef kullanıcıyı düzenleme Yetkisi de kontrol edelim (ek güvenlik)
    if ( ! current_user_can('edit_user', $user_id) ) {
        return;
    }
    // nonce kontrolü (önceki kodda vardı)
    if ( empty($_POST['my_user_fields_nonce']) || ! wp_verify_nonce($_POST['my_user_fields_nonce'], 'my_save_user_fields') ) {
        return;
    }

    $permsWhitelist  = my_profile_permissions_config(); // Beyaz liste
    $brandsWhitelist = my_profile_brands_config();


// --- FIX: doğru meta anahtarlarıyla kaydet ---
    update_user_meta($user_id, 'logo_kullanici', sanitize_text_field($_POST['logo_kullanici'] ?? ''));
    update_user_meta($user_id, 'cinsiyet',      sanitize_text_field($_POST['cinsiyet'] ?? ''));
    update_user_meta($user_id, 'telefon',       sanitize_text_field($_POST['telefon'] ?? ''));



    // ---- Yetkiler & Alt Yetkiler ----
    $incoming = isset($_POST['my_permissions']) && is_array($_POST['my_permissions']) ? $_POST['my_permissions'] : [];
    $cleanMatrix = [];
    foreach ($incoming as $permKey => $data) {
        // Sadece whitelist’te olan yetkileri kabul et
        if (!array_key_exists($permKey, $permsWhitelist)) continue;

        $enabled = isset($data['_enabled']) && $data['_enabled'] ? true : false;
        if (!$enabled) continue; // işaretli değilse tamamen atla

        $subs = [];
        if (!empty($data['subs']) && is_array($data['subs'])) {
            // Alt yetkileri beyaz listeye göre filtrele
            $allowedSubs = $permsWhitelist[$permKey];
            foreach ($data['subs'] as $s) {
                $s = sanitize_text_field($s);
                if (in_array($s, $allowedSubs, true)) {
                    $subs[] = $s;
                }
            }
        }
        $cleanMatrix[$permKey] = $subs; // boş alt yetki de olabilir
    }
    update_user_meta($user_id, 'my_permissions_matrix', $cleanMatrix);

    // ---- Markalar ----
    $incomingBrands = isset($_POST['my_brands']) && is_array($_POST['my_brands']) ? $_POST['my_brands'] : [];
    $cleanBrands = [];
    foreach ($incomingBrands as $b) {
        $b = sanitize_text_field($b);
        if (in_array($b, $brandsWhitelist, true)) {
            $cleanBrands[] = $b;
        }
    }
    update_user_meta($user_id, 'my_brands', array_values(array_unique($cleanBrands)));
}
function my_custom_user_fields($user) {
    ?>
    <table class="form-table">
        <tr>
            <th><label for="logo_kullanici"><?php echo __('Logo Kullanıcı Adı','komtera'); ?></label></th>
            <td>
                <input type="text" name="logo_kullanici" id="logo_kullanici"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'logo_kullanici', true)); ?>"
                       class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="cinsiyet"><?php echo __('Cinsiyet','komtera'); ?></label></th>
            <td>
                <select name="cinsiyet" id="cinsiyet">
                    <?php $c = get_user_meta($user->ID, 'cinsiyet', true); ?>
                    <option value=""><?php echo __('Seçiniz','komtera'); ?></option>
                    <option value="male" <?php selected($c, 'male'); ?>><?php echo __('Erkek','komtera'); ?></option>
                    <option value="female" <?php selected($c, 'female'); ?>><?php echo __('Kadın','komtera'); ?></option>
                    <option value="other" <?php selected($c, 'other'); ?>><?php echo __('Diğer','komtera'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="telefon"><?php echo __('Telefon','komtera'); ?></label></th>
            <td>
                <input type="text" name="telefon" id="telefon"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'telefon', true)); ?>"
                       class="regular-text" />
            </td>
        </tr>
    </table>
    <?php
}





// AJAX handler for getting user permissions
add_action('wp_ajax_get_user_permissions', 'handle_get_user_permissions');
function handle_get_user_permissions() {
    // Güvenlik kontrolü
    if (!wp_verify_nonce($_POST['nonce'], 'get_user_permissions_nonce')) {
        wp_die('Invalid nonce');
    }

    // Sadece yetkili kullanıcılar
    if (!current_user_can('edit_users')) {
        wp_die('Unauthorized');
    }

    $user_id = intval($_POST['user_id']);

    if (!$user_id) {
        wp_send_json_error('Invalid user ID');
    }

    // İzinleri ve markaları al
    $permissions = get_user_meta($user_id, 'my_permissions_matrix', true);
    $brands = get_user_meta($user_id, 'my_brands', true);

    wp_send_json_success(array(
        'permissions' => is_array($permissions) ? $permissions : array(),
        'brands' => is_array($brands) ? $brands : array()
    ));
}
<?php
/**
 * Güvenli rol kurulumu:
 * - Önce subscriber cap'lerini al (yoksa populate_roles ile kur).
 * - Built-in rollerden subscriber, contributor, author, editor'ü sil.
 * - Custom rollerini subscriber cap'leri ile oluştur.
 * - Bir kez çalışsın diye option'la kilitle.
 */

add_action('init', function () {
    // Tek sefer çalıştırma kilidi
    if (get_option('kt_roles_installed')) {
        return;
    }

    $base_caps = array('read' => true);
    $sub = get_role('subscriber');

    if (!$sub && function_exists('populate_roles')) {
        populate_roles();
        $sub = get_role('subscriber');
    }
    if ($sub && is_array($sub->capabilities)) {
        $base_caps = $sub->capabilities;
    }

    foreach (['subscriber','contributor','author','editor'] as $role) {
        if (get_role($role)) {
            remove_role($role);
        }
    }

    $custom_roles = [
        'satis'        => 'Satış',
        'satis_destek' => 'Satış Destek',
        'teknik'       => 'Teknik',
        'finans'       => 'Finans',
        'yonetim'      => 'Yönetim',
        'lojistik'     => 'Lojistik',
        'idari_isler'  => 'İdari İşler',
    ];

    foreach ($custom_roles as $role_key => $role_name) {
        // Varsa önce kaldır (temiz kurulum)
        if (get_role($role_key)) {
            remove_role($role_key);
        }
        add_role($role_key, $role_name, $base_caps);
        // Gerekirse ek cap örneği:
        // if ($r = get_role($role_key)) { $r->add_cap('upload_files'); }
    }

    // 4) Bir daha çalışmasın
    update_option('kt_roles_installed', 1);
}, 1);

// Yeniden çalıştırmak istersen (ör. cap güncelledin):
// delete_option('kt_roles_installed');

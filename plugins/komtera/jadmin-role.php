<?php
/**
 * Plugin Name: JAdmin (Users Manager)
 * Description: Sadece kullanıcı yönetimi için tam yetkili rol. (Add / Edit / Delete / Promote / Duplicate*). Administrator üzerinde işlem yapamaz.
 * Version: 1.0
 */

// Rolü oluştur / güncelle
register_activation_hook(__FILE__, function () {
    // Subscriber baz alınır
    $sub = get_role('subscriber');
    $caps = $sub ? $sub->capabilities : [];

    // Kullanıcı yönetimi kapasiteleri (tek site + multisite uyumlu)
    $caps['read']           = true;
    $caps['list_users']     = true;
    $caps['edit_users']     = true;   // başkalarını düzenleme
    $caps['create_users']   = true;   // yeni kullanıcı ekleme
    $caps['delete_users']   = true;   // kullanıcı silme
    $caps['promote_users']  = true;   // rol değiştirme
    $caps['remove_users']   = true;   // multisite remove
    $caps['add_users']      = true;   // multisite add

    // Eğer “duplicate user” özelliğiniz özel bir cap kontrol ediyorsa
    // çoğu zaman create/edit yeterli olur ama garanti için ekliyoruz:
    $caps['duplicate_users'] = true;

    // Rolü ekle (varsa günceller)
    if (! get_role('jadmin')) {
        add_role('jadmin', 'JAdmin', $caps);
    } else {
        // mevcutsa kapasiteleri enjekte et
        $role = get_role('jadmin');
        foreach ($caps as $cap => $grant) {
            if (!$role->has_cap($cap)) { $role->add_cap($cap, $grant); }
        }
    }
});

// Admin menüsünde sadece Users bölümü görünür (diğer menüler subscriber seviyesinde kalır)
add_action('admin_menu', function () {
    if (current_user_can('jadmin_only_marker')) { return; } // idempotent
    // İsteğe bağlı: başka menüleri gizlemek istersen buraya ekleyebilirsin.
}, 999);

// Güvenlik: JAdmin, administrator üzerinde işlem yapamasın
add_filter('map_meta_cap', function ($caps, $cap, $user_id, $args) {
    // edit_user, remove_user, promote_user, delete_user operasyonlarında hedef kullanıcıyı kontrol et
    if (in_array($cap, ['edit_user', 'remove_user', 'promote_user', 'delete_user'], true)) {
        $target_user_id = isset($args[0]) ? (int)$args[0] : 0;
        if ($target_user_id && user_can($target_user_id, 'administrator') && ! user_can($user_id, 'administrator')) {
            // Engelle
            return ['do_not_allow'];
        }
    }
    return $caps;
}, 10, 4);

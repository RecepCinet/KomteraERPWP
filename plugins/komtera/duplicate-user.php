<?php
/**
 * Plugin Name: Komtera Duplicate User
 * Description: Kullanicinin herseyini kopyalar. (Yetkiler&Alt Yetkiler&Markalar dahil)
 * Author: Recep Cinet
 */

if (!defined('ABSPATH')) exit;

function du_user_meta_blacklist() {
    return [
        'wp_capabilities','wp_user_level','session_tokens','community-events-location',
        'dismissed_wp_pointers','_wp_session_tokens','application_passwords',
        'default_password_nag','password_reset_key',
    ];
}

/** Kullanıcı listesine Duplicate linki ekle */
add_filter('user_row_actions', function($actions, $user_object) {
    if (!current_user_can('create_users')) return $actions;

    $url = wp_nonce_url(
        admin_url('users.php?page=du_duplicate_form&user_id=' . intval($user_object->ID)),
        'du_duplicate_form_' . intval($user_object->ID)
    );
    $actions['du_duplicate'] = '<a href="' . esc_url($url) . '">Duplicate</a>';
    return $actions;
}, 10, 2);

/** Menüye gizli sayfa ekle */
add_action('admin_menu', function() {
    add_users_page('Duplicate User', '', 'create_users', 'du_duplicate_form', 'du_render_form');
});

/** Form ekranı */
function du_render_form() {
    if (!current_user_can('create_users')) wp_die('No permission');

    $user_id = intval($_GET['user_id'] ?? 0);
    if (!$user_id || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'du_duplicate_form_' . $user_id)) {
        wp_die('-Burdan direk kullanilamaz. Oncelikle user listesinden useri secip duplicate seciniz.');
    }

    $user = get_userdata($user_id);
    if (!$user) wp_die('User not found');

    ?>
    <div class="wrap">
        <h1>Duplicate User: <?php echo esc_html($user->user_login); ?></h1>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <?php wp_nonce_field('du_do_duplicate_' . $user_id); ?>
            <input type="hidden" name="action" value="du_do_duplicate">
            <input type="hidden" name="source_id" value="<?php echo intval($user_id); ?>">

            <table class="form-table">
                <tr>
                    <th><label for="new_username">New Username</label></th>
                    <td><input type="text" name="new_username" id="new_username" required></td>
                </tr>
                <tr>
                    <th><label for="new_email">New Email</label></th>
                    <td>
                        <input 
                            type="email" 
                            name="new_email" 
                            id="new_email" 
                            required 
                            value="<?php echo esc_attr('@komtera.com'); ?>"
                        >
                    </td>
                </tr>
            </table>

            <?php submit_button('Duplicate User'); ?>

            <!-- Cancel Button -->
            <a href="<?php echo admin_url('users.php'); ?>" class="button button-secondary">Cancel</a>
        </form>
    </div>
    <?php
}

/** Duplicate işleme */
add_action('admin_post_du_do_duplicate', function() {
    if (!current_user_can('create_users')) wp_die('No permission');

    $source_id   = intval($_POST['source_id'] ?? 0);
    $new_username = sanitize_user($_POST['new_username'] ?? '');
    $new_email    = sanitize_email($_POST['new_email'] ?? '');

    if (!$source_id || empty($new_username) || empty($new_email)) {
        wp_die('Missing fields.');
    }
    if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'du_do_duplicate_' . $source_id)) {
        wp_die('Bad nonce.');
    }
    if (username_exists($new_username)) wp_die('Username already exists.');
    if (email_exists($new_email)) wp_die('Email already exists.');

    $source = get_userdata($source_id);
    if (!$source) wp_die('User not found');

    $userdata = [
        'user_login'   => $new_username,
        'user_pass'    => wp_generate_password(20, true, true),
        'user_email'   => $new_email,
        'first_name'   => $source->first_name,
        'last_name'    => $source->last_name,
        'display_name' => $source->display_name,
        'role'         => $source->roles[0] ?? '',
    ];
    $new_user_id = wp_insert_user($userdata);
    if (is_wp_error($new_user_id)) wp_die($new_user_id->get_error_message());

    // Meta kopyala
    $blacklist = du_user_meta_blacklist();
    $all_meta  = get_user_meta($source_id);
    foreach ($all_meta as $key => $values) {
        if (in_array($key, $blacklist, true)) continue;
        foreach ($values as $v) {
            add_user_meta($new_user_id, $key, maybe_unserialize($v));
        }
    }

    // Rol tekrar set et
    if (!empty($userdata['role'])) {
        $nu = new WP_User($new_user_id);
        $nu->set_role($userdata['role']);
    }

    wp_safe_redirect(admin_url('user-edit.php?user_id=' . $new_user_id . '&du_duplicated=1'));
    exit;
});

/** Başarı bildirimi */
add_action('admin_notices', function() {
    if (isset($_GET['du_duplicated'])) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>User duplicated successfully.</strong></p></div>';
    }
});
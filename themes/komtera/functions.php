<?php
function theme_enqueue_scripts() {
    // Ana stil dosyaları varsa zaten burada çağrılır

    // Main.js ekle
    wp_enqueue_script(
        'komtera',
        get_stylesheet_directory_uri() . '/js/main.js',
        array('jquery'), // jQuery gerekirse
        filemtime(get_stylesheet_directory() . '/js/main.js'), // cache busting için
        true // footer'da yükle
    );
}
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');


require_once get_template_directory() . '/_conn.php';

require_once get_template_directory() . '/inc/pasif.php';

require_once get_template_directory() . '/inc/roller.php';

require_once get_template_directory() . '/inc/init.php';
require_once get_template_directory() . '/inc/widgets.php';
// Yetkiler ve Alt Yetkiler:
require_once get_template_directory() . '/inc/yetkiler.php';
require_once get_template_directory() . '/inc/menu.php';
require_once get_template_directory() . '/inc/admin_bar.php';
require_once get_template_directory() . '/inc/version.php';
require_once get_template_directory() . '/inc/users.php';




// JAdmin için Admin Bar'da + New → User öğesini gizle
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_user_logged_in()) return;
    $user = wp_get_current_user();
    if (in_array('jadmin', (array) $user->roles, true)) {
        $wp_admin_bar->remove_node('new-user');
        $wp_admin_bar->remove_node('new-content');
    }
}, 9999);
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




// User edit sayfasında kaydet butonunu sağ üstte sabit göster
function komtera_admin_styles() {
    global $pagenow;
    if ($pagenow == 'user-edit.php' || $pagenow == 'profile.php') {
        ?>
        <style>
        /* Kaydet butonunu sağ altta sabit konumda göster */
        #submit {
            position: fixed !important;
            bottom: 20px !important;
            right: 20px !important;
            z-index: 9999 !important;
            background: #0073aa !important;
            border-color: #005a87 !important;
            color: #fff !important;
            padding: 8px 20px !important;
            border-radius: 4px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            text-shadow: 0 -1px 1px #005a87 !important;
            box-shadow: 0 1px 0 #005a87 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }
        
        #submit:hover {
            background: #005a87 !important;
            border-color: #004067 !important;
            box-shadow: 0 1px 0 #004067 !important;
        }
        
        #submit:active {
            transform: translateY(1px) !important;
        }
        
        /* Responsive design için */
        @media (max-width: 782px) {
            #submit {
                bottom: 10px !important;
                right: 10px !important;
                padding: 6px 15px !important;
                font-size: 12px !important;
            }
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'komtera_admin_styles');

// JAdmin için Admin Bar'da + New → User öğesini gizle
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_user_logged_in()) return;
    $user = wp_get_current_user();
    if (in_array('jadmin', (array) $user->roles, true)) {
        $wp_admin_bar->remove_node('new-user');
        $wp_admin_bar->remove_node('new-content');
    }
}, 9999);
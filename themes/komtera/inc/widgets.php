<?php

// Standard Widgetleri siliyor? Wordpress Habeler ve Etkinlikler!
add_action('wp_dashboard_setup', function () {
    // Etkinlik
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    // WordPress haberleri
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    // Welcome panelini kaldır
    remove_action('welcome_panel', 'wp_welcome_panel');
    // İstersen diğerlerini de:
    // remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); // Hızlı taslak
    // remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // Şu an (bazı sürümlerde)
});

add_filter('get_user_option_screen_layout_dashboard', function() {
    return 3; // her kullanıcıda 1 sütun
});




if ( current_user_can('administrator') ) {
    include(get_stylesheet_directory() . '/widgets/template.php');
}

// Ana Widgets
include( get_stylesheet_directory() . '/widgets/kur.php' );
include( get_stylesheet_directory() . '/widgets/release_notes.php' );

// Eski gadgets'lardan dönüştürülen widgets
include( get_stylesheet_directory() . '/widgets/onay_is_atama.php' );
include( get_stylesheet_directory() . '/widgets/yakinda_kapanacak_firsatlar.php' );
include( get_stylesheet_directory() . '/widgets/siparis_ozel_sku.php' );
include( get_stylesheet_directory() . '/widgets/satis_hedefleri.php' );


// Gerekirse welcome panelin açık olduğundan emin ol
add_action('load-index.php', function(){
    $uid = get_current_user_id();
    if ( 1 != get_user_meta($uid, 'show_welcome_panel', true) ) {
        update_user_meta($uid, 'show_welcome_panel', 0);
    }
});

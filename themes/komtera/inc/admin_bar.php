<?php

add_action('admin_bar_menu', function($wp_admin_bar) {
    $icon_url='https://erp.komtera.com/wp-content/uploads/2025/08/komtera_logo.png';
    $wp_admin_bar->add_node([
        'id'    => 'my-mini-icon',
        'title' => '<img src="'.esc_url($icon_url).'" style="height:22px; width:auto; vertical-align:middle;" alt=""/>',
        'href'  => admin_url(),
    ]);
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('site-name');
    $wp_admin_bar->remove_node('help');
}, 999);

// Sağ üstteki yardım tab’ını kaldır
add_action('admin_head', function () {
    echo '<style>
        #contextual-help-link-wrap { display: none !important; }
    </style>';
});

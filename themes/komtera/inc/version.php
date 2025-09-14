<?php

// Admin footer'ı tamamen kaldır - daha güçlü yöntem
add_action('admin_head', function() {
    echo '<style>
        #wpfooter, .wp-admin #wpfooter { 
            display: none !important; 
            height: 0 !important; 
            padding: 0 !important; 
            margin: 0 !important; 
        }
        #wpcontent, #wpbody-content {
            padding-bottom: 0 !important;
        }
        .wp-admin #wpbody {
            padding-bottom: 0 !important;
        }
    </style>';
});

// Footer hook'larını tamamen kaldır
remove_action('wp_footer', 'wp_admin_bar_render', 1000);
add_filter('admin_footer_text', '__return_empty_string');
add_filter('update_footer', '__return_empty_string', 999);

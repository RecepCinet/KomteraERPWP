<?php

// Footer yazısını özelleştirmek
add_filter('admin_footer_text', function () {
    return '©2026 Komtera Teknoloji A.Ş.';
});

add_filter('update_footer', function () {
    return 'Komtera ERP v3.2026 Alfa 7';
}, 999);

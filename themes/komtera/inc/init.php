<?php
global $kur_tarih;
// wp-content/mu-plugins/force-admin-only.php

function _is_login_request(): bool {
    $pagenow = $GLOBALS['pagenow'] ?? '';
    if ($pagenow === 'wp-login.php' || $pagenow === 'wp-register.php') return true;
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return (strpos($uri, 'wp-login.php') !== false)
        || (strpos($uri, 'wp-register.php') !== false)
        || (strpos($uri, 'wp-signup.php') !== false);
}

function _is_public_endpoint(): bool {
    if (wp_doing_ajax()) return true;
    if (defined('REST_REQUEST') && REST_REQUEST) return true;
    if (defined('DOING_CRON') && DOING_CRON) return true;
    if (defined('WP_CLI') && WP_CLI) return true;
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($uri === '/robots.txt') return true;
    if (strpos($uri, 'sitemap') !== false) return true;
    return false;
}

// 1) Ön yüzde davranış:
// - Login olduysa: tüm ön yüz isteklerini admin'e gönder
// - Login değilse ve public/login değilse: login'e gönder (login sonrası admin)
add_action('template_redirect', function () {
    if (is_user_logged_in()) {
        // Admin veya login ekranında değilsek admin'e
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (stripos($uri, '/wp-admin') === false && stripos($uri, 'wp-login.php') === false) {
            nocache_headers();
            wp_safe_redirect( admin_url() );
            exit;
        }
        return;
    }

    if (!_is_public_endpoint() && !_is_login_request()) {
        nocache_headers();
        wp_safe_redirect( wp_login_url( admin_url() ) );
        exit;
    }
}, 0);

// 2) Login ekranı: zaten login ise admin'e YALNIZCA logout hariç
add_action('login_init', function () {
    $action = $_REQUEST['action'] ?? '';
    // logout (ve şifre akışlarını) serbest bırak
    if (in_array($action, ['logout', 'rp', 'resetpass', 'postpass'], true)) {
        return;
    }
    if (is_user_logged_in()) {
        wp_safe_redirect( admin_url() );
        exit;
    }
});

// 3) Başarılı login sonrası her zaman admin
add_filter('login_redirect', function ($redirect_to, $requested, $user) {
    return admin_url();
}, 10, 3);

// 4) Logout sonrası nereye? (login sayfası önerilir)
add_filter('logout_redirect', function ($redirect_to, $requested, $user) {
    return wp_login_url(); // veya home_url('/')
}, 10, 3);





































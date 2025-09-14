<?php
/**
 * Plugin Name: Custom Critical Error Screen
 * Description: Fatal error ekranını özelleştirir; kullanıcı, URL, IP, User-Agent gösterir ve feedback maili gönderir.
 */

function cces_get_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff) {
        $parts = array_map('trim', explode(',', $xff));
        foreach ($parts as $p) {
            if (filter_var($p, FILTER_VALIDATE_IP)) {
                $ip = $p;
                break;
            }
        }
    }
    return $ip;
}

add_action('admin_post_critical_feedback', 'cces_handle_feedback');
add_action('admin_post_nopriv_critical_feedback', 'cces_handle_feedback');
function cces_handle_feedback() {
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'critical_feedback')) {
        status_header(400);
        echo 'Invalid request.'; exit;
    }
    $admin_email = get_option('admin_email');
    if (!$admin_email) {
        $admin_email = 'webmaster@' . parse_url(home_url(), PHP_URL_HOST);
    }
    $username = sanitize_text_field($_POST['username'] ?? 'Misafir');
    $url      = esc_url_raw($_POST['url'] ?? '');
    $ip       = sanitize_text_field($_POST['ip'] ?? '');
    $ua       = sanitize_text_field($_POST['ua'] ?? '');
    $note     = wp_kses_post($_POST['note'] ?? '');
    $when     = date_i18n('Y-m-d H:i:s');
    $subject = 'Kritik Hata Feedback';
    $body = "Zaman: {$when}\n"
        . "Kullanıcı: {$username}\n"
        . "URL: {$url}\n"
        . "IP: {$ip}\n"
        . "User-Agent: {$ua}\n"
        . "Not:\n{$note}\n";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    $ok = wp_mail($admin_email, $subject, $body, $headers);
    status_header($ok ? 200 : 500);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Geri Bildirim</title></head><body style="font-family:sans-serif;padding:24px">';
    echo $ok ? '<h2>Teşekkürler!</h2><p>Geri bildiriminiz gönderildi.</p>'
        : '<h2>Hata</h2><p>Geri bildirim gönderilemedi.</p>';
    echo '<p><a href="' . esc_url(home_url('/')) . '">Ana sayfaya dön</a></p>';
    echo '</body></html>';
    exit;
}

add_filter('wp_die_handler', function () {
    return function ($message, $title = '', $args = array()) {
        $username = 'Misafir';
        if (function_exists('is_user_logged_in') && is_user_logged_in()) {
            $user = wp_get_current_user();
            if ($user && $user->exists()) {
                $username = $user->user_login;
            }
        }
        $url = esc_url_raw($_SERVER['REQUEST_URI'] ?? '');
        $ip  = cces_get_ip();
        $ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $nonce = function_exists('wp_create_nonce') ? wp_create_nonce('critical_feedback') : '';
        status_header(500);
        echo '<p><a href="' . esc_url( wp_logout_url( home_url() ) ) . '" class="btn">Çıkış Yap</a></p>';

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Kritik Hata</title>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<style>
                body{font-family:sans-serif;background:#f6f7f9;margin:0}
                .wrap{max-width:720px;margin:8vh auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 24px rgba(0,0,0,.06);padding:28px}
                h1{margin:0 0 8px;color:#b00020}
                .meta{background:#fafafa;border:1px solid #eee;border-radius:8px;padding:12px;margin:16px 0;font-size:14px}
                label{display:block;font-weight:600;margin:8px 0 6px}
                textarea{width:100%;box-sizing:border-box;border:1px solid #ccc;border-radius:8px;padding:10px;min-height:90px}
                .btn{margin-top:12px;padding:10px 18px;border-radius:6px;background:#111;color:#fff;font-weight:600;border:0;cursor:pointer}
                .error-box{background:#fee;border:1px solid #fbb;border-radius:8px;padding:12px;margin-top:16px;color:#900}
              </style>';
        echo '</head><body><div class="wrap">';
        echo "<h1>ERP'de bir hata oluştu</h1>";
        echo '<p>Sayfa şu anda görüntülenemiyor. En kısa sürede çözeceğiz.</p>';

        // hata mesajı buraya
        if (!empty($message)) {
            echo '<div class="error-box"><strong>Hata Detayı:</strong><br>' . wp_kses_post($message) . '</div>';
        }

        echo '<div class="meta">';
        echo '<p><strong>Kullanıcı:</strong> ' . esc_html($username) . '</p>';
        echo '<p><strong>IP:</strong> ' . esc_html($ip) . '</p>';
        echo '<p><strong>URL:</strong> ' . esc_html($url) . '</p>';
        echo '<p><strong>User-Agent:</strong> ' . esc_html($ua) . '</p>';
        echo '</div>';

        $action = esc_url(admin_url('admin-post.php'));
        echo '<form method="post" action="' . $action . '">';
        echo '<input type="hidden" name="action" value="critical_feedback">';
        echo '<input type="hidden" name="_wpnonce" value="' . esc_attr($nonce) . '">';
        echo '<input type="hidden" name="username" value="' . esc_attr($username) . '">';
        echo '<input type="hidden" name="url" value="' . esc_attr($url) . '">';
        echo '<input type="hidden" name="ip" value="' . esc_attr($ip) . '">';
        echo '<input type="hidden" name="ua" value="' . esc_attr($ua) . '">';
        echo '<label for="note">Kısa not (isteğe bağlı):</label>';
        echo '<textarea id="note" name="note"></textarea>';
        echo '<button type="submit" class="btn">E-posta Gönder</button>';
        echo '</form>';
        echo '</div></body></html>';
        die();
    };
});

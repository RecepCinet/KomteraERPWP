<?php
// 0) Güvenlik: t ve f parametrelerini daralt
$params = $_GET;

$tablo  = isset($params['t']) ? preg_replace('~[^a-zA-Z0-9_-]~', '', $params['t']) : '';
$folder = isset($params['f']) ? preg_replace('~[^a-zA-Z0-9_-]~', '', $params['f']) : 'tablolar';

if ($tablo === '') {
    http_response_code(400);
    exit('Parametre eksik: t');
}

// 1) WP'yi yükle
$dir = __DIR__;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir.'/wp-load.php')) { require_once $dir.'/wp-load.php'; break; }
    $dir = dirname($dir);
}
if (!function_exists('__')) { http_response_code(500); exit('WP yüklenemedi'); }

// 2) (Önemli) Mevcut kullanıcı diline geç
switch_to_locale( get_user_locale() );   // admin İngilizce ise en_US olur

// 3) Textdomain’i yükle (tema için)
load_theme_textdomain('komtera', get_stylesheet_directory().'/languages');

// Sonra include…
require __DIR__.'/pq.php';
require __DIR__.'/_'.$folder.'/kt_'.$tablo.'.html';
require __DIR__.'/_'.$folder.'/kt_'.$tablo.'_js.php';

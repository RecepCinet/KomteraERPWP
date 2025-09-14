<?php
/**
 * Plugin Name: Komtera SMTP EMail
 * Description: SMTP Ayarlari
 * Author: Recep Cinet
 */

if (!defined('ABSPATH')) exit;

// From e-mail & name
add_filter('wp_mail_from', function($from){ return 'bilgi@komtera.com'; });
add_filter('wp_mail_from_name', function($name){ return 'bilgi@komtera.com'; });

// HTML içerik istiyorsanız (opsiyonel)
// add_filter('wp_mail_content_type', fn() => 'text/html');

add_action('phpmailer_init', function($phpmailer){
    // Ortam değişkeni/constant üzerinden de okuyabilirsiniz
    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.office365.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = 'bilgi@komtera.com';
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->Password = '2F&g1D4-5!-ad7S!';
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port = 587;
    $phpmailer->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
});

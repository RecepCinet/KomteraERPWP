<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

// Set content type first
header('Content-Type: application/json');

try {
    session_start();

    // Load WordPress - try multiple paths
    $wp_load_paths = array(
        dirname(__FILE__) . '/../../../../wp-load.php',
        dirname(__FILE__) . '/../../../wp-load.php',
        dirname(__FILE__) . '/../../wp-load.php',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'
    );

    $wp_loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            $wp_loaded = true;
            break;
        }
    }

    if (!$wp_loaded) {
        throw new Exception('WordPress could not be loaded');
    }

    $users = get_users(array(
        'orderby' => 'display_name',
        'order' => 'ASC'
    ));

    $data = array();
    foreach ($users as $user) {
        $data[] = array(
            'id' => $user->ID,
            'user_login' => $user->user_login,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name
        );
    }

    $response = [
        'success' => true,
        'data' => $data
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'data' => []
    ]);
}
?>

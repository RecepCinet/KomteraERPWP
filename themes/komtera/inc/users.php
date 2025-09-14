<?php


// Users ekranında (users.php) pasif kullanıcı satırlarını gri göster

// Ticket: 1651 Active/Inactive users development
add_action('admin_head-users.php', function () {
    $u = wp_get_current_user();
    if (!array_intersect(['manage_options','administrator', 'jadmin'], (array)$u->roles)) {
        return;
    }
    // Pasif kullanıcıları çek
    $ids = get_users([
        'meta_key'   => 'account_disabled',
        'meta_value' => '1',
        'fields'     => 'ID',
        'number'     => -1,
    ]);

    if (empty($ids)) return;

    echo "<style>";
    foreach ($ids as $id) {
        // Her satır tr#user-{ID} şeklinde
        echo "
        #the-list tr#user-{$id} { background: #fff7f7 !important; }
        #the-list tr#user-{$id} td, 
        #the-list tr#user-{$id} th { color: #FCC !important; }
        #the-list tr#user-{$id} a { color: #FCC !important; }
        ";
        // İstersen opaklık vermek için (yorumdan çıkarabilirsin):
        // echo "#the-list tr#user-{$id} { opacity: .6; }";
    }
    echo "</style>";
});
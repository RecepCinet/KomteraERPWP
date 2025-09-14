<?php

$current_user_id = get_current_user_id();

// Tüm custom meta listesini al
$all_meta = get_user_meta($current_user_id);

echo '<pre>';
print_r($all_meta);
echo '</pre>';


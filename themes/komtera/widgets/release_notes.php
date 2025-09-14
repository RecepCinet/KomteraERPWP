<?php

function release_notes_dashboard_widget() {
    $rn = file_get_contents(get_stylesheet_directory() . "/release_notes.txt");
    $rn = str_replace("\n", "<br />", $rn);
    echo '<div style="max-height:160px; overflow-y:auto; padding-right:8px;">';
    echo "$rn";
    echo '</div>';
}

function add_release_notes_dashboard_widget() {
    wp_add_dashboard_widget(
        'release_notes',
        'Release Notes',
        'release_notes_dashboard_widget'
    );
}

add_action('wp_dashboard_setup', 'add_release_notes_dashboard_widget');

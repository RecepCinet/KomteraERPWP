<?php

// Diger WIDGETler buraya listelensin include ile mesela!
function my_custom_dashboard_widget() {
    echo "<h3>Test icin widget</h3>";
    echo "<p>Ornek calisma icin</p>";
}

function add_my_custom_dashboard_widget() {
    wp_add_dashboard_widget(
        'template_widget',         // Widget ID
        'Template',               // Başlık
        'my_custom_dashboard_widget'   // İçeriği yazdıran fonksiyon
    );
}
add_action('wp_dashboard_setup', 'add_my_custom_dashboard_widget');

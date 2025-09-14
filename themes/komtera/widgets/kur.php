<?php
// MS SQL için: TOP 1 + ORDER BY
function aa_get_latest_kur(){
    global $conn;
    $stmt = $conn->prepare("
        SELECT TOP 1 usd, eur, tarih
        FROM aa_erp_kur
        ORDER BY tarih DESC
    ");
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['usd'=>null,'eur'=>null,'tarih'=>null];
}

function kur_dashboard_widget() {
    $data  = aa_get_latest_kur();
    $usd   = $data['usd'];
    $eur   = $data['eur'];
    ?>
    <h1 align="center">$ <?php echo $usd; ?> &nbsp; € <?php echo $eur; ?></h1>
    <?php
}

function add_kur_dashboard_widget() {
    $data  = aa_get_latest_kur();
    $tarih = $data['tarih'];
    if ($tarih) {
        $t = is_numeric($tarih) ? (int)$tarih : strtotime($tarih);
        if ($t) $tarih = date('d.m.Y', $t);
    }
    $title = 'Günlük Kur' . ($tarih ? ' (' . $tarih . ')' : '');
    wp_add_dashboard_widget(
        'kur_widget',
        $title,
        'kur_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'add_kur_dashboard_widget');

add_action('admin_head', function() {
    echo '<style>
        #kur_widget { background:#000 !important; color:#fff !important; }
        #kur_widget .hndle, #kur_widget h2 { color:#fff !important; }
        #kur_widget h1, #kur_widget p {
            font-family: Consolas, monospace;
            font-size: 32px;
            color: greenyellow !important;
        }
    </style>';
});

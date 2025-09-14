<?php
$dir = __DIR__;
$found = false;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($dir . '/wp-load.php')) {
        require_once $dir . '/wp-load.php';
        $found = true;
        break;
    }
    $dir = dirname($dir);
}

if (!$found) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "wp-load.php bulunamadı.\n";
    echo "Başlangıç dizini: " . __DIR__ . "\n";
    exit;
}


$scheme = get_user_option('admin_color'); // örn: 'fresh', 'light', 'blue', ...

echo "#$scheme#";



// Bu dosya sadece iframe içinden çalışsın
if (empty($_SERVER['HTTP_REFERER'])) {
    die('Direct access not allowed.');
}

// Sadece kendi domainimizden gelsin
$allowed_domain = $_SERVER['HTTP_HOST'];
$referer_domain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

if ($referer_domain !== $allowed_domain) {
    die('Access denied. Only allowed from: ' . $allowed_domain);
}
?>


<div id="grid_json" style="margin:auto;"></div>
<link rel="stylesheet" href="pqgrid/pqgrid.min.css"/>
<link rel="stylesheet" href="pqgrid/pqgrid.ui.min.css"/>
<link rel='stylesheet' href='pqgrid/themes/gray/pqgrid.css'/>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="pqgrid/pqgrid.min.js"></script>
<script src="pqgrid/localize/pq-localize-tr.js"></script>
<script src="pqgrid/pqTouch/pqtouch.min.js"></script>
<script src="pqgrid/jsZip-2.5.0/jszip.min.js"></script>
<script src="pqgrid/js/base64.min.js"></script>
<script src="pqgrid/js/FileSaver.js"></script>


<script>
    var data = [{
        rank: 1,
        company: 'Exxon Mobil',
        revenues: 339938.0,
        profits: 36130.0
    },
        {
            rank: 2,
            company: 'Wal-Mart Stores',
            revenues: 315654.0,
            profits: 11231.0
        },
        {
            rank: 3,
            company: 'Royal Dutch Shell',
            revenues: 306731.0,
            profits: 25311.0
        },
        {
            rank: 4,
            company: 'BP',
            revenues: 267600.0,
            profits: 22341.0
        },
        {
            rank: 5,
            company: 'General Motors',
            revenues: 192604.0,
            profits: -10567.0
        },
        {
            rank: 6,
            company: 'Chevron',
            revenues: 189481.0,
            profits: 14099.0
        },
        {
            rank: 7,
            company: 'DaimlerChrysler',
            revenues: 186106.3,
            profits: 3536.3
        },
        {
            rank: 8,
            company: 'Toyota Motor',
            revenues: 185805.0,
            profits: 12119.6
        },
        {
            rank: 9,
            company: 'Ford Motor',
            revenues: 177210.0,
            profits: 2024.0
        },
        {
            rank: 10,
            company: 'ConocoPhillips',
            revenues: 166683.0,
            profits: 13529.0
        },
        {
            rank: 11,
            company: 'General Electric',
            revenues: 157153.0,
            profits: 16353.0
        },
        {
            rank: 12,
            company: 'Total',
            revenues: 152360.7,
            profits: 15250.0
        },
        {
            rank: 13,
            company: 'ING Group',
            revenues: 138235.3,
            profits: 8958.9
        },
        {
            rank: 14,
            company: 'Citigroup',
            revenues: 131045.0,
            profits: 24589.0
        },
        {
            rank: 15,
            company: 'AXA',
            revenues: 129839.2,
            profits: 5186.5
        },
        {
            rank: 16,
            company: 'Allianz',
            revenues: 121406.0,
            profits: 5442.4
        },
        {
            rank: 17,
            company: 'Volkswagen',
            revenues: 118376.6,
            profits: 1391.7
        },
        {
            rank: 18,
            company: 'Fortis',
            revenues: 112351.4,
            profits: 4896.3
        },
        {
            rank: 19,
            company: 'Crédit Agricole',
            revenues: 110764.6,
            profits: 7434.3
        },
        {
            rank: 20,
            company: 'American Intl. Group',
            revenues: 108905.0,
            profits: 10477.0
        }
    ];

    var obj = {
        width: "80%",
        height: 400,
        resizable: true,
        title: "Grid From JSON",
        showBottom: false,
        scrollModel: {
            autoFit: true
        },
        dataModel: {
            data: data
        }
    };
    grid=$("#grid_json").pqGrid(obj);
</script>

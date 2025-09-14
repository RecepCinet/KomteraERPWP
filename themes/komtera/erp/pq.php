<?PHP
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


require_once '../inc/yetkiler.php';

$current_user_id = get_current_user_id();
if (!$current_user_id) {
    echo json_encode(['error' => 'Giriş yapmış kullanıcı bulunamadı.']);
    exit;
}
//echo "#$current_user_id#<br />";
//
//// Kullanıcının seçilmiş yetki ve alt yetkilerini çek
//$current_user_permissions = get_user_meta($current_user_id, 'my_permissions_matrix', true);
//
//print_r($current_user_permissions);
//
//
//die();


 $user_locale = get_user_locale();
 switch_to_locale($user_locale);


// $fresh,light,blue,coffee,ectoplasm,midnight,ocean,sunrise
switch ($scheme) {
    case 'fresh':
        $theme="office";
        break;
    case 'light':
        $theme="gray";
        break;
    case 'modern':
        $theme="blue";
        break;
    case 'blue':
        $theme="steelblue";
        break;
    case 'coffee':
        $theme="brown";
        break;
    case 'ectoplasm':
        $theme="pruple";
        break;
    case 'midnight':
        $theme="rosybrown";
        break;
    case 'ocean':
        $theme="tan";
        break;
    case 'sunrise':
        $theme="violet";
        break;
    default:
        $theme="gray";
}
$theme="gray";

// bootstrap,brown,chocolate,cocoa,crimson,gray,green,indigo,office,purple,red,rosybrown,sandybrown,steelblue,tan,violet,yellow


//// Bu dosya sadece iframe içinden çalışsın
//if (empty($_SERVER['HTTP_REFERER'])) {
//    die('Direct access not allowed.');
//}
//
//// Sadece kendi domainimizden gelsin
//$allowed_domain = $_SERVER['HTTP_HOST'];
//$referer_domain = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
//
//if ($referer_domain !== $allowed_domain) {
//    die('Access denied. Only allowed from: ' . $allowed_domain);
//}
//?>

<link rel="stylesheet" href="pqgrid/pqgrid.min.css"/>
<link rel="stylesheet" href="pqgrid/pqgrid.ui.min.css"/>
<link rel='stylesheet' href='pqgrid/themes/<?PHP echo $theme; ?>/pqgrid.css'/>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="pqgrid/pqgrid.min.js"></script>
<script src="pqgrid/localize/pq-localize-<?PHP echo substr(get_user_locale(),0,2); ?>.js"></script>
<script src="pqgrid/pqTouch/pqtouch.min.js"></script>
<script src="pqgrid/jsZip-2.5.0/jszip.min.js"></script>
<script src="pqgrid/js/base64.min.js"></script>
<script src="pqgrid/js/FileSaver.js"></script>

<style>
    .pq-grid {
        box-shadow: 4px 4px 10px 0px rgba(50, 50, 50, 0.75);
        margin-bottom: 12px;
        font-family: Arial;
        font-size: 12px;
    }
    .pq-toolbar button {
        margin: 0px 5px;
    }
    button.delete_btn {
        margin: -3px 0px;
        height: 30px;
    }
    .pq-grid-row {
        height: 30px !important;
    }
    .pq-grid .pq-grid-cell {
        line-height: 30px !important;
    }
    pq-row-delete > .pq-grid-cell {
        background-color: pink;
    }
    pq-grid-number-col {
        padding: 5px 5px;
    }
</style>
<script>
    function refreshDV() {
        grid.refreshDataAndView();
        //alert("yes");
    }

    function ExcelKaydet() {
        blob = grid.exportData({
            url: "export.php",
            format: 'xls',
            nopqdata: true, //applicable for JSON export.
            render: false
        });
        if (typeof blob === "string") {
            blob = new Blob([blob]);
        }
        saveAs(blob, new Date().toISOString() + ".xls");
    }
</script>
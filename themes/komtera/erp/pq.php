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
    echo json_encode(['error' => __('Giriş yapmış kullanıcı bulunamadı.','komtera')]);
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
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script src="pqgrid/js/base64.min.js"></script>
<script src="pqgrid/js/FileSaver.js"></script>
<script>
    function refreshDV() {
        grid.refreshDataAndView();
        //alert("yes");
    }
    function ExcelKaydet(marka) {
        if (!marka) {
            alert('Marka bilgisi eksik!');
            return;
        }

        // Veritabanından veriyi çek
        $.ajax({
            url: '_service/export_fiyat_listesi.php?marka=' + encodeURIComponent(marka),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    // Excel oluştur - hiç müdahale etme, olduğu gibi bas
                    var ws = XLSX.utils.json_to_sheet(response.data);
                    var wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, marka);

                    // Excel dosyasını indir
                    var filename = marka + '_' + new Date().toISOString().split('T')[0] + '.xlsx';
                    XLSX.writeFile(wb, filename);

                    alert(response.data.length + ' kayıt export edildi.');
                } else {
                    alert('Export hatası: ' + (response.message || 'Veri bulunamadı'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Export hatası:', error);
                alert('Export hatası: ' + error);
            }
        });
    }

    function ExceldenAl(marka) {
        if (!marka) {
            alert('Marka bilgisi eksik!');
            return;
        }

        var input = document.createElement('input');
        input.type = 'file';
        input.accept = '.xlsx,.xls';

        input.onchange = function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function(e) {
                try {
                    var data = e.target.result;
                    var workbook = XLSX.read(data, {type: 'binary'});
                    var firstSheetName = workbook.SheetNames[0];
                    var worksheet = workbook.Sheets[firstSheetName];
                    var jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1});

                    // İlk satır başlık, diğerleri veri
                    if (jsonData.length > 1) {
                        var headers = jsonData[0];
                        var rows = jsonData.slice(1);

                        // Excel verilerini hazırla
                        var records = rows.map(function(row) {
                            var obj = {};
                            headers.forEach(function(header, i) {
                                obj[header] = row[i];
                            });
                            return obj;
                        });

                        // Sunucuya kaydet
                        $.ajax({
                            url: '_service/save_fiyat_listesi.php',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({
                                marka: marka,
                                records: records
                            }),
                            success: function(response) {
                                if (response.success) {
                                    alert(response.message);
                                    // Grid'i yenile
                                    grid.refreshDataAndView();
                                } else {
                                    alert('Hata: ' + response.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Kayıt hatası:', error);
                                alert('Kayıt hatası: ' + error);
                            }
                        });

                    } else {
                        alert('Excel dosyası boş veya sadece başlık satırı içeriyor.');
                    }
                } catch(err) {
                    console.error('Import hatası:', err);
                    alert('Excel dosyası yüklenirken hata oluştu: ' + err.message);
                }
            };

            reader.readAsBinaryString(file);
        };

        input.click();
    }
</script>
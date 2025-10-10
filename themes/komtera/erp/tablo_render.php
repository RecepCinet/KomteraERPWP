<?php
$params = $_GET;
$tablo=$params['t'];
$folder=$params['f'];
if (!isset($params['f'])) {
    $folder='tablolar';
}

// Debug: Log all GET parameters
error_log("tablo_render.php - GET params: " . print_r($_GET, true));
?><script>
    console.log("tablo_render.php loaded");
    console.log("Table: <?PHP echo $tablo; ?>");
    console.log("GET params:", <?php echo json_encode($_GET); ?>);
</script>
<?php
include 'pq.php';
include '_' . $folder . '/kt_' . $tablo . '.html';
include '_' . $folder . '/kt_' . $tablo . '_js.php';
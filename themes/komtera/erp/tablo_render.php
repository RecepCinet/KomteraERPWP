<?php
$params = $_GET;
$tablo=$params['t'];
$folder=$params['f'];
if (!isset($params['f'])) {
    $folder='tablolar';
}
?><script>
    console.log("<?PHP echo $tablo; ?>");
</script>
<?php
include 'pq.php';
include '_' . $folder . '/kt_' . $tablo . '.html';
include '_' . $folder . '/kt_' . $tablo . '_js.php';
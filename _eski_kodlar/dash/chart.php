<?php 
include 'phpChart_Lite/phpChart.php';

$type=$_GET['type'];
$key=$_GET['key'];
$val=$_GET['val'];

$keyparca= explode("|", $key);
$valparca= array_map('intval', explode('|', $val));

$yaz=array($valparca);


$typeyaz="basic_chart";


$pc = new C_PhpChartX($yaz,'basic_chart');
$pc->set_animate(true);
$pc->set_title(array('text'=>$_GET['title']));
$pc->draw();

?>


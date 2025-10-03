<?PHP
include './_func.php';

$key=$_GET['key'];
$val=$_GET['val'];
$title=$_GET['title'];
$xtitle=GET('xtitle');
$ytitle=GET('xtitle');

$yaz="";
$keyparca= explode("|", $key);
$valparca= explode("|", $val);

for ($i = 1; $i < count($keyparca); $i++) {
    if ($i>1) {
        $yaz.=",";
    }
    $yaz.="['" . $keyparca[$i] . "'," . $valparca[$i] . "]";
}
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawMultSeries);

function drawMultSeries() {
      var data = google.visualization.arrayToDataTable([
        ['', ''],
        <?PHP echo $yaz; ?>
      ]);


      var options = {
        title: '<?PHP echo $title; ?>',
        chartArea: {width: '100%'},
        hAxis: {
          title: '<?PHP echo $ytitle; ?>',
          minValue: 0
        },
        vAxis: {
          title: '<?PHP echo $xtitle; ?>'
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
</script>
  </head>
  <body>
    <div id="chart_div" style="width: 100%; height: 80%;"></div>
  </body>
</html>

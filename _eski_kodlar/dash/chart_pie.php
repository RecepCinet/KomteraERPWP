<?PHP
$key=$_GET['key'];
$val=$_GET['val'];
$title=$_GET['title'];

$yaz="['Task', 'Hours per Day'],";
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
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          <?PHP echo $yaz; ?>
        ]);
        var options = {
          title: '<?PHP echo $title; ?>',
          is3D: true,
        };
        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="piechart" style="width: 100%; height: 80%;"></div>
  </body>
</html>

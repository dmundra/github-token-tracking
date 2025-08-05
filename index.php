<?php

require __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();
$curl->setHeader('Authorization', 'Bearer <GitHub Token>');
$curl->get('https://api.github.com/rate_limit');

if ($curl->error) {
  echo 'Error: ' . $curl->errorMessage . "\n";
  $curl->diagnose();
} else {
  // echo 'Response:' . "\n<pre>";
  // var_dump($curl->response);
  // var_dump($curl->requestHeaders);
  // var_dump($curl->responseHeaders);
  $response = $curl->response;
  $limit = $response->rate->limit;
  $remaining = $response->rate->remaining;
  $used = $response->rate->used;
  date_default_timezone_set('America/Los_Angeles');
  $reset = date('l jS \of F Y h:i:s A', $response->rate->reset);
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <title>GitHub Token Tracking</title>
   <script src="https://www.gstatic.com/charts/loader.js"></script>
   <script>
      // https://developers.google.com/chart/interactive/docs/gallery/gauge
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['GitHub Requests', <?php echo $used; ?>],
        ]);

        var options = {
          width: 800, height: 240,
          redFrom: <?php echo $limit - 440; ?>, redTo: <?php echo $limit; ?>,
          yellowFrom: <?php echo $limit - 880; ?>, yellowTo: <?php echo $limit - 440; ?>,
          minorTicks: 5,
          max: <?php echo $limit; ?>,
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

        chart.draw(data, options);

        setInterval(function() {
          fetch('github-requests.php', options)
            .then(response => response.json())
            .then(body => {
              data.setValue(0, 1, body.used);
              chart.draw(data, options);
              document.getElementById('reset').innerHTML = 'Requests reset by ' + body.reset;
            });
        }, 5000);
      }
    </script>
  </head>
  <body>
    <h1>GitHub Token Tracking</h1>
    <div id="chart_div" style="width: 800px; height: 240px;"></div>
    <p id="reset"><?php echo 'Requests reset by ' . $reset; ?></p>
  </body>
</html>

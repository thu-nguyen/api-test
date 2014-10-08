<?php
include_once("function.php");
$reportFiles = array(
	UPDATE_SCORE => array(
		'title'  => 'User Score Action',
		'level0' => UPDATE_SCORE . '/user.json',
		'level1' => UPDATE_SCORE . '/level.json'
	),
  UNLOCKED_FLASHCARD => array(
    'title'  => 'Unlocked Flashcard Action',
    'level0' => UNLOCKED_FLASHCARD . '/user.json',
    'level1' => UNLOCKED_FLASHCARD . '/term.json'
  ),
  COLLECTED_FLASHCARD => array(
    'title'  => 'Collected Flashcard Action',
    'level0' => COLLECTED_FLASHCARD . '/user.json',
    'level1' => COLLECTED_FLASHCARD . '/term.json'
  ),
  UPDATE_COIN => array(
    'title'  => 'Update Coin Action',
    'level0' => UPDATE_COIN . '/user.json',
    'level1' => UPDATE_COIN . '/level.json'
  ),
  SEND_MESSAGE => array(
    'title'  => 'Send Message Action',
    'level0' => SEND_MESSAGE . '/user.json',
    'level1' => SEND_MESSAGE . '/message.json'
  ),
  UPDATE_REVISE_STAGE => array(
    'title'  => 'Update Revise Stage Action',
    'level0' => UPDATE_REVISE_STAGE . '/user.json',
    'level1' => UPDATE_REVISE_STAGE . '/stage.json'
  )
);
$action     = (isset($_GET['action'])) ? $_GET['action'] : UPDATE_SCORE;
$folder     = (isset($_GET['folder'])) ? $_GET['folder'] : REPORT_PATH;
$files      = $reportFiles[$action];
$dataLevel1 = readResult($folder . '/' .$files['level1']);
$level1     = chartData($dataLevel1);
$level0     = chartData(readResult($folder . '/' . $files['level0']));
$overview   = getOverviewData($dataLevel1);

?>
<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
        	['Total Request', 'Request Per Second', 'Failed Request', 'Duration', 'Concurrency'],
        	<?php foreach ($level0 as $item) {?>
        	[<?=$item['request']?>, <?=$item['average']?>, <?=$item['failed']?>, <?=$item['duration']?>, <?=$item['concurrency']?> ],
        	<?php }?>
        ]);

        var options = {
          title: '<?=$files["title"]?>',
          hAxis: {title: 'Total Requests'}
        };

        var chart = new google.visualization.LineChart(document.getElementById('level0_chart'));

        chart.draw(data, options);

        var data1 = google.visualization.arrayToDataTable([
        	['Total Request', 'Request Per Second', 'Failed Request', 'Duration', 'Concurrency'],
        	<?php foreach ($level1 as $item) {?>
        	[<?=$item['request']?>, <?=$item['average']?>, <?=$item['failed']?>, <?=$item['duration']?>, <?=$item['concurrency']?> ],
        	<?php }?>
        ]);

        var options = {
          title: '<?=$files["title"]?> Sub Level',
          hAxis: {title: 'Total Requests'}
        };

        var chart1 = new google.visualization.LineChart(document.getElementById('level1_chart'));

        chart1.draw(data1, options);
      }
    </script>
  </head>
  <body>
  	<style type="text/css">
  	.menu{
  		float: left;
  		width: 200px;
  		padding-top: 50px
  	}
  	.chart_area{
  		float: left;
  	}
    .overview{
      margin-left: 200px; 
    }
    .overview h1{
      text-align: center;
    }
    .overview p{

    }
    .overview b{
      display: inline-block;
      width: 250px;
    }
  	</style>    
  	<div class="menu">
  		<h2>Actions</h2>
  		<ul>
  			<?php foreach ($reportFiles as $action => $file) { ?>  			
  			<li><a href="?action=<?=$action?>&folder=<?=$folder?>"><?=$action?></a></li>
  			<?php }?>
  		</ul>
  	</div>
  	<div class="chart_area">
      <div class="overview">
        <h1><?=$files["title"]?></h1>
        <p><b>Total request:</b> <?=$overview['total_request']?> requests</p>
        <p><b>Total failed request:</b> <?=$overview['failed_request']?> requests</p>
        <p><b>Total time request:</b> <?=$overview['duration']?> seconds (<?=secondToTime($overview['duration'])?>)</p>
        <p><b>Average concurrency</b> <?=$overview['concurrency']?> requests</p>        
        <p><b>Average request per second:</b> <?=$overview['request_per_second']?> requests/second</p>
        <p><b>Average time per request:</b> <?=$overview['time_per_request']?> seconds/request</p>
      </div>
    	<div id="level0_chart" style="width: 1300px; height: 500px;"></div>
    	<div id="level1_chart" style="width: 1300px; height: 500px;"></div>
    </div>
  </body>
</html>

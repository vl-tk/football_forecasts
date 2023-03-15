<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once('./header.php');
require_once('./visual_header.php');

// $values = array('A', 'B', 'C');
// $weights = array(3, 7, 10);

// for ($i=0; $i < 20; $i++)
// {
// 	echo Random::weighted_random_simple($values, $weights);
// }

		// -5 - 0.005
		// -4 - 0.01
		// -3 - 0.03
		// -2 - 0.08
		// -1 - 0.12
		// 0 - 0.27
		// 1 - 0.2
		// 2 - 0.15
		// 3 - 0.085
		// 4 - 0.03
		// 5 - 0.01
		// 6 - 0.005

// 100 ... 50

// 50
// 60
// 70
// 80
// 90
// 100

$k = 100/50 - 1;

$values = array(-5, -4, -3,  -2,  -1,   0,   1,   2,  3,  4,  5, 6);
// $init_values = array(-5, -4, -3,  -2,  -1,   0,   1,   2,  3,  4,  5, 6);
// $weights = array(1,  3, 5, 45, 200, 540, 500, 300, 45, 10, 5, 1);
// $weights = array(5, 10, 30, 80, 120, 270, 200, 150, 85, 30, 10, 5);
$init_weights = array(5, 10, 30, 80, 120, 270, 200, 150, 85, 30, 10, 5);

foreach ($values as $value)
	$new_values[] = round(intval($value + $k), 0);

foreach ($init_weights as $key => $w)
{
	$new_pos = (array_key_exists($key - $k, $init_weights) ? $init_weights[$key - $k] : 0);

	$new_weights[] = $new_pos;

	// if ($values[$key] >0)
	// 	$new_weights[] = round($w*$k, 0);
	// else
	// 	$new_weights[] = round($w/$k, 0);
}

print_r($new_weights);

// $weights = array(1, 10, 30, 360, 400, 640, 500, 200, 35, 10, 3, 1);

// echo '<pre>';
// print_r($new_values);
// echo '<pre>';
// print_r($new_weights);

// for ($i=0; $i < 200; $i++)
// 	echo Random::weighted_random_simple($values, $weights)." ";



include("/lib/pchart/pChart/pData.class");
include("/lib/pchart/pChart/pChart.class");

// Dataset definition
$DataSet = new pData;
$DataSet->AddPoint($init_weights, "User");
$DataSet->AddPoint($new_weights, "User2");
$DataSet->AddAllSeries();
$DataSet->AddPoint($values,"Serie3");
$DataSet->SetAbsciseLabelSerie("Serie3");
// $DataSet->SetAbsciseLabelSerie("Serie3");
// $DataSet->SetSerieName("January","Serie1");
// $DataSet->SetSerieName("February","Serie2");
$DataSet->SetXAxisName("Дата");
$DataSet->SetYAxisName("Очки");
// $DataSet->SetYAxisUnit("Очки");
// $DataSet->SetXAxisUnit("Дата");

// Initialise the graph
$Test = new pChart(700,400);
// $Test->drawGraphAreaGradient(255,255,255,50);

// Graph area setup
$Test->setFontProperties("./lib/pchart/Fonts/tahoma.ttf",8);
$Test->setGraphArea(60,20,600,300);
$Test->drawGraphArea(255,255,255,FALSE);

// $AxisBoundaries = array(0=>array("Min"=>0,"Max"=>100));
//  $ScaleSettings  = array
//  ("Mode"=>SCALE_MODE_MANUAL,"ManualScale"=>$AxisBoundaries,"DrawSubTicks"=>TRUE,"DrawArrows"=>TRUE,"ArrowSize"=>6);
//  $Test->drawScale($ScaleSettings);

$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
// $Test->drawGraphAreaGradient(162,183,202,50);
$Test->drawGrid(4,TRUE,150,150,150,20);

// // Draw the line chart
// $Test->setShadowProperties(3,3,0,0,0,30,4);
$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
// $Test->clearShadow();
$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),4,2,-1,-1,-1,TRUE);

// // Draw the legend
// $Test->setFontProperties("./lib/pchart/Fonts/tahoma.ttf",8);
// $Test->drawLegend(605,142,$DataSet->GetDataDescription(),236,238,240,52,58,82);

// // // Draw the title
// $Title = "Average Temperatures during the first months of 2008  ";
// $Test->drawTextBox(0,210,700,230,$Title,0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30);

// Render the picture
$Test->addBorder(0);
$Test->Render("./img/chart/test.png");
?>
<img src="/img/chart/test.png" />
<?
require_once('./footer.php');
?>

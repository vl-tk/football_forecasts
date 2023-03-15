<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('./templates/header.php');

$tournament_id = DEFAULT_TOURNAMENT;
if (array_key_exists('cup', $_GET)) {
	$tournament_id = Tournament::getIDByPrefix($_GET['cup']);
}

$user_group = null;
if (array_key_exists('group', $_GET)) {
	$user_group = $_GET['group'];
}

define("VOID", 'VOID');

$user_id = $user->getId();

Log::write('Просмотр графика - Пользователь '.$user->getLogin());

$games = Game::getInfoFilteredByUser($user_id, $tournament_id);
$prognoses = Prognosis::getFilteredForGroup($tournament_id, $user_group);

$prognozzists = array();
foreach ($prognoses as $game_id => $game_prognoses):
	foreach ($game_prognoses as $gp):
		if (!in_array($gp['user_id'], $prognozzists))
			$prognozzists[] = $gp['user_id'];
	endforeach;
endforeach;

$users = array();
$graph = array();
$borders = array();
$prev_game_day = -1;
$gms = array();
$final_sums = array();

$i = 1;
foreach ($games as $index => $game)
{
	if (isset($_GET['playoff']) && $_GET['playoff'] == 1 && strlen($game['playoff_round']) == 0)
		continue;

	if (strlen($game['result']) > 0 && $game['finished'] == 'Y')
	{
		$gms[] = $i;
		$game_date = new DateTime($game['started_at']);
		$game_day = $game_date->format('d');
		if ($prev_game_day == -1)
			$prev_game_day = $game_day;

		if ($game_day > $prev_game_day)
		{
			$borders[] = $i-2;
			$prev_game_day = $game_day;
		}
		$i++;

		foreach ($prognozzists as $current_user_id)
		{
			if (array_key_exists($game['id'], $prognoses) && !empty($prognoses[$game['id']]))
			{
				$made_prognosis_for_game = false;
				foreach ($prognoses[$game['id']] as $gp)
				{
					if ($gp['user_id'] == $current_user_id)
					{
						$result = explode(':', $game['result']);
						$r1 = (int) $result[0];
						$r2 = (int) $result[1];

						$penalty_result = explode(':', $game['penalty_result']);
						$pr1 = (int) $penalty_result[0];
						$pr2 = (int) $penalty_result[1];

						$ratings[$current_user_id]['games'] += 1;
						$ratings[$current_user_id]['total_diff'] += abs($gp['team1'] - $r1) + abs($gp['team2'] - $r2);

						// new scheme
						$new_points = Calc::getPoints($r1, $r2, $pr1, $pr2, $gp['team1'], $gp['team2']);
						$ratings[$current_user_id]['new_scheme'] += $new_points;
						$ratings[$current_user_id]['total'] += $new_points;

						$graph[$current_user_id][] = (int) $ratings[$current_user_id]['total'];
						$final_sums[$current_user_id] = (int) $ratings[$current_user_id]['total'];

						if (!in_array($current_user_id, $users))
							$users[$current_user_id] = $gp['name'];

						$made_prognosis_for_game = true;
					}
				}

				if (!$made_prognosis_for_game)
					$graph[$current_user_id][] = VOID;
			}
			else // nobody made a prognosis for this game
			{
				$graph[$current_user_id][] = VOID;
			}
		}
	}
}
?>

<div id="chart">
	<?
	if (empty($graph))
		return;

	function cmpfunction($a, $b)
	{
		if ($a === $b)
			return 0;

		return ($a > $b) ? -1 : 1;
	}
	uasort($final_sums, 'cmpfunction');

	$sorted_graph = array();
	foreach($final_sums as $user_id => $final_sum)
		$sorted_graph[$user_id] = $graph[$user_id];

	/* Include all the classes */
	include("./lib/pchart2/class/pDraw.class.php");
	include("./lib/pchart2/class/pImage.class.php");
	include("./lib/pchart2/class/pData.class.php");

	// Dataset definition
	$DataSet = new pData();
	foreach ($sorted_graph as $user_id => $graph_points):
		$DataSet->addPoints($graph_points, $users[$user_id]);
		// $DataSet->setSerieDescription($users[$user_id],"Months");
	endforeach;

	$DataSet->addPoints($gms,"Labels");
	$DataSet->setSerieDescription("Labels","Months");
	$DataSet->setAbscissa("Labels");

	// $DataSet->addPoints(array(2,7,5,18,VOID,12,10,15,8,5,6,9),"Help Desk");

	// Initialise the graph
	$image = new pImage(1600,600,$DataSet);

	// Graph area setup
	$image->setGraphArea(50,50,1500,550);


	$image->setFontProperties(array("FontName"=>"./lib/pchart2/fonts/tahoma.ttf","FontSize"=>11));

	// /* Draw the scale, keep everything automatic */
	$image->drawScale(array('Mode' => SCALE_MODE_START0));
	// $image->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

	// /* Draw the scale, keep everything automatic */
	$image->drawSplineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"DisplayOffset"=>3));
	// $image->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"DisplayOffset"=>3));

	// $Test->drawFilledRoundedRectangle(0,0,600,300,5,255,255,255);
	// $Test->drawRoundedRectangle(5,5,695,225,5,230,230,230);
	// $Test->drawGraphArea(255,255,255,TRUE);

	$image->drawXThreshold($borders,array("Alpha"=>100,"Ticks"=>1,"R"=>240,"G"=>240,"B"=>240));
	// $image->drawXThreshold(2,array("Alpha"=>70,"Ticks"=>2,"R"=>230,"G"=>230,"B"=>230));

	// // Draw the legend
	// $Test->drawLegend(605,142,$DataSet->GetDataDescription(),236,238,240,52,58,82);
	$image->drawLegend(1500,100, array("Style"=>LEGEND_NOBORDER));

	// // // Draw the title
	// $Title = "Average Temperatures during the first months of 2008  ";
	// $Test->drawTextBox(0,210,700,230,$Title,0,255,255,255,ALIGN_RIGHT,TRUE,0,0,0,30);

	$image->render("./img/chart/chart.png");
	?>
	<style type="text/css">
		body {
			background-color: #2B2B2B;
		}
		img {
			border: none;
		}
	</style>
	<img src="/img/chart/chart.png" />
</div>
<?
require_once('./templates/footer.php');

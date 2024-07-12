<?php

class Html
{
	public static function showGamesTable($table_id, $games = array(), $prognoses = array(), $playoff = false)
	{
		global $user;
		$user_id = $user->GetId();

		?>
		<table class="table table-striped" id="<?=$table_id?>">

			<thead>
				<tr>
					<th></th>
					<th style="width: 60px">Date</th>
					<th colspan="2">
					<th>Game</th>
					<th colspan="2">
					<?
					if ($user->isAdmin()):
					?>
						<th><a href="javascript:void(0)" class="more-controls" data-id="<?=$table_id?>">Control</a></th>
					<?
					else:
					?>
						<th></th>
					<?
					endif;
					?>
				</tr>
			</thead>

			<tbody>
				<?
				if (is_array($games)):
					$prevPlayoffRound = '';
					foreach ($games as $index => $game):

						if (!$playoff)
						{
							if (strlen($game['playoff_round']) > 0)
								continue;
						}
						else
						{
							if (strlen($game['playoff_round']) == 0)
								continue;
						}

						// add style
						if (array_key_exists($index+1, $games)):
							$next = $games[$index+1];
							$bottom_border = (Y::isLater($next['started_at'], $game['started_at'])) ? 'bottom' : '';
						endif;

						$finished = ($game['finished'] == 'Y' && $user->isAuthorized()) ? 'finished' : '';

						$title = '';
						if ($game['finished'] == 'Y' && strlen($game['result']) > 0)
							$title = 'Game is over';
						else if ($game['finished'] == 'Y')
							$title = 'Forecasts are not accepted anymore';

						$hasPrognoses = (!empty($prognoses) && array_key_exists($game['id'], $prognoses) && !empty($prognoses[$game['id']])) ? true : false;

						if ($hasPrognoses)
						{
							$onlyMine = true;
							foreach ($prognoses[$game['id']] as $key => $gp)
							{
								if ($gp['user_id'] != $user_id)
								{
									$onlyMine = false;
									break;
								}
							}
						}

						$unfolding = ($hasPrognoses && !$onlyMine) ? 'unfolding' : '';
						$tr_clickable = ($hasPrognoses && !$onlyMine) ? 'tr_clickable' : '';
					?>

						<?
						$newPlayoffRound = false;
						if (strlen($game['playoff_round']) > 0 && $game['playoff_round'] != $prevPlayoffRound)
						{
							$newPlayoffRound = true;
							$prevPlayoffRound = $game['playoff_round'];
						}

						if ($newPlayoffRound):
						?>
							<tr class="playoff_round">
								<td></td>
								<td></td>
								<td colspan=5 class="round"><?=Y::getPlayoffRoundName($game['playoff_round'])?></td>
								<td></td>
							</tr>
						<?
						endif;
						?>

						<tr title="<?=$title?>" id="tr_<?=$index?>" class="game_info <?=$bottom_border?> <?=$finished?> <?=$tr_clickable?>">
							<td><?=$index + 1?></td>

							<td title="<?=Y::hour_minute($game["started_at"])?>"><?=Y::day_month($game["started_at"])?></td>

							<td class="team">
								<div class="team1">
									<img src="/img/teams/<?=$game['team1_prefix']?>.png" />&nbsp;&nbsp;
									<span class="with_hint" title="<?=$game['team1_fifa_rating']?> place in FIFA rating">
										<?=$game["team1_name"]?>
									</span>
								</div>
							</td>

							<td class="x">
								<?=Html::numControl(
									$game['id'],
									$game['team1_id'],
									$game['team1_prognosis'],
									1,
									$game['finished'],
									$game['result'],
									$game['penalty_result']
									)?>
							</td>
							<td style="width:10px">-</td>
							<td class="x">
								<?=Html::numControl(
									$game['id'],
									$game['team2_id'],
									$game['team2_prognosis'],
									2,
									$game['finished'],
									$game['result'],
									$game['penalty_result']
								)?>
							</td>

							<td class="team">
								<div class="team2">
									<img src="/img/teams/<?=$game['team2_prefix']?>.png" />&nbsp;&nbsp;
									<span class="with_hint" title="<?=$game['team2_fifa_rating']?> place in FIFA rating">
										<?=$game["team2_name"]?>
									</span>
								</div>
							</td>

							<td>
								<?
								if ($hasPrognoses && !$onlyMine):
									$prognoses_title = ($game['finished'] == 'Y') ? 'Results' : 'Forecasts';
								?>
									<a href="javascript:void(0);" id="<?=$index?>_show_info" class="show_info dotted"><?=$prognoses_title?></a>&nbsp;
								<?
								endif;

								if ($user->isAdmin()):
								?>
									<div class="admin-controls">
										<?
										if ($game['finished'] != 'Y'):
										?>
										<a href="javascript:void(0);" data-game-id="<?=$game['id']?>" class="btn-sm btn-danger finish_button" title="Disallow forecasts">Stop</a>
										<a href="javascript:void(0);" data-game-id="<?=$game['id']?>" class="btn-sm btn-info random_button" title="Stupid bot">Т</a>
										<a href="javascript:void(0);" data-game-id="<?=$game['id']?>" data-team1-rank="<?=$game['team1_rank']?>" data-team2-rank="<?=$game['team2_rank']?>" data-clever="Y" class="btn-sm btn-info random_button" title="Smart bot">У</a>
										<a href="javascript:void(0);" data-game-id="<?=$game['id']?>" data-team1-fifa-rank="<?=$game['team1_fifa_rank']?>" data-team2-fifa-rank="<?=$game['team2_fifa_rank']?>" data-clever="Y" class="btn-sm btn-info random_button" title="Fifa-bot">F</a>
										<?
										else:
										?>
										<a href="javascript:void(0);" data-game-id="<?=$game['id']?>" class="btn-sm btn-success continue_button" title="Allow forecasts">Allow forecasts</a>
										<a href="javascript:void(0);" data-result="<?=$game['result']?>" data-game-id="<?=$game['id']?>" class="btn-sm result_button">Score</a>
										<?
										endif;
										?>
									</div>
								<?
								endif;
								?>

								<?
								if ($user->isManager()):
								?>
									<div class="admin-controls">
									<?
									if ($game['finished'] == 'Y'):
									?>
										<a href="javascript:void(0);" data-result="<?=$game['result']?>" data-game-id="<?=$game['id']?>" class="btn-sm result_button">Score</a>
									<?
									endif;
									?>
									</div>
								<?
								endif;
								?>
							</td>
						</tr>

						<tr class="hiddenx <?=$unfolding?> <?=$bottom_border?>" id="<?=$index?>_info">
							<td colspan="2"></td>
							<td colspan="5">
								<div style="width: 85%;">
									<div style="margin-right: 37%; text-align: right;">
										<?php
										if (is_array($prognoses)):

											foreach ($prognoses as $game_id => $game_prognoses):

												if ($game['id'] != $game_id)
													continue;

												$tmp_result = array();
												$tmp_results = array();
												foreach ($game_prognoses as $gp):

													if ($gp['user_id'] == $user_id && $game['finished'] != 'Y')
														continue;

													$gp1 = Y::r($gp['team1']);
													$gp2 = Y::r($gp['team2']);

													$pointsHTML = Calc::getPointsHTML($gp['team1'], $gp['team2'], $game['finished'], $game['result'], $game['penalty_result']);

													if ($game['finished'] == 'Y'):
														$tmp_result['html'] = "<strong>".Y::getUserName($gp['user_id'], $gp['name'])."</strong>&nbsp;".$gp1."&nbsp;:&nbsp;".$gp2.$pointsHTML."<br>";
													else:
														$tmp_result['html'] = "<strong>".Y::getUserName($gp['user_id'], $gp['name'])."</strong>&nbsp;made forecast<br>";
													endif;

													$tmp_result['name'] = $gp['name'];

													$tmp_result['total'] = 0;
													if (strlen($game['result']) > 0)
													{
														$results = explode(':',  $game['result']);
														$r1 = (int) $results[0];
														$r2 = (int) $results[1];

														$penalty_results = explode(':',  $game['penalty_result']);
														$pr1 = (int) $penalty_results[0];
														$pr2 = (int) $penalty_results[1];

														$tmp_result['total'] = Calc::getPoints($r1, $r2, $pr1, $pr2, $gp1, $gp2);

														$tmp_points = Calc::getPointsByDiff($gp1, $gp2, $game['result']);
														if ($tmp_points == Calc::TOTAL)
															$tmp_result['exact'] += 1;

														if (Y::same_result($r1, $r2, $gp1, $gp2, $pr1, $pr2))
															$tmp_result['exact_result'] += 1;
													}

													$tmp_results[] = $tmp_result;
												endforeach;
											endforeach;

											uasort($tmp_results, array('Y', 'cmp'));  // resort by total points

											foreach ($tmp_results as $tmp_result):
												echo $tmp_result['html'];
											endforeach;

										endif;
										?>
									</div>
								</div>
							</td>
							<td colspan="1"></td>
						</tr>
					<?
					endforeach;
				endif;
				?>
			</tbody>
		</table>
		<?
	}

	public static function showRatingTable($table_id, $games, $prognoses, $playoffOnly = false)
	{
		global $user;
		$user_id = $user->GetId();

		$ratings = array();
		$users = array();
		$total_new_points = 0;
		foreach ($games as $index => $game):

			if (array_key_exists($game['id'], $prognoses) && !empty($prognoses[$game['id']])):
				foreach ($prognoses[$game['id']] as $gp):

					$show = true;
					if ($playoffOnly)
						$show = (array_key_exists('playoff_round', $game) && strlen($game['playoff_round']) > 0) ? true : false;

					if (strlen($game['result']) > 0 && $game['finished'] == 'Y' && $show)
					{
						$result = explode(':', $game['result']);
						$r1 = (int) $result[0];
						$r2 = (int) $result[1];

						$penalty_result = explode(':', $game['penalty_result']);
						$pr1 = (int) $penalty_result[0];
						$pr2 = (int) $penalty_result[1];

						$ratings[$gp['user_id']]['games'] += 1;
						$ratings[$gp['user_id']]['total_diff'] += abs($gp['team1'] - $r1) + abs($gp['team2'] - $r2);


						// new scheme
						$new_points = Calc::getPoints($r1, $r2, $pr1, $pr2, $gp['team1'], $gp['team2']);
						$ratings[$gp['user_id']]['new_scheme'] += $new_points;

						$ratings[$gp['user_id']][$game['id']]['teams'] = $game['team1_name'].' - '.$game['team2_name'];
						$ratings[$gp['user_id']][$game['id']]['teams_prognosis'] = 'Прогноз '.Y::r($gp['team1']).':'.Y::r($gp['team2']);
						$ratings[$gp['user_id']][$game['id']]['teams_result'] = 'Счет '.$r1.':'.$r2;

						$ratings[$gp['user_id']]['total'] += $new_points;

						// diff points
						$points = Calc::getPointsByDiff($gp['team1'], $gp['team2'], $game['result']);

						$ratings[$gp['user_id']]['diff_points'] += $points;

						// additional stats info
						if ($points == Calc::TOTAL)
							$ratings[$gp['user_id']]['exact'] += 1;
						if (abs($points - Calc::TOTAL) == 1)
							$ratings[$gp['user_id']]['exact1'] += 1;

						if (Y::same_result($r1, $r2, $gp['team1'], $gp['team2'], $pr1, $pr2))
							$ratings[$gp['user_id']]['exact_result'] += 1;

						// old scheme (3 for exact, 2 for diff, 1 for win/draw)
						$old_points = 0;
						if ($points == Calc::TOTAL)
							$old_points += 3;
						else if (($r1 - $r2) == ($gp['team1'] - $gp['team2']))
							$old_points += 2;
						else if (Y::same_result($r1, $r2, $gp['team1'], $gp['team2']))
							$old_points += 1;
						$ratings[$gp['user_id']]['old_scheme'] += $old_points;

						// kulichki points
						$kulichki_points = 0;
						if (Y::same_result($r1, $r2, $gp['team1'], $gp['team2']))
							$kulichki_points += 3;
						if (($r1 - $r2) == ($gp['team1'] - $gp['team2']))
						{
							$kulichki_points += 4;

							if ($r1 - $r2 >= 3)
								$kulichki_points +=1;
						}
						else if (abs(($r1 - $r2) - ($gp['team1'] - $gp['team2'])) == 1)
							$kulichki_points += 2;
						if ($points == Calc::TOTAL)
							$kulichki_points += 3;

						$ratings[$gp['user_id']]['kulichki_points'] += $kulichki_points;


						// pdd points
						$pdd_points = 0;
						if (Y::same_result($r1, $r2, $gp['team1'], $gp['team2']))
							$pdd_points += 5;
						if ($r1 == $gp['team1'])
							$pdd_points += 2;
						if ($r2 == $gp['team2'])
							$pdd_points += 2;

						$ratings[$gp['user_id']]['pdd_points'] += $pdd_points;

						// hints
						$ratings[$gp['user_id']][$game['id']]['points_text'] = $new_points.' '.Y::pl($new_points, 'points', 'points', 'points');
						$ratings[$gp['user_id']][$game['id']]['diff_points_text'] = $points.' '.Y::pl($points, 'points', 'points', 'points');
						$ratings[$gp['user_id']][$game['id']]['old_points_text'] = $old_points.' '.Y::pl($old_points, 'points', 'points', 'points');
						$ratings[$gp['user_id']][$game['id']]['kulichki_points_text'] = $kulichki_points.' '.Y::pl($kulichki_points, 'points', 'points', 'points');
						$ratings[$gp['user_id']][$game['id']]['pdd_points_text'] = $pdd_points.' '.Y::pl($pdd_points, 'points', 'points', 'points');
					}

					if (!in_array($gp['user_id'], $users)){
						$users[$gp['user_id']] = $gp['name'];
					}
				endforeach;
			endif;

		endforeach;

		uasort($ratings, array('Y', 'cmp'));  // resort by total points
		// echo '<pre>';
		// print_r($users);
		// echo '<pre>';
		// print_r($ratings);
		unset($data);

		$hint_4321 = "4 очка за угаданный счет<br>3 очка за угаданный исход и разницу мячей<br>2 очка за угаданный исход и отличие в разнице мячей < 1.5 мяча<br>1 очко за угаданный исход и отличие в разнице мячей > 1.5 мяча<br><br>Hover over the number to see the components for each game points";
		$hint_percent = "Процент от возможного количества очков за прошедшие матчи<br><br>Наведите курсор на число, чтобы увидеть расшифровку";
		$hint_321 = "3 очка за угаданный счет<br>2 за разницу<br>1 очко за исход (победа, ничья)<br><br>Наведите курсор на число, чтобы увидеть расшифровку за каждый матч";
		$hint_diff = "По формуле ".Calc::TOTAL." - (| Голы 1-й команды - Прогноз на голы 1-й команды | + | Голы 2-й команды - Прогноз на голы 2-й команды |)<br><br>Наведите курсор на число, чтобы увидеть расшифровку за каждый матч";
		$hint_kulichki = "Угадан исход: +3 очка<br>Угадана разница забитых и пропущенных мячей: +4 очка<br>Ошибка на 1: +2 очка<br>Полностью угадан счет: +3 очка<br>Приз за угаданную разность 3 и более мячей: +1 очко";
		$hint_522 = "5 очков за угаданный исход (победа, ничья, поражение)<br>+2 очка за угаданное число голов любой из команд";
		?>

		<table id="<?=$table_id?>" class="table table-striped" style="max-width: 1100px">
			<thead>
				<th><strong>Place</strong></th>
				<th><strong>User</strong></th>
				<th class="hinted" title="<?=$hint_4321?>"><strong>Points</strong>&nbsp;<sup class="hint" title="<?=$hint_4321?>">*</sup></th>
				<th><strong>Average deviation (goals)</strong></th>
				<th class="hinted" title="<?=$hint_percent?>"><strong>%</strong><sup class="hint" title="<?=$hint_percent?>">*</sup></th>
				<th><strong>Exact forecasts</strong></th>
				<th><strong>1-goal errors</strong></th>

				<th><strong>Correct outcomes</strong></th>
				<th class="hinted" title="<?=$hint_diff?>"><strong>Points for the goal difference scheme</strong>&nbsp;<sup class="hint" title="<?=$hint_diff?>">*</sup></th>
				<th class="hinted" title="<?=$hint_321?>"><strong>Points for scheme 3-2-1</strong>&nbsp;<sup class="hint" title="<?=$hint_321?>">*</sup></th>
				<th class="hinted" title="<?=$hint_kulichki?>"><strong>Points for 'kulichki.net' scheme</strong>&nbsp;<sup class="hint" title="<?=$hint_kulichki?>">*</sup></th>
				<th class="hinted" title="<?=$hint_522?>"><strong>Points for scheme 5-2-2</strong>&nbsp;<sup class="hint" title="<?=$hint_522?>">*</sup></th>
				<th><strong>Total forecasts</strong></th>
			</thead>

			<tbody>
			<?
			$i = 0;
			foreach ($ratings as $uid => $data):
				$i++;

				$main_hint = "";
				$diff_hint = "";
				$old_hint = "";
				$kulichki_hint = "";
				$pdd_hint = "";
				foreach ($data as $key => $value):
					if (is_numeric($key)):
						$main_hint     .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br/>';
						$diff_hint     .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['diff_points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br>';
						$old_hint      .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['old_points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br>';
						$kulichki_hint .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['kulichki_points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br>';
						$pdd_hint      .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['pdd_points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br>';
					endif;
				endforeach;

				$highlighted = ($user_id == $uid) ? 'highlighted' : '';
				?>
				<tr class="<?=$highlighted?>">
					<td>
						<?=$i?>
					</td>
					<td>
						<?=Y::getUserName($uid, $users[$uid])?><?//=Y::getSmile($user_id, $i)?>
					</td>
					<td class="centered hinted" title="<?=$main_hint?>">
						<strong><?=$data['new_scheme']?></strong>
					</td>
					<td class="centered">
						<?=str_replace(",", ".", round($data['total_diff'] / $data['games'], 2))?>
					</td>
					<td class="centered hinted" title="<?=$data['total']?> / <?=$data['games'] * 4?>">
						<?=str_replace(",", ".", round(($data['total'] / ($data['games'] * 4)) * 100, 2))?>
					</td>
					<td class="centered hinted" title="<?=round($data['exact'] / $data['games'] * 100, 2)?>">
						<?=$data['exact']?>
					</td>
					<td class="centered hinted" title="<?=round($data['exact1'] / $data['games'] * 100, 2)?>">
						<?=$data['exact1']?>
					</td>

					<td class="centered hinted" title="<?=round($data['exact_result'] / $data['games'] * 100, 2)?>">
						<?=$data['exact_result']?>
					</td>
					<td class="centered hinted" title="<?=$diff_hint?>">
						<?=$data['diff_points']?>
					</td>
					<td class="centered hinted" title="<?=$old_hint?>">
						<?=$data['old_scheme']?>
					</td>
					<td class="centered hinted" title="<?=$kulichki_hint?>">
						<?=$data['kulichki_points']?>
					</td>
					<td class="centered hinted" title="<?=$pdd_hint?>">
						<?=$data['pdd_points']?>
					</td>
					<td class="centered">
						<?=$data['games']?>
					</td>
				</tr>
				<?
			endforeach;
			?>
			</tbody>
		</table>
		<?
		if (!$playoffOnly):

			$href = "/graph.php";
			if (array_key_exists("cup", $_GET))
				$href = $href."?cup=".$_GET["cup"];

			if (array_key_exists("group", $_GET)) {
				if (strpos($href, '?') === false) {
					$href = $href."?group=".$_GET["group"];
				}
				else {
					$href = $href."&group=".$_GET["group"];
				}
			}
		?>
			<a href="<?=$href?>">Динамика набора очков</a>
		<?
		else:

			$href = "/graph.php?playoff=1";
			if (array_key_exists("cup", $_GET))
				$href = $href."&cup=".$_GET["cup"];

			if (array_key_exists("group", $_GET))
				$href = $href."&group=".$_GET["group"];
		?>
			<a href="<?=$href?>">Динамика набора очков в плей-офф</a>
		<?
		endif;
	}

	public static function showNumbers($table_id, $games, $prognoses, $playoffOnly = false)
	{
		global $user;
		$user_id = $user->GetId();

		$ratings = array();
		$users = array();
		$game_ids = array();
		foreach ($games as $index => $game):

			if (array_key_exists($game['id'], $prognoses) && !empty($prognoses[$game['id']])):
				$show = true;
				if ($playoffOnly)
					$show = (array_key_exists('playoff_round', $game) && strlen($game['playoff_round']) > 0) ? true : false;

				if (strlen($game['result']) > 0 && $game['finished'] == 'Y' && $show):
					$game_ids[] = $game['id'];
				endif;
			endif;

			if (array_key_exists($game['id'], $prognoses) && !empty($prognoses[$game['id']])):
				foreach ($prognoses[$game['id']] as $gp):

					$show = true;
					if ($playoffOnly)
						$show = (array_key_exists('playoff_round', $game) && strlen($game['playoff_round']) > 0) ? true : false;

					if (strlen($game['result']) > 0 && $game['finished'] == 'Y' && $show)
					{
						$result = explode(':', $game['result']);
						$r1 = (int) $result[0];
						$r2 = (int) $result[1];

						$penalty_result = explode(':', $game['penalty_result']);
						$pr1 = (int) $penalty_result[0];
						$pr2 = (int) $penalty_result[1];

						$ratings[$gp['user_id']]['games'] += 1;

						// new scheme
						$new_points = Calc::getPoints($r1, $r2, $pr1, $pr2, $gp['team1'], $gp['team2']);

						// $ratings[$gp['user_id']][$game['id']]['teams'] = $game['team1_name'].' - '.$game['team2_name'];
						// $ratings[$gp['user_id']][$game['id']]['teams_prognosis'] = 'Прогноз '.Y::r($gp['team1']).':'.Y::r($gp['team2']);
						// $ratings[$gp['user_id']][$game['id']]['teams_result'] = 'Счет '.$r1.':'.$r2;

						$ratings[$gp['user_id']]['total'] += $new_points;
						$ratings[$gp['user_id']]['numbers'][$game['id']] = $new_points;
					}

					if (!in_array($gp['user_id'], $users)){
						$users[$gp['user_id']] = $gp['name'];
					}
				endforeach;
			endif;

		endforeach;

		uasort($ratings, array('Y', 'cmp'));  // resort by total points
		$start_game_id = $game_ids[0];
		// echo '<pre>';
		// print_r($users);
		// echo '<pre>';
		// print_r($game_ids);
		// echo '<pre>';
		// print_r($ratings);
		?>
		<table id="<?=$table_id?>" class="table table-striped" style="max-width: 1100px">
			<thead>
				<th><strong>Place</strong></th>
				<th><strong>User</strong></th>
				<?
				foreach ($game_ids as $game_id):
				?>
					<th><?=$game_id - $start_game_id + 1?></th>
				<?
				endforeach;
				?>
			</thead>

			<tbody>
			<?
			$i = 0;
			foreach ($ratings as $uid => $data):
				$i++;

				// $main_hint = "";
				// foreach ($data as $key => $value):
				// 	if (is_numeric($key)):
				// 		$main_hint     .= $value['teams_result'].'&nbsp;-&nbsp;'.$value['teams_prognosis'].'&nbsp;-&nbsp;'.$value['points_text'].'&nbsp;-&nbsp;'.$value['teams'].'<br/>';
				// 	endif;
				// endforeach;
				?>
				<tr class="">
					<td>
						<?=$i?>
					</td>
					<td>
						<?=Y::getUserName($uid, $users[$uid])?>
					</td>
					<?
					foreach ($game_ids as $game_id):
						if (array_key_exists($game_id, $data['numbers'])):
							$value = $data['numbers'][$game_id];
						?>
							<td class="centered" title="">
								<? if ($value == 4): ?>
									<span style="color: #000"?><?=$value?></span>
								<? elseif ($value == 3): ?>
									<span style="color: #555"?><?=$value?></span>
								<? elseif ($value == 2): ?>
									<span style="color: #999"?><?=$value?></span>
								<? elseif ($value == 1): ?>
									<span style="color: #ccc"?><?=$value?></span>
								<? else: ?>
									<span style="color: #eee"?><?=$value?></span>
								<? endif ?>
							</td>
						<?
						else:
						?>
							<td class="centered">
								<span style="color: #eee"?>-</span>
							</td>
						<?
						endif;
					endforeach;
					?>
				</tr>
				<?
			endforeach;
			?>
			</tbody>
		</table>
		<?
		if (!$playoffOnly):

			$href = "/graph.php";
			if (array_key_exists("cup", $_GET))
				$href = $href."?cup=".$_GET["cup"];

			if (array_key_exists("group", $_GET)) {
				if (strpos($href, '?') === false) {
					$href = $href."?group=".$_GET["group"];
				}
				else {
					$href = $href."&group=".$_GET["group"];
				}
			}
		?>
			<a href="<?=$href?>">Динамика набора очков</a>
		<?
		else:

			$href = "/graph.php?playoff=1";
			if (array_key_exists("cup", $_GET))
				$href = $href."&cup=".$_GET["cup"];

			if (array_key_exists("group", $_GET))
				$href = $href."&group=".$_GET["group"];
		?>
			<a href="<?=$href?>">Динамика набора очков в плей-офф</a>
		<?
		endif;
	}

	public static function numControl($id = null, $team_id = 0, $value = 0, $team_number = 0, $finished = '', $finished_result = '', $penalty_result = '')
	{
		global $user;

		if ($finished == 'Y')
		{
			if (strlen($finished_result) == 0)
			{
				if ($user->isAuthorized())
				{
					$r = '...';
					?>
					<div class="ongoing" title="Матч продолжается"><?=$r?></div>
					<?
				}
				else
				{
					?>
					<?
				}
			}
			else
			{
				$rr =  explode(':', $finished_result);
				$r = $rr[$team_number-1];

				$p = '';
				if (strlen($penalty_result) > 0)
				{
					$pp =  explode(':', $penalty_result);
					$p = $pp[$team_number-1];
				}
				?>
				<div class="result_simple"><?=$r?><? echo (strlen($p)>0) ? "<sup><span class='penalty'>($p)</span></sup>" : '' ?></div>
				<?
			}
		}
		else
		{
			if ($user->isAuthorized())
			{
				if (strlen($value) > 0)
				{
					$value = (int) $value;
					$big = 'big';
				}
				else
				{
					$value = '?';
					$big = 'big-question';
				}
			?>
				<div class="clickable <?=$big?>" id="click_<?=$id?>_<?=$team_number?>"><?=$value?></div>
				<select class="selectable control hiddenx selectpicker" title='Choose one of the following...' id="select_<?=$id?>_<?=$team_number?>" data-game-id="<?=$id?>">
					<?
					for ($i=0; $i < 8; $i++)
					{
						$selected = ($i == $value) ? 'selected' : '';
						?>
						<option value="<?php echo $i?>" <?=$selected?>><?php echo $i?></option>
						<?
					}
					?>
				</select>
			<?
			}
			else // not finished, not authorized
			{
			?>
			<?
			}
		}
	}

	public static function showPlayoffScheme($games)
	{
		$games = Game::parsePlayOffGameData($games);
		?>
			<table cellspacing="0" cellpadding="0" border="0" style="font-size: 90%; margin:1em 2em 1em 1em;">
			<tbody>
				<tr>
					<td height="5"></td>
					<td class="test4" colspan="2">1/8 финала</td>
					<td colspan="2"></td>
					<td class="test4" colspan="2">1/4 финала</td>
					<td colspan="2"></td>
					<td class="test4" colspan="2">Полуфиналы</td>
					<td colspan="2"></td>
					<td class="test4" colspan="2">Финал</td>
				</tr>
				<tr>
					<td height="5"></td>
					<td style="width:15em;">&nbsp;</td>
					<td width="30">&nbsp;</td>
					<td width="15">&nbsp;</td>
					<td width="20">&nbsp;</td>
					<td style="width:15em;">&nbsp;</td>
					<td width="30">&nbsp;</td>
					<td width="15">&nbsp;</td>
					<td width="20">&nbsp;</td>
					<td style="width:15em;">&nbsp;</td>
					<td width="30">&nbsp;</td>
					<td width="15">&nbsp;</td>
					<td width="20">&nbsp;</td>
					<td style="width:15em;">&nbsp;</td>
					<td width="30">&nbsp;</td>
				</tr>
				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?=Html::getDate($games, '1/8', 0)?></td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="4">&nbsp;</td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="7">&nbsp;</td>
					<td rowspan="3" colspan="2"></td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="7">&nbsp;</td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="13">&nbsp;</td>
					<td rowspan="9" colspan="2"></td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="13">&nbsp;</td>
					<td style="border-width:0 0 1px 0; border-style: solid;border-color:black;" rowspan="25">&nbsp;</td>
					<td rowspan="21" colspan="2"></td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>

				<tr>
				<td height="5"></td>
				<td class="test" rowspan="2"><?=Html::getTeam($games, '1/8', 0, 1)?></td>
				<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 0, 1) ?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?=Html::getDate($games, '1/4', 0)?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?=Html::getTeam($games, '1/8', 0, 2)?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 0, 2) ?></td>
					<td class="playoff-border" rowspan="6">&nbsp;</td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test" rowspan="2"><?=Html::getTeam($games, '1/4', 1, 1)?></td>
						<td align="center" class="test" rowspan="2"><?=Html::getScore($games, '1/4', 1, 1)?></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?=Html::getDate($games, '1/8', 1)?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test3" rowspan="12">&nbsp;</td>
						<td class="test" rowspan="2"><?=Html::getTeam($games, '1/4', 1, 2)?></td>
						<td align="center" class="test" rowspan="2"><?=Html::getScore($games, '1/4', 1, 2)?></td>
						<td class="playoff-border" rowspan="12">&nbsp;</td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 1, 1) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 1, 1) ?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="6" colspan="2"></td>
					<td rowspan="2" colspan="2">8 июля<?//=Html::getDate($games, '1/2', 0)?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 1, 2) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 1, 2) ?></td>
					<td class="test3" rowspan="6">&nbsp;</td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2">&nbsp;</td>
					<td align="center" class="test" rowspan="2"></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 2)?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test3" rowspan="24">&nbsp;</td>
					<td class="test" rowspan="2">&nbsp;</td>
					<td align="center" class="test" rowspan="2"></td>
					<td class="playoff-border" rowspan="24">&nbsp;</td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 2, 1) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 2, 1) ?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td rowspan="2" colspan="2"><?=Html::getDate($games, '1/4', 0)?></td>
						<td rowspan="18" colspan="2"></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 2, 2) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 2, 2) ?></td>
					<td class="playoff-border" rowspan="6">&nbsp;</td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 3, 1)?></td>
						<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/4', 3, 1)?></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 3)?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test3" rowspan="12">&nbsp;</td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 3, 2)?></td>
						<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/4', 3, 2)?></td>
						<td class="test3" rowspan="12">&nbsp;</td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 3, 1) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 3, 1) ?></td>
				</tr>

							<tr>
								<td height="5"></td>
								<td rowspan="6" colspan="2"></td>
								<td rowspan="2" colspan="2">13 июля<?php// echo Html::getDate($games, '1', 0) ?></td>
							</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 3, 2) ?></td>
					<td class="test centered" rowspan="2"><?php echo Html::getScore($games, '1/8', 3, 2) ?></td>
					<td class="test3" rowspan="6">&nbsp;</td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2">&nbsp;</td>
					<td align="center" class="test" rowspan="2"></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 4)?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test2" rowspan="23">&nbsp;</td>
					<td class="test" rowspan="2">&nbsp;</td>
					<td align="center" class="test" rowspan="2"></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 4, 1)?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 4, 1)?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/4', 2) ?></td>
						<td rowspan="10" colspan="2"></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 4, 2)?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 4, 2)?></td>
					<td class="playoff-border" rowspan="6">&nbsp;</td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 0, 1)?></td>
						<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/4', 0, 1)?></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 4)?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test3" rowspan="12">&nbsp;</td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 0, 2)?></td>
						<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/4', 0, 2)?></td>
						<td class="playoff-border" rowspan="12">&nbsp;</td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 5, 1)?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 5, 1)?></td>
				</tr>

						<tr>
							<td height="5"></td>
							<td rowspan="6" colspan="2"></td>
							<td rowspan="2" colspan="2">9 июля<?php// echo Html::getDate($games, '1/2', 1) ?></td>
						</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 5, 2)?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 5, 2)?></td>
					<td class="test3" rowspan="6">&nbsp;</td>
				</tr>

						<tr>
							<td height="5"></td>
							<td class="test" rowspan="2">&nbsp;</td>
							<td class="test centered" rowspan="2"></td>
						</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 6)?></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test2" rowspan="11">&nbsp;</td>
					<td class="test" rowspan="2">&nbsp;</td>
					<td align="center" class="test" rowspan="2"></td>
					<td class="test2" rowspan="11">&nbsp;</td>
					<td align="center" rowspan="2" colspan="2"><strong>Третье место</strong></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 6, 1) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 6, 1) ?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/4', 3) ?></td>
						<td rowspan="9" colspan="2"></td>
						<td rowspan="2" colspan="2">12 июля</td>
					</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 6, 2) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 6, 2) ?></td>
					<td class="playoff-border" rowspan="6">&nbsp;</td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 2, 1) ?></td>
						<td class="test centered" rowspan="2"><?php echo Html::getScore($games, '1/4', 2, 1) ?></td>
						<td class="test" rowspan="2">&nbsp;</td>
						<td class="test centered" rowspan="2"></td>
					</tr>

				<tr>
					<td height="5"></td>
					<td rowspan="2" colspan="2"><?php echo Html::getDate($games, '1/8', 7)?></td>
				</tr>

					<tr>
						<td height="5"></td>
						<td class="test2" rowspan="5">&nbsp;</td>
						<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/4', 2, 2) ?></td>
						<td class="test centered" rowspan="2"><?php echo Html::getScore($games, '1/4', 2, 2) ?></td>
						<td class="test2" rowspan="5">&nbsp;</td>
						<td class="test" rowspan="2">&nbsp;</td>
						<td align="center" class="test" rowspan="2"></td>
					</tr>

				<tr>
				<td height="5"></td>
				<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 7, 1) ?></td>
				<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 7, 1) ?></td>
				</tr>

				<tr>
				<td height="5"></td>
				<td rowspan="3" colspan="2"></td>
				<td rowspan="3" colspan="2"></td>
				</tr>

				<tr>
					<td height="5"></td>
					<td class="test" rowspan="2"><?php echo Html::getTeam($games, '1/8', 7, 2) ?></td>
					<td align="center" class="test" rowspan="2"><?php echo Html::getScore($games, '1/8', 7, 2) ?></td>
					<td class="test2" rowspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td height="5"></td>
				</tr>
			</tbody>
		</table>
		<?
	}

	public static function showGroupScheme($games)
	{
		?>
			<table>
				<tr></tr>
			</table>
		<?
	}

	public static function getResults()
	{
		?>
			<div>
				<strong>Групповой этап</strong>
				<ol class="result_list">
					<li>sadfasdf</li>
					<li></li>
					<li></li>
				</ol>
				<br/>

				<strong>Плей-офф</strong>
				<ol class="result_list">
					<li></li>
					<li></li>
					<li></li>
				</ol>
				<br/>

				<strong>Общий зачет</strong>
				<ol class="result_list">
					<li><p class="gold">vlad</p> - 101 очко</li>
					<li><p class="silver">фифа-бот</p> - 99 очков</li>
					<li><p class="bronze">pasha</p> - 96 очков</li>
				</ol>
				<hr/>
				<strong>Награды</strong>
			</div>
		<?
	}

	public static function getRules()
	{
		?>
			<div>
				<strong>Основные моменты</strong>
				<br><br>
				1. Для добавления прогнозов необходимо <a href="/auth.php">войти</a> на сайт.<br>
				2. Прогнозы принимаются и могут меняться пользователем до начала матча<br>
				<img class="help-image" src="/img/help/howto.png"/>
				<br>
				3. После окончания матча в систему вводится его результат и происходит подсчет, сколько очков получил каждый пользователь за свой прогноз<br>
				<img class="help-image" src="/img/help/tmp.png"/>
				<br>
				4. На вкладке "Рейтинг" отражается количество очков за все прогнозы для каждого пользователя<br>
				<img class="help-image" src="/img/help/result.png"/>
				<br>
				<strong>Подсчет очков</strong>
				<br><br>
				Количество очков за матч начисляется по формуле:
				<ol>
					<li>4 очка за угаданный счет</li>
					<li>3 очка за угаданный исход и разницу мячей</li>
					<li>2 очка за угаданный исход и отличие в разнице мячей < 1.5 мяча</li>
					<li>1 очко за угаданный исход и отличие в разнице мячей > 1.5 мяча</li>
				</ol>
				<ol style="list-style-type:none; margin-left: 0px; padding-left: 0px;">
					<li>Если победитель матча определяется серией пенальти, то счетом матча считается счет после дополнительного времени.</li>
					<li>Если матч дошел до серии пенальти и итоговый исход ничья, то участник, поставивший на победу и угадавший победителя, получает 1 очко.</li>
				</ol>
				При равенстве очков победитель определяется по наибольшему количеству точно угаданных счетов; при их равенстве - по количеству точно угаданных исходов (победа, ничья)</li>
				<br><br>
				<strong>Про игроков</strong>
				<br><br>
				1. В турнирах принимают участие различные боты. Тупой случайным образом выбирает вариант из самых вероятных результатов. Умные генерируют прогноз, учитывая силы команд.
				</ul>
				<br>
			</div>
		<?
	}

	public static function getTeam($games, $round, $game_index, $team_index = 1)
	{
		$strong = '';
		$strong2 = '';
		if ($team_index == Y::getWinnerID($games, $round, $game_index))
		{
			$strong = '<strong>';
			$strong2 = '</strong>';
		}

		return '&nbsp;<img style="width:22px; height:15px" src="/img/teams/'.$games[$round][$game_index]['team'.$team_index.'_id'].'.png" />&nbsp;'.$strong.$games[$round][$game_index]['team'.$team_index.'_name'].$strong2;
	}

	public static function getScore($games, $round, $game_index, $team_index = 1)
	{
		$strong = '';
		$strong2 = '';
		if ($team_index == Y::getWinnerID($games, $round, $game_index))
		{
			$strong = '<strong>';
			$strong2 = '</strong>';
		}

		$result = explode(':', $games[$round][$game_index]['result']);

		$team_result = $result[$team_index-1];

		if (strlen($games[$round][$game_index]['penalty_result']) > 0)
		{
			$penalty_result = explode(':', $games[$round][$game_index]['penalty_result']);
			$team_result .= '('.$penalty_result[$team_index-1].')';
		}

		return $strong.$team_result.$strong2;
	}

	public static function getDate($games, $round, $game_index)
	{
		return Y::day_month($games[$round][$game_index]['started_at']);
	}

	public static function getTimeList($list)
	{
		?>
			<table id="list" class="table table-striped" style="max-width: 600px">
				<thead>
					<th><strong>User</strong></th>
					<th><strong>Game</strong></th>
					<th><strong>When</strong></th>
				</thead>
				<tbody>
					<? foreach ($list as $prognosis): ?>
					<tr>
						<td class="centered"><?=Y::getUserName($prognosis['user_id'], $prognosis['name'])?></td>
						<td class="centered"><?=Y::day_month($prognosis['started_at'])?> - <? echo $prognosis['team1_name']." - ".$prognosis['team2_name'];?></td><?//." ".Y::r($prognosis['team1'])." : ".Y::r($prognosis['team2'])?>
						<td class="centered" title="<?=$prognosis['updated_at']?>"><?=Y::hour_minute($prognosis['updated_at'])?></td>
					</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		<?
	}
}

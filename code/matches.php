<?php
if (!defined('INSIDE_CALL')) die();
include_once('./templates/top_menu.php');

$user_id = $user->getId();

$tournament_id = DEFAULT_TOURNAMENT;
if (array_key_exists('cup', $_GET)) {
	$tournament_id = Tournament::getIDByPrefix($_GET['cup']);
}

$user_group = null;
if (array_key_exists('group', $_GET)) {
	$user_group = $_GET['group'];
}

$games = Game::getInfoFilteredByUser($user_id, $tournament_id);
$prognoses = Prognosis::getFilteredForGroup($tournament_id, $user_group);

// TODO
$messages = Chat::getAll();
// User::convertpw();

// if (array_key_exists('response', $_POST)) {
// 	Y::send(ADMIN_EMAIL, 'response', $_POST['response']);
// 	$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/email_sent.txt', 'a');
// 	fwrite($fp, print_r( $_POST['response'] ,true));
// 	fwrite($fp, print_r( "\n--------".Date("d-M H:i:s")."-------\n" ,true));
// 	fclose($fp);
// 	unset($_POST['response']);
// }

$current_tab = Y::getCurrentTab();

$userGroups = User::getGroups($user->getId());
?>
<div class="container-fluid">

	<?
	// message
	if (false && $user_id == MSG_USER_ID && !file_exists($_SERVER['DOCUMENT_ROOT'].'/email_sent.txt')) {
	?>
		<div class="alert alert-info" style="width: 600px">
			<p>Привет, это админ</p>
			<p>Напиши, пожалуйста, от кого ты пришел на сайт</p>
			<form action="/" method="post">
				<p><textarea name="response" style="width: 100%; padding: 5px; margin-top: 5px; border: solid 1px lightblue; height: 100px"></textarea>
				<p><input type="submit" style="" value="Отправить" /></p>
			</form>
		</div>
	<?
	}
	?>

	<? if (count($userGroups) > 0): ?>
		<div class="row" style="padding: 2px 0px 5px 5px; border-bottom: solid 1px lightgrey; margin-bottom: 5px">
			<?
			foreach ($userGroups as $id => $group):
				$strong = "";
				if ($user_group == $group['name'])
					$strong = "current_user_group";
			?>
				<? if ($group['name'] == "all"): ?>

					<? if ($user_group == null): ?>
						<a class="group_plate current_user_group" href="/">Все пользователи</a>
					<? else: ?>
						<a class="group_plate" href="/">Все пользователи</a>
					<? endif ?>

				<? else: ?>
					<a class="group_plate <?=$strong?>" href="/?group=<?=$group['name']?>">
						<?=$group['name']?>
					</a>
				<? endif ?>

			<? endforeach ?>
		</div>
	<? endif; ?>

	<div class="row-fluid">
		<div class="">
			<div class="tabbable">

				<ul class="nav nav-tabs">
					<li class="<?=Y::is_active($current_tab, "first")?>"><a href="#s1" id="first" data-toggle="tab">Матчи</a></li>
					<li class="<?=Y::is_active($current_tab, "diagram")?>"><a href="#s2" id="diagram" data-toggle="tab">Рейтинг</a></li>
					<li class="<?=Y::is_active($current_tab, "playoff_matches")?>"><a href="#s6" id="playoff_matches" data-toggle="tab">Матчи плей-офф</a></li>
					<li class="<?=Y::is_active($current_tab, "playoff_list")?>"><a href="#s5" id="playoff_list" data-toggle="tab">Рейтинг плей-офф</a></li>
					<?php
					/*
					<!-- <li class=""><a href="#s7" id="playoff_list" data-toggle="tab">Групповой этап</a></li> -->
					<!-- <li class=""><a href="#s8" id="playoff_list" data-toggle="tab">Сетка плей-офф</a></li> -->
					*/
					?>
					<li class="<?=Y::is_active($current_tab, "list")?>"><a href="#s4" id="list" data-toggle="tab">Новые прогнозы</a></li>
					<?
					if (false && $user->isAuthorized()){
					?>
						<li class="<?=Y::is_active($current_tab, "chat_tab")?>"><a href="#s7" id="chat_tab" data-toggle="tab">Обсуждение</a></li>
					<?
					}
					?>
					<?
					if (false && $user->isAuthorized()){
					?>
						<li class="<?=Y::is_active($current_tab, "results")?>"><a href="#s9" id="results" data-toggle="tab">Итоги</a></li>
					<?
					}
					?>
					<li class="<?=Y::is_active($current_tab, "rules")?>"><a href="#s3" id="rules" data-toggle="tab">Правила</a></li>
				</ul>

				<div class="tab-content">

					<div class="tab-pane <?=Y::is_active($current_tab, "first")?> well" id="s1" style="max-width: 1100px">
						<?php echo Html::showGamesTable('games', $games, $prognoses); ?>
					</div>

					<!-- РЕЙТИНГ -->

					<div class="tab-pane <?=Y::is_active($current_tab, "diagram")?>" id="s2">
						<?php echo Html::showRatingTable('rating', $games, $prognoses);	?>
					</div>

					<div class="tab-pane <?=Y::is_active($current_tab, "playoff_matches")?> well" id="s6" style="max-width: 1100px">
						<?php echo Html::showGamesTable('playoff_games', $games, $prognoses, true); ?>
					</div>

					<!-- РЕЙТИНГ ПЛЕЙ-ОФФ-->

					<div class="tab-pane <?=Y::is_active($current_tab, "playoff_list")?>" id="s5">
						<?php echo Html::showRatingTable('plrating', $games, $prognoses, true); ?>
					</div>

					<?
					if (false && $user->isAuthorized()){
					?>
						<div class="tab-pane" id="s7">
							<div class="row-fluid" style="margin-bottom: 5px">
							    <div id="chat" style="max-width: 1100px">
							        <div class="panel-body" style="border: solid 1px lightgrey">
							            <ul class="chat">
							            	<? foreach ($messages as $msg) {
							            		?>
							                    <li class="clearfix">
							                        <div class="chat-body clearfix">
							                            <?=$msg['created_at']?> <strong><?=$msg['user_name']?></strong>: <?=$msg['message']?>
							                        </div>
							                    </li>
							                <? } ?>
							            </ul>
							        </div>
							        <div class="panel-footer">
							            <div class="input-group">
							                <input id="btn-input" type="text" class="form-control input-sm" placeholder="..." />
							                <span class="input-group-btn">
							                    <button class="btn btn-warning btn-sm" id="btn-chat">
							                        Send</button>
							                </span>
							            </div>
							        </div>
							    </div>
							</div>
						</div>
					<?
					}
					?>

					<div class="tab-pane" id="s8">
						<?php // echo Html::showPlayoffScheme($games) ?>
					</div>

					<!-- ПРАВИЛА -->

					<div class="tab-pane <?=Y::is_active($current_tab, "list")?>" id="s4">
						<?php
							$list = Prognosis::getListVisibleForGroup($user_id, $tournament_id, $user_group);
							echo Html::getTimeList($list);
						?>
					</div>

					<?
					if (false && $user->isAuthorized()):
					?>
						<div class="tab-pane <?=Y::is_active($current_tab, "results")?>" id="s9">
							<?php echo Html::getResults()?>
						</div>
					<?
					endif;
					?>

					<div class="tab-pane <?=Y::is_active($current_tab, "rules")?>" id="s3">
						<?php echo Html::getRules()?>
					</div>

					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
				</div><!--  end of tabs -->

			</div>
		</div>
	</div>

	<hr>
	<?
	require_once('./templates/visual_footer.php');
	?>
</div>
<?
require_once('./templates/footer.php');
?>

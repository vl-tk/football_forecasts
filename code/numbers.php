<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once('./templates/header.php');
require_once('./templates/visual_header.php');
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

$userGroups = User::getGroups($user->getId());
?>
<div class="container-fluid">

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
						<a class="group_plate current_user_group" href="/numbers.php">All users</a>
					<? else: ?>
						<a class="group_plate" href="/numbers.php">All users</a>
					<? endif ?>

				<? else: ?>
					<a class="group_plate <?=$strong?>" href="/numbers.php?group=<?=$group['name']?>">
						<?=$group['name']?>
					</a>
				<? endif ?>

			<? endforeach ?>
		</div>
	<? endif; ?>

	<div class="row-fluid">
		<div>
			<?php echo Html::showNumbers('numbers', $games, $prognoses);	?>
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

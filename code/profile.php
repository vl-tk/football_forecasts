<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once('./templates/header.php');
require_once('./templates/visual_header.php');
include_once('./templates/top_menu.php');

if ($user->isAuthorized()):
$userGroups = User::getGroups($user->getId());
$userOwnedGroups = User::getOwnedGroups($user->getId());
?>
	<div class="container-fluid">

		<div class="row-fluid">
			<div class="">
				Профиль <strong><?=$user->getLogin()?></strong>
			</div>
			<hr/>

			<div class="">
				<strong>Состоит в группах</strong>
				<ol>
				<? foreach ($userGroups as $id => $group): ?>

					<? if ($id == 0): ?>
						<li><a href="/">Все пользователи</a></li>
					<? else: ?>
						<li><a href="/?group=<?=$group['name']?>"><?=$group['name']?></a></li>
					<? endif ?>

				<? endforeach ?>
				</ol>
			</div>

			<div class="">
				<? if (count($userOwnedGroups) > 0): ?>
				<strong>Мои группы</strong>
					<ol>
					<?
					foreach ($userOwnedGroups as $id => $group):
						$link = 'http://'.$_SERVER['HTTP_HOST'].'/join.php?group='.$group['name'].'&code='.$group['code'];
					?>
						<li>
							<strong><?=$group['name']?></strong>&nbsp;(<?=$group['user_count']?>)
							<input class="link" type="text" value="<?=$link?>" />

								<?
								$group_users = UserGroup::getGroupUsers($group['id'], $user->getId());
								if (count($group_users) > 0):
								?>
									<ol>
									<?
									foreach ($group_users as $id => $group_user):
									?>
										<li>
											<?=$group_user['name']?>
										</li>
									<?
									endforeach
									?>
									</ol>
								<?
								endif;
								?>
							<a href="#" class="group_delete_link" data-group-id="<?=$group['id']?>">Удалить группу</a>
						</li>
						<br/>
					<? endforeach ?>
					</ol>
				<? endif; ?>

				<? if (($user->isAdmin() || $user->isManager()) && count($userOwnedGroups) < MAX_GROUPS_TO_OWN): ?>
					<a href="#" class="group_create_btn">Создать группу</a>
				<? endif; ?>
			</div>
		</div>

		<hr>
		<?
		require_once('./templates/visual_footer.php');
		?>
	</div>
<?
else:
endif;
?>

<?
require_once('./templates/footer.php');
?>

<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once('./templates/header.php');
require_once('./templates/visual_header.php');
include_once('./templates/top_menu.php');

if ($user->isAuthorized()):

$group_name = $_GET['group'];
$group_code = $_GET['code'];

$group = Group::join(
	$user->getId(),
	$group_name,
	$group_code
);

if (!array_key_exists('error', $group)):
?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="">
				Вы успешно вступили в группу <a href="/?group=<?=$group['name']?>"><?=$group['name']?></a>.
			</div>
		</div>
	</div>
<? else: ?>
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="">
				Ошибка вступления в группу. <?=$group['error']?>
			</div>
		</div>
	</div>
<?
endif;
else:
?>
<?
endif;
?>

<?
require_once('./templates/visual_footer.php');
require_once('./templates/footer.php');
?>

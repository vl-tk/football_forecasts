<?php

include_once("./templates/system_header.php");

$user_id = $user->getId();
$res = false;

if (isset($_POST['finished']))
{
	if ($user->isAdmin())
	{
		if ($_POST['finished'] == 'Y')
			Game::finish($_POST['game_id']);
		else if ($_POST['finished'] == 'N')
			Game::start($_POST['game_id']);

		echo 'success';
	}
	else
	{
		echo 'error:';
	}

	die();
}
else if (isset($_POST['value']))
{
	$res = Prognosis::set($user_id, $_POST['game_id'],	$_POST['team_number'], $_POST['value']);

	if ($res)
		echo 'success';
	else
		echo 'error';

	die();
}
else if (isset($_POST['result']))
{
	if ($user->isAdmin() || $user->isManager())
		$res = Game::update($_POST['game_id'], array('result' => $_POST['result']));

	if ($res)
		echo 'success';
	else
		echo 'error';

	die();
}
else if (isset($_POST['random']))
{
	if ($user->isAdmin())
	{
		$clever = ($_POST['clever'] == 'Y') ? true : false;

		if ($clever && isset($_POST['team1_rank']) && isset($_POST['team2_rank']))
		{
			$uid = SMART_BOT_USER_ID;
			$prognosis = Bot::getPrognosis($_POST['team1_rank'], $_POST['team2_rank']);
		}
		else if ($clever && isset($_POST['team1_fifa_rank']) && isset($_POST['team2_fifa_rank']))
		{
			$uid = FIFA_BOT_USER_ID;
			$prognosis = Bot::getPrognosis($_POST['team1_fifa_rank'], $_POST['team2_fifa_rank']);
		}
		else
		{
			$uid = STUPID_BOT_USER_ID;
			$prognosis = Bot::getSimplePrognosis();
		}

		$res = Prognosis::set($uid, $_POST['game_id'],	1, $prognosis[0]);
		$res = Prognosis::set($uid, $_POST['game_id'],	2, $prognosis[1]);
	}

	if ($res)
		echo 'success';
	else
		echo 'error';

	die();
}
else if (isset($_POST['chat']))
{
	$res = false;

	if ($user->isAuthorized()) {
		$res = Chat::add(array(
			'user_id' => $user->getId(),
			'message' => $_POST['message'],
		));
	}
	if ($res)
		echo 'success';
	else
		echo 'error';

	die();
}
else if (isset($_POST['group_create']))
{
	$res = false;

	// if ($user->isAuthorized() && ($user->isManager() || $user->isAdmin())) {
	if ($user->isAuthorized()) {
		$res = Group::create(array(
			'user_id' => $user->getId(),
			'name' => $_POST['group_name']
		));
	}
	if ($res) {
		if (is_array($res) && array_key_exists('status', $res) && $res['status'] == 'ok')
			echo 'success';
		else
			echo $res['error'];
	}
	else
		echo $res['error'];

	die();
}
else if (isset($_POST['group_delete']))
{
	$res = false;

	if ($user->isAuthorized() && ($user->isGroupOwner($_POST['group_id']))) {
		$res = Group::delete(array(
			'user_id' => $user->getId(),
			'group_id' => $_POST['group_id']
		));

	}

	if ($res)
		echo 'success';
	else
		echo 'error';

	die();
}

?>

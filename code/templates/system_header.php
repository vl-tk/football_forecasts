<?
error_reporting(E_ALL ^ E_NOTICE);
setlocale(LC_ALL, 'ru_RU.UTF-8');
$start = microtime(true);
require_once("./config/config.php");
require_once("./lib/database.php");
require_once("./lib/y.php");
require_once("./lib/html.php");
require_once("./lib/user.php");
require_once("./lib/group.php");
require_once("./lib/game.php");
require_once("./lib/usergroup.php");
require_once("./lib/tournament.php");
require_once("./lib/calc.php");
require_once("./lib/prognosis.php");
require_once("./lib/chat.php");
require_once("./lib/log.php");
require_once("./lib/bot.php");
require_once("./lib/agent.php");

$error = array();
$user = new User();

session_start();

if (isset($_REQUEST["out"]) && $_REQUEST["out"] == "Y")
{
	$user->unauthorize();
}
else
{
	if (!$user->isAuthorized())
	{
		if (isset($_REQUEST['register']) && $_REQUEST['register'] == "Y")
		{
			$res = $user->register(
				$_REQUEST['login'],
				$_REQUEST['name'],
				$_REQUEST['password'],
				$_REQUEST['password2']
			);

			if (is_array($res))
				$error = $res;
			else {
				$user->authorize($_REQUEST['login'], $_REQUEST['password']);
			}
		}
		else if (isset($_REQUEST['login']) && isset($_REQUEST['password']))
		{
			$remember = (isset($_REQUEST['remember'])) ? $_REQUEST['remember'] : '';

			$res = $user->authorize($_REQUEST['login'], $_REQUEST['password'], $remember);

			if (is_array($res))
				$error = $res;
		}
		else
		{
			$authorized = false;

			if (isset($_COOKIE['sessid']))
			{
				$res = $user->loginByHash($_COOKIE['sessid']);

				if ($res === true)
					$authorized = true;
			}

			if (!$authorized && isset($_SESSION['sessid']))
			{
				$res = $user->loginByHash($_SESSION['sessid']);

				if ($res === true)
					$authorized = true;
			}
		}
	}
	else
	{
		Agent::checkAgents();
	}
}

define('INSIDE_CALL', true);

// if (!$user->isAuthorized())
// {
// 	include_once("./visual_header.php");
// 	include_once("./auth.php");
// 	die();
// }
?>

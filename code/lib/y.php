<?php

class Y
{
	/**
	* Shows appropriate text for the number
	*/
	public static function pl($n, $form1, $form2, $form5)
	{
		$n = abs($n) % 100;
		$n1 = $n % 10;

		if ($n > 10 && $n < 20)
			$res = $form5;
		else if ($n1 > 1 && $n1 < 5)
			$res = $form2;
		else if ($n1 == 1)
			$res = $form1;
		else
			$res = $form5;

		return $res;
	}

	public static function cmp($a, $b)
	{
		if ($a['total'] != $b['total'])
			return ($a['total'] > $b['total']) ? -1 : 1;

		if ($a['exact'] != $b['exact'])
			return ($a['exact'] > $b['exact']) ? -1 : 1;

		if ($a['exact_result'] != $b['exact_result'])
			return ($a['exact_result'] > $b['exact_result']) ? -1 : 1;

		if (array_key_exists('name', $a) && array_key_exists('name', $b))
			return strnatcmp($a['name'], $b['name']);

		return 0;
	}

	public static function hour_minute($d)
	{
		$date = new DateTime($d);
		return $date->format('H:i');
	}

	public static function getRusMonth($month)
	{
		if($month > 12 || $month < 1)
			return false;

		$aMonth = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');

		return $aMonth[$month - 1];
	}

	public static function getRusShortDay($daynumber)
	{
		if($daynumber > 7 || $daynumber < 1)
			return false;

		$values = array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');

		return $values[$daynumber-1];
	}

	public static function day_month($d)
	{
		$date = new DateTime($d);

		return $date->format('j').'&nbsp;'.self::getRusMonth($date->format('m')).',&nbsp;'.self::getRusShortDay($date->format('N'));
	}

	public static function isLater($date1, $date2)
	{
		$d1 = new DateTime($date1);
		$d2 = new DateTime($date2);

		return ($d1->format("Y-m-d") > $d2->format("Y-m-d")) ? true : false;
	}

	public static function getCurrentTab() {
		$tabs = array('first', 'diagram', 'playoff_matches', 'playoff_list', 'list', 'rules');
		$current_tab = $_COOKIE["tab"];
		if (!in_array($current_tab, $tabs))
			$current_tab = 'first';
		return $current_tab;
	}


	public static function is_active($current_tab, $code)
	{
		if ($current_tab == $code)
			return 'active';
		return '';
	}

	public static function r($prognosis_number)
	{
		return (strlen($prognosis_number) > 0) ? $prognosis_number : '?';
	}

	public static function sign($val)
	{
		$val = (int) $val;

		if ($val > 0)
			return '+';
		else if ($val < 0)
			return '-';
		else
			return '=';
	}

	public static function same_result($r1, $r2, $p1, $p2, $pr1 = null, $pr2 = null)
	{
		$r1 = (int) $r1;
		$r2 = (int) $r2;
		$p1 = (int) $p1;
		$p2 = (int) $p2;

		if ($pr1 == null || $pr2 == null)
		{
			if (Y::sign($r1 - $r2) === Y::sign($p1 - $p2))
				return true;
			else
				return false;
		}
		else
		{
			$pr1 = (int) $pr1;
			$pr2 = (int) $pr2;

			if (Y::sign($pr1 - $pr2) === Y::sign($p1 - $p2))
				return true;
			else
				return false;
		}
	}

	public static function getWinnerID($games, $round, $game_index)
	{
		$result = explode(':', $games[$round][$game_index]['result']);
		$penalty_result = explode(':', $games[$round][$game_index]['penalty_result']);

		$team1_result = $result[0];
		$team2_result = $result[1];
		$team1_penalty_result = $penalty_result[0];
		$team2_penalty_result = $penalty_result[1];

		if ($team1_result > $team2_result)
		{
			return 1;
		}
		else if ($team1_result < $team2_result)
		{
			return 2;
		}
		else // draw
		{
			if ($team1_penalty_result > $team2_penalty_result)
				return 1;
			else if ($team1_penalty_result < $team2_penalty_result)
				return 2;
		}
	}

	public static function send($to, $subject = "(No subject)", $message = "", $add_header = "")
	{
		$header = "MIME-Version: 1.0\r\n".
		$header .= "Content-type: text/plain; charset=UTF-8\r\n";
		$header .= "From: New Prognosis <football.gvr.lclients.ru>\r\n";
		$header .= $add_header;

		mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $header);
	}

	public static function getSmile($user_id, $pos)
	{
		$result = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		if ($pos == 1)
			return '&nbsp;&nbsp;<img class="smile hiddenx" style="border:none" src="/img/smiles/mooning.gif" />';

		if ($pos == 2)
			return '&nbsp;&nbsp;<img class="smile hiddenx" style="smile border:none" src="/img/smiles/poolparty.gif" />';

		return $result;
	}

	public static function getPlayoffRoundName($code)
	{
		switch ($code)
		{
			case '1/8':
				return '1/8 Финала';
				break;

			case '1/4':
				return '1/4 Финала';
				break;

			case '1/2':
				return 'Полуфинал';
				break;

			case '3':
				return 'Матч за 3-е место';
				break;

			case '1':
				return 'Финал';
				break;

			default:
				return '';
				break;
		}
	}

	public static function getUserName($uid, $username='', $motto='')
	{
		global $user;
		if ($user->isAuthorized())
		{
			if ($user->getId() == $uid) {
				return '<span class="current_user">'.$username.'</span>';
			}
		}

		return $username;

		/*
		$names = array(
			'john',
			'smith',
			'patrick',
			'oscar',
			'jo',
			'gold',
			'peter',
			'super',
			'sam',
			'mars',
			'eminem',
			'wayne',
			'first',
			'zamok',
			'dukalis',
			'michael',
			'twister',
			'general',
			'ok',
			'adolf',
			'samovar',
			'goalkeeper',
			'samuel',
			'prosto',
			'muhomor',
			'space'
		);

		global $user;
		if (!$user->isAuthorized())
		{
			if (in_array($uid, Bot::$bots))
				$res = $username;
			else
			{
				$res = $names[$uid];
			}
		}
		else
			$res = $username;

		if ($motto != '') {
			return '<span class="motto" title="'.$motto.'">'.$res.'</span>';
		}

		return $res;
		*/
	}
}

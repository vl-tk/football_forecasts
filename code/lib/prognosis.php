<?php

class Prognosis
{
	public static function get($params = array())
	{
		$filter = '';

		$query = "
			SELECT P.*, U.login, U.name
			FROM tbl_prognosis P
			INNER JOIN tbl_user U ON U.id = P.user_id
			WHERE (1=1) ".$filter."
			ORDER BY id DESC";

		return Database::makeQuery($query);
	}

	public static function getGroupUserIDs($user_group)
	{
		$list = array();

		$query = sprintf("
			SELECT U.id
			FROM tbl_user U
			LEFT JOIN tbl_user2group UG ON UG.user_id = U.id
			LEFT JOIN tbl_group G ON G.id = UG.group_id
			WHERE (1=1) AND G.name = '%s'",
			mysqli_real_escape_string($user_group)
		);

		$res = Database::makeQuery($query);

		if ($res)
		{
			while ($item = mysqli_fetch_array($res))
				$list[] = $item['id'];
		}

		return $list;
	}

	/*
	new logic
	if filter output for group
	*/
	public static function getListVisibleForGroup($user_id=null, $tournament_id=null, $user_group=null)
	{
		$user_id = (int) $user_id;
		$user_id = ($user_id == 0) ? 1 : $user_id;

		if ($user_group) {
			$user_ids = self::getGroupUserIDs($user_group);
			$str_users = implode (", ", $user_ids);
			$user_subquery = "AND U.id IN (".$str_users.")";
		}
		else {
			$user_subquery = "";
		}

		$list = array();

		$query = "
			SELECT
				distinct P.*,
				U.name,
				U.motto,
				T1.name as team1_name,
				T2.name as team2_name,
				G.started_at
			FROM tbl_prognosis P
			LEFT JOIN tbl_user U ON U.id = P.user_id
			LEFT JOIN tbl_game G ON G.id = P.game_id
			LEFT JOIN tbl_team T1 ON T1.id = G.team1_id
			LEFT JOIN tbl_team T2 ON T2.id = G.team2_id
			LEFT JOIN tbl_user2group UG ON UG.user_id = U.id
			WHERE (1=1)
			".$user_subquery."
			AND G.tournament_id = ".$tournament_id."
			ORDER BY P.updated_at DESC
			LIMIT 30";

		$res = Database::makeQuery($query);

		if ($res)
		{
			while ($item = mysqli_fetch_array($res))
				$list[] = $item;
		}

		return $list;
	}

	public static function getFilteredForGroup($tournament_id, $user_group=null)
	{
		if ($user_group) {
			$user_ids = self::getGroupUserIDs($user_group);
			$str_users = implode (", ", $user_ids);
			$user_subquery = "AND U.id IN (".$str_users.")";
		}
		else {
			$user_subquery = "";
		}

		$prognoses = array();

		$query = "
			SELECT
				P.*, U.login, U.name, U.motto, G.result
			FROM tbl_prognosis P
			INNER JOIN tbl_user U ON U.id = P.user_id
			INNER JOIN tbl_user2group UG ON UG.user_id = U.id
			INNER JOIN tbl_game G on G.id = P.game_id
			WHERE (1=1)
			".$user_subquery."
			AND G.tournament_id = ".$tournament_id."
			ORDER BY id asc";

		$res = Database::makeQuery($query);

		if ($res)
		{
			$prognoses = array();
			while ($prognosis = mysqli_fetch_array($res))
			{
				if (array_key_exists($prognosis['game_id'], $prognoses))
				{
					if (!in_array($prognosis, $prognoses[$prognosis['game_id']]))
						$prognoses[$prognosis['game_id']][] = $prognosis;
				}
				else
				{
					$prognoses[$prognosis['game_id']][] = $prognosis;
				}
			}
		}

		return $prognoses;
	}

	/*
	old prohibitive logic
	if some user is allowed to see only his group users
	public static function getListVisibleByUser($user_id=null, $tournament_id=null)
	{
		$user_id = (int) $user_id;
		$user_id = ($user_id == 0) ? 1 : $user_id;

		$list = array();

		$query = "
			SELECT
				distinct P.*,
				U.name,
				U.motto,
				T1.name as team1_name,
				T2.name as team2_name,
				G.started_at
			FROM tbl_prognosis P
			LEFT JOIN tbl_user U ON U.id = P.user_id
			LEFT JOIN tbl_game G ON G.id = P.game_id
			LEFT JOIN tbl_team T1 ON T1.id = G.team1_id
			LEFT JOIN tbl_team T2 ON T2.id = G.team2_id
			LEFT JOIN tbl_user2group UG ON UG.user_id = U.id
			WHERE (1=1) AND UG.group_id in (select group_id from tbl_user2group UG2 where user_id = ".$user_id.")
			AND G.tournament_id = ".$tournament_id."
			ORDER BY P.updated_at DESC
			LIMIT 30";

		$res = Database::makeQuery($query);

		if ($res)
		{
			while ($item = mysqli_fetch_array($res))
				$list[] = $item;
		}

		return $list;
	}

	old
	public static function getFilteredByUser($user_id=null, $tournament_id=null)
	{
		$user_id = (int) $user_id;
		$user_id = ($user_id == 0) ? 1 : $user_id;

		$prognoses = array();

		$query = "
			SELECT P.*, U.login, U.name, U.motto, G.result
			FROM tbl_prognosis P
			INNER JOIN tbl_user U ON U.id = P.user_id
			INNER JOIN tbl_user2group UG ON UG.user_id = U.id
			INNER JOIN tbl_game G on G.id = P.game_id
			WHERE (1=1)
			AND UG.group_id in (select group_id from tbl_user2group UG2 where user_id = ".$user_id.")
			AND G.tournament_id = ".$tournament_id."
			ORDER BY id asc";

		$res = Database::makeQuery($query);

		if ($res)
		{
			$prognoses = array();
			while ($prognosis = mysqli_fetch_array($res))
			{
				if (array_key_exists($prognosis['game_id'], $prognoses))
				{
					if (!in_array($prognosis, $prognoses[$prognosis['game_id']]))
						$prognoses[$prognosis['game_id']][] = $prognosis;
				}
				else
				{
					$prognoses[$prognosis['game_id']][] = $prognosis;
				}
			}
		}

		return $prognoses;
	}
	*/

	public static function add($params)
	{
		$keys = "";
		$values = "";

		foreach ($params as $key => $value)
		{
			$keys .= (strlen($keys) > 0) ? ", ".$key."" : "".$key."";
			$values .= (strlen($values) > 0) ? ", '".$value."'" : "'".$value."'";
		}

		$sql = "INSERT INTO tbl_prognosis (".$keys.", created_at, updated_at) VALUES (".$values.", NOW(), NOW())";

		return Database::makeQuery($sql);
	}

	public static function update($id, $params)
	{
		$str = '';
		foreach ($params as $key => $value)
		{
			if (strlen($str) > 0)
				$str .= ', ';

			$str .= $key.' = '.$value;
		}

		$sql = "UPDATE tbl_prognosis SET ".$str.", updated_at = NOW() WHERE id = ".intval($id);

		return Database::makeQuery($sql);
	}

	public static function canSetPrognosis($game_id)
	{
		$res = false;

		$game_id = (int) $game_id;

		$sql = "SELECT id, finished FROM tbl_game WHERE id = ".$game_id;

		$result = Database::makeQuery($sql);

		while ($game_record = mysqli_fetch_array($result))
		{
			if ($game_record['finished'] != 'Y')
			{
				$res = true;
				break;
			}
		}

		return $res;
	}

	public static function set($user_id, $game_id, $team_number, $value)
	{
		$value = (int) $value;
		$res = false;

		if (Prognosis::canSetPrognosis($game_id))
		{
			$id = Prognosis::exists($user_id, $game_id, $team_number);

			if ($id === false)
			{
				if ($team_number == 1)
					$params = array('team1' => $value);
				else if ($team_number == 2)
					$params = array('team2' => $value);

				$params = array_merge($params, array('user_id' => $user_id, 'game_id' => $game_id));

				$res = Prognosis::add($params);

				if ($res)
				{
					$mail = self::getMailInfo($user_id, $game_id);

					Log::write('Прогноз добавлен: '.$mail['name'].' на игру '.$mail['team1_name'].' - '.$mail['team2_name'].' '.Y::r($mail['team1']).' : '.Y::r($mail['team2']));

					//Y::send(ADMIN_EMAIL, '[football] Прогноз '.$mail['name'] , 'Прогноз '.$mail['name'].' на игру '.$mail['team1_name'].' - '.$mail['team2_name'].' '.Y::r($mail['team1']).' : '.Y::r($mail['team2']));
				}
			}
			else
			{
				if ($team_number == 1)
					$params = array('team1' => $value);
				else if ($team_number == 2)
					$params = array('team2' => $value);

				$res = Prognosis::update($id, $params);

				if ($res)
				{
					$mail = self::getMailInfo($user_id, $game_id);

					Log::write('Прогноз обновлен: '.$mail['name'].' на игру '.$mail['team1_name'].' - '.$mail['team2_name'].' '.Y::r($mail['team1']).' : '.Y::r($mail['team2']));

					Y::send(ADMIN_EMAIL, '[football] Прогноз '.$mail['name'] , 'Прогноз '.$mail['name'].' на игру '.$mail['team1_name'].' - '.$mail['team2_name'].' '.Y::r($mail['team1']).' : '.Y::r($mail['team2']));
				}
			}
		}

		return $res;
	}

	public static function getMailInfo($user_id, $game_id)
	{
		$filter = '';
		$result = array();

		$query = "
			SELECT DISTINCT
			U.name,
			T1.name as team1_name,
			T2.name as team2_name,
			P.team1,
			P.team2
			FROM tbl_prognosis P
			LEFT JOIN tbl_user U ON U.id = P.user_id
			LEFT JOIN tbl_game G ON G.id = P.game_id
			LEFT JOIN tbl_team T1 ON T1.id = G.team1_id
			LEFT JOIN tbl_team T2 ON T2.id = G.team2_id
			LEFT JOIN tbl_user2group UG ON UG.user_id = U.id
			WHERE (1=1) AND U.id = ".$user_id." AND G.id = ".$game_id;

		$res = Database::makeQuery($query);

		if ($res)
		{
			$r = array();
			while ($r = mysqli_fetch_array($res, mysqli_ASSOC))
				$result = $r;
		}

		return $result;
	}

	public static function exists($user_id, $game_id, $team_number)
	{
		$user_id = (int) $user_id;
		$game_id = (int) $game_id;

		$sql = "SELECT * FROM tbl_prognosis WHERE user_id = ".$user_id." AND game_id = ".$game_id;

		$res = Database::makeQuery($sql);

		$id = false;
		while ($result = mysqli_fetch_array($res))
		{
			$id = $result['id'];
			break;
		}

		return $id;
	}

	// public static function delete($id)
	// {
	// 	return Database::makeQuery("DELETE FROM match WHERE id = ".intval($id));
	// }
}

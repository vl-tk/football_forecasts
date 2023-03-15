<?php

class Game
{
	public static function getInfoFilteredByUser($user_id = null, $tournament_id=null)
	{
		$user_id = (int) $user_id;
		$user_id = ($user_id == 0) ? 1 : $user_id;

		$games = array();

		$query = "
			SELECT

			G.id,
			G.started_at,
			G.finished,
			G.result,
			G.penalty_result,
			G.playoff_round,

			T1.id as team1_id,
			T1.prefix as team1_prefix,
			T1.name as team1_name,
			T1.rank as team1_rank,
			T1.fifa_rank as team1_fifa_rank,
			T1.fifa_rating as team1_fifa_rating,
			P.team1 as team1_prognosis,

			T2.id as team2_id,
			T2.prefix as team2_prefix,
			T2.name as team2_name,
			T2.rank as team2_rank,
			T2.fifa_rank as team2_fifa_rank,
			T2.fifa_rating as team2_fifa_rating,
			P.team2 as team2_prognosis

			FROM tbl_game G
			INNER JOIN tbl_team T1 ON G.team1_id = T1.id
			INNER JOIN tbl_team T2 ON G.team2_id = T2.id
			LEFT JOIN tbl_prognosis P ON G.id = P.game_id AND P.user_id = ".$user_id."
			WHERE (1=1) AND G.tournament_id = ".$tournament_id."
			ORDER BY G.started_at ASC";

		$res = Database::makeQuery($query);

		if ($res)
		{
			while ($game = mysqli_fetch_array($res))
				$games[] = $game;
		}

		return $games;
	}

	public static function finish($id)
	{
		$id = (int) $id;

		$sql = "UPDATE tbl_game SET finished = 'Y' WHERE id = ".$id;

		return Database::makeQuery($sql);
	}

	public static function start($id)
	{
		$id = (int) $id;

		$sql = "UPDATE tbl_game SET finished = '' WHERE id = ".$id;

		return Database::makeQuery($sql);
	}

	public static function get($params = array())
	{
		if (array_key_exists("id", $params))
			$filter .= "AND ID = '".intval($params["id"])."'";

		$query = "
			SELECT
			G.id,
			T1.id as team1_id,
			T1.name as team1_name,
			P.team1 as team1_prognosis,
			T2.id as team2_id,
			T2.name as team2_name,
			P.team2 as team2_prognosis,
			G.started_at,
			G.finished,
			G.result
			FROM tbl_game G
			INNER JOIN tbl_team T1 ON G.team1_id = T1.id
			INNER JOIN tbl_team T2 ON G.team2_id = T2.id
			LEFT JOIN tbl_prognosis P ON G.id = P.game_id AND P.user_id = ".$user_id."
			WHERE (1=1) ".$filter."
			ORDER BY G.started_at ASC";

		return Database::makeQuery($query);
	}

	// public static function add($params)
	// {
	// 	$keys = "";
	// 	$values = "";

	// 	foreach ($params as $key => $value)
	// 	{
	// 		$keys .= (strlen($keys) > 0) ? ", '".$key."'" : "'".$key."'";
	// 		$values .= (strlen($values) > 0) ? ", '".$value."'" : "'".$value."'";
	// 	}

	// 	return Database::makeQuery("INSERT INTO match (".$keys.") VALUES (".$values.")");
	// }

	public static function update($id, $params)
	{
		$id = intval($id);

		$sql = '';
		foreach ($params as $key => $value)
			$sql .= $key." = '".$value."'";

		return Database::makeQuery("UPDATE tbl_game SET ".$sql." WHERE id = ".$id);
	}

	// public static function delete($id)
	// {
	// 	return Database::makeQuery("DELETE FROM match WHERE id = ".intval($id));
	// }


	// makes game data in the following format
	// $game['1/8'][0]['date']
	// $game['1/8'][0]['team1_id']
	// $game['1/8'][0]['team1_name']
	// $game['1/8'][0]['team1_result']
	// $game['1/8'][0]['team1_penalty_result']
	// $game['1/8'][0]['team2_id']
	// $game['1/8'][0]['team2_name']
	// $game['1/8'][0]['team2_result']
	// $game['1/8'][0]['team2_penalty_result']
	public static function parsePlayOffGameData($games)
	{
		$result = array();

		foreach ($games as $game)
		{
			if (strlen($game['playoff_round']) != 0)
				$result[$game['playoff_round']][] = $game;
		}

		return $result;
	}
}

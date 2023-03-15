<?php

class Tournament
{
	public static function getIDByPrefix($prefix)
	{
		$tournament_id = null;

		$tournaments = array();

		$query = "SELECT T.prefix FROM tbl_tournament T";

		$res = Database::makeQuery($query);

		if ($res)
		{
			while ($tournament = mysqli_fetch_array($res))
				$tournaments[] = $tournament['prefix'];
		}

		if ($tournaments && in_array($prefix, $tournaments)) {

			$query = sprintf("
				SELECT T.id
				FROM tbl_tournament T
				WHERE (1=1)
				AND T.prefix = '%s'",
				$prefix
			);

			$res = Database::makeQuery($query);

			if ($res)
			{
				if ($tournament = mysqli_fetch_array($res))
					$tournament_id = $tournament['id'];
			}
		}

		return $tournament_id;
	}

}

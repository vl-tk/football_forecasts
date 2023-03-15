<?php

class Computer
{
	const TOTAL = 5;

	public static function getPoints($r1, $r2, $p1, $p2)
	{
		$points = 0;

		if (($r1 == $p1) && ($r2 == $p2))
			$points = 4;
		else if (($r1 - $r2) == ($p1 - $p2))
			$points = 3;
		else if (Y::same_result($r1, $r2, $p1, $p2) && (abs(($r1 - $r2) - ($p1 - $p2)) < 1.5))
			$points = 2;
		else if (Y::same_result($r1, $r2, $p1, $p2) && (abs(($r1 - $r2) - ($p1 - $p2)) > 1.5))
			$points = 1;

		return $points;
	}

	public static function getPointsHTML($p1, $p2, $result, $game_finished)
	{
		if (strlen($result) <= 0 || $game_finished != 'Y')
			return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		$p1 = (int) $p1;
		$p2 = (int) $p2;

		$results = explode(':', $result);
		$r1 = (int) $results[0];
		$r2 = (int) $results[1];

		$points = self::getPoints($r1, $r2, $p1, $p2);

		$title = "";

		return "&nbsp;&nbsp;-&nbsp;&nbsp;<span title=\"".$title."\"><strong>".$points."</strong></span>";
	}


	// too easy to win by using 1-1. deprecated
	public static function getPointsByDiff($p1, $p2, $result)
	{
		$results = explode(':', $result);

		$result1 = (int) $results[0];
		$result2 = (int) $results[1];

		$diff1 = abs($result1 - $p1);
		$diff2 = abs($result2 - $p2);

		return self::TOTAL - ($diff1 + $diff2);
	}

	// too easy to win by using 1-1. deprecated
	public static function getPointsByDiffHTML($team1_prognosis, $team2_prognosis, $result, $game_finished)
	{
		if (strlen($result) <= 0 || $game_finished != 'Y')
			return '';


		$team1_prognosis = (int) $team1_prognosis;
		$team2_prognosis = (int) $team2_prognosis;

		$points = self::getPointsByDiff($team1_prognosis, $team2_prognosis, $result);

		// title

		$results = explode(':', $result);
		$result1 = (int) $results[0];
		$result2 = (int) $results[1];

		$title = "= ".self::TOTAL." - (|".$team1_prognosis." - ".$result1."| + |".$team2_prognosis." - ".$result2."|)";

		// title

		return "&nbsp;&nbsp;<span title=\"".$title."\">[&nbsp;".$points." ".Y::pl($points, 'очко', 'очка', 'очков')."&nbsp;]</span>";
	}
}
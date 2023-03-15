<?php

class Calc
{
	const TOTAL = 5;

	/**
	 * Calculates the number of points
	 * @param  [int] $r1  result for the first team
	 * @param  [int] $r2  result for the second team
	 * @param  [int] $pr1 penalty for the first team
	 * @param  [int] $pr2 penalty for the second team
	 * @param  [int] $p1  prognosis for the first team
	 * @param  [int] $p2  prognosis for the second team
	 * @return [int]      number of points
	 */
	public static function getPoints($r1, $r2, $pr1, $pr2, $p1, $p2)
	{
		$points = 0;

		if ($r1 == $r2 && $pr1 != 0 && $pr2 != 0) // if penalty result (pr)
		{
			if (($r1 == $p1) && ($r2 == $p2)) // exact draw result
				$points = 4;
			else if (($r1 - $r2) == ($p1 - $p2))
				$points = 3;
			// old logic with differentiating of draws scores
			// else if (Y::same_result($r1, $r2, $p1, $p2) && (abs($r1 - $p1) < 1.5)) // draw
			// 	$points = 2;
			// else if (Y::same_result($r1, $r2, $p1, $p2) && (abs($r1 - $p1) > 1.5)) // draw
			// 	$points = 1;
			else if (Y::same_result($pr1, $pr2, $p1, $p2)) // penalty winner
				$points = 1;
		}
		else
		{
			if (($r1 == $p1) && ($r2 == $p2))
				$points = 4;
			else if (($r1 - $r2) == ($p1 - $p2))
				$points = 3;
			else if (Y::same_result($r1, $r2, $p1, $p2) && (abs(($r1 - $r2) - ($p1 - $p2)) < 1.5))
				$points = 2;
			else if (Y::same_result($r1, $r2, $p1, $p2) && (abs(($r1 - $r2) - ($p1 - $p2)) > 1.5))
				$points = 1;
		}

		return $points;
	}

	public static function getPointsHTML($p1, $p2, $game_finished, $result, $penalty_result)
	{
		if (strlen($result) <= 0 || $game_finished != 'Y')
			return "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

		$p1 = (int) $p1;
		$p2 = (int) $p2;

		$results = explode(':', $result);
		$r1 = (int) $results[0];
		$r2 = (int) $results[1];

		$penalty_results = explode(':', $penalty_result);
		$pr1 = (int) $penalty_results[0];
		$pr2 = (int) $penalty_results[1];

		$points = self::getPoints($r1, $r2, $pr1, $pr2, $p1, $p2);

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

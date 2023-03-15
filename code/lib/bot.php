<?php

class Bot
{
	public static $bots = array(6,7,17);

	/**
	 * weighted_random_simple()
	 * Pick a random item based on weights.
	 *
	 * @param array $values Array of elements to choose from
	 * @param array $weights An array of weights. Weight must be a positive number.
	 * @return mixed Selected element.
	 */
	function weighted_random_simple($values, $weights)
	{
		$count = count($values);
		$i = 0;
		$n = 0;
		$num = mt_rand(0, array_sum($weights));
		while($i < $count){
			$n += $weights[$i];
			if($n >= $num){
				break;
			}
			$i++;
		}
		return $values[$i];
	}

	// get $team1_rank, $team2_rank

	// http://globalscience.ru/article/read/17328/

	// 100 90 60 33
	// 100/85 = смещает вероятность в сторону большей разницы мячей - на столько

	// нормальное распределение
	// -5 - 0.005
	// -4 - 0.01
	// -3 - 0.03
	// -2 - 0.08
	// -1 - 0.12
	// 0 - 0.27
	// 1 - 0.2
	// 2 - 0.15
	// 3 - 0.085
	// 4 - 0.03
	// 5 - 0.01
	// 6 - 0.005

	// -5 - 0.005
	// -4 - 0.01
	// -3 - 0.03
	// -2 - 0.08
	// -1 - 0.12
	// 0 - 0.27
	// 1 - 0.2
	// 2 - 0.15
	// 3 - 0.085
	// 4 - 0.03
	// 5 - 0.01
	// 6 - 0.005

	// 6 - 0.005
	// 5 - 0.015
	// -5 - 0.005
	// 4 - 0.04
	// -4 - 0.015
	// 3 - 0.085
	// -3 - 0.03
	// -2 - 0.08
	// 2 - 0.16
	// -1 - 0.125
	// 1 - 0.2
	// 0 - 0.275

	// https://ru.wikipedia.org/wiki/%D7%E5%EC%EF%E8%EE%ED%E0%F2_%EC%E8%F0%E0_%EF%EE_%F4%F3%F2%E1%EE%EB%F3_2010#.D0.9F.D0.BB.D0.B5.D0.B9-.D0.BE.D1.84.D1.84
	// сила команды увеличивает вероятность ее выигрыша;

	// Рассмотрим все возможные исходы матчей от 0 до 5. Итого 6*6=36. Вероятность попадания "пальцем в небо" - 1/36.
	// Это около 3%. А если будем прозорливее, то посчитаем исходы во всех доступных турнирах и получим,
	// что наиболее вероятные исходы это 1:0, 0:1; 2:0, 2:1, 2:2, 0:2, 1:2, которые в сумме составят, наверное, более 90% исходов. Выровняв статистически можно легко довести вероятность до 30%.

	//http://w-shadow.com/blog/2008/12/10/fast-weighted-random-choice-in-php/

	public static function getPrognosis($team1_rank, $team2_rank)
	{
		$team1_rank = (int) $team1_rank;
		$team2_rank = (int) $team2_rank;

		$d = $team1_rank - $team2_rank;

		if ($d > 30)
		{
			$options = array(
				array(3,1),
				array(3,0),
				array(2,0),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d > 20 && $d <= 30)
		{
			$options = array(
				array(3,1),
				array(2,0),
				array(2,1),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d > 5 && $d <= 20)
		{
			$options = array(
				array(2,1),
				array(1,0),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d > -5 && $d <= 5)
		{
			$options = array(
				array(0,0),
				array(0,1),
				array(1,0),
				array(1,1),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d < -5 && $d >= -20)
		{
			$options = array(
				array(1,2),
				array(0,1),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d < -20 && $d >= -30)
		{
			$options = array(
				array(1,3),
				array(0,2),
				array(1,2),
			);

			$k = array_rand($options);
			$res = $options[$k];
		}
		else if ($d < -30)
		{
			$options = array(
				array(1,3),
				array(0,3),
				array(0,2)
			);

			$k = array_rand($options);
			$res = $options[$k];
		}

		return $res;
	}

	public static function getSimplePrognosis()
	{
		$options = array(
			array(0,1),
			array(1,0),
			array(1,1),
			array(2,0),
			array(0,2),
			array(2,1),
			array(1,2),
			array(0,0)
		);

		$k = array_rand($options);
		return $options[$k];
	}
}

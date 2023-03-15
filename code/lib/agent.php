<?php

class Agent
{
	public static function checkAgents()
	{
		$sql = "UPDATE tbl_game SET finished = 'Y' WHERE convert_tz(started_at, concat(replace(timezone,'UTC',''),':00'), '+02:00') < NOW()";

		// $started_at="2012-01-01 12:00:00";
		// $timezone="UTC";
		// $toTimezone="Europe/Moscow";
		// echo date_create($started_at, new DateTimeZone($fromTimeZone))->setTimezone(new DateTimeZone($toTimezone))->format("Y-m-d H:i:s");

		$res = Database::makeQuery($sql);

		return true;
	}
}

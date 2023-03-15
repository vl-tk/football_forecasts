<?php

class UserGroup
{
	public static function add($user_id, $group_id)
	{
		$query = "INSERT INTO `tbl_user2group` (`user_id`, `group_id`) VALUES ('".$user_id."', '".$group_id."')";

		return Database::makeQuery($query);
	}

	public static function exists($user_id, $group_id)
	{
		$user_id = (int) $user_id;
		$group_id = (int) $group_id;

		$query = "SELECT * FROM `tbl_user2group` UG WHERE (1=1) AND UG.user_id = ".$user_id." AND UG.group_id = ".$group_id;

		$res = Database::makeQuery($query);

		$connections = array();

		if ($res) {
			while ($c = mysql_fetch_array($res))
				$connections[] = $c;
		}

		if (count($connections) > 0)
			return true;

		return false;
	}

	public static function deleteGroupUsers($group_id)
	{
		$query = "DELETE FROM `tbl_user2group` WHERE group_id = ".$group_id;

		return Database::makeQuery($query);
	}

	public static function getGroupUsers($group_id, $owner_id) {

		$user_id = (int) $user_id;
		$group_id = (int) $group_id;

		$query = "
		SELECT DISTINCT
			UG.*, U.name
		FROM `tbl_user2group` UG
		LEFT JOIN `tbl_user` U ON U.id = UG.user_id
		WHERE (1=1)
		AND UG.group_id = ".$group_id." AND UG.user_id <> ".$owner_id;

		$res = Database::makeQuery($query);

		$users = array();

		if ($res) {
			while ($u = mysql_fetch_array($res))
				$users[] = $u;
		}

		return $users;
	}

}

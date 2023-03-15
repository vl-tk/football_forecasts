<?php

class Group
{
	public static function get($params = array())
	{
		if (array_key_exists("id", $params))
			$filter .= "AND id = '".intval($params["id"])."'";

		if (array_key_exists("user_id", $params))
			$filter .= "AND user_id = '".intval($params["user_id"])."'";

		if (array_key_exists("group_name", $params)) {

			$res = Database::makeQuery("SELECT G.name FROM tbl_group G");

			if ($res) {

				$whitelist = array();

				while ($g = mysql_fetch_array($res))
					$whitelist[] = $g['name'];

				if (is_array($whitelist) && in_array($params['group_name'], $whitelist))
					$filter .= "AND name = '".Database::escape($params["group_name"])."'";
				else
					return array();
			}
			else
				return array();
		}

		$order = "";

		$query = "SELECT * FROM tbl_group G WHERE (1=1) ".$filter.$order;

		$groups = array();

		$res = Database::makeQuery($query);

		if ($res) {
			while ($g = mysql_fetch_array($res))
				$groups[] = $g;
		}

		return $groups;
	}

	public static function add($params)
	{
		$keys = "";
		$values = "";

		foreach ($params as $key => $value)
		{
			$keys .= (strlen($keys) > 0) ? ", `".$key."`" : "`".$key."`";
			$values .= (strlen($values) > 0) ? ", '".$value."'" : "'".$value."'";
		}

		$query = "INSERT INTO `tbl_group` (".$keys.") VALUES (".$values.")";

		$res = Database::makeQuery($query);

		if ($res) {
			$id = mysql_insert_id();
			return $id;
		}

		return false;
	}

	public static function create($params)
	{
		$name = $params['name'];

		# name
		if (!preg_match('/^[a-z0-9_]+$/', $name))
			return array(
				'status'=>'error',
				'error'=>'Неверное название: используйте символы a-z 0-9 и подчеркивание _ без пробелов'
			);

		# max count
		$groups = Group::get(array('user_id' => intval($params['user_id'])));
		if (count($groups) >= MAX_GROUPS_TO_OWN)
			return array(
				'status'=>'error',
				'error'=>'Нельзя создать больше '.MAX_GROUPS_TO_OWN.' групп'
			);

		$code = substr(md5(uniqid(rand(), true)), 0, 8);

		$params['code'] = $code;

		$group_id = self::add($params);

		$res = UserGroup::add($params['user_id'], $group_id);

		if ($res)
			return array('status'=>'ok');

		return array(
			'status'=>'error',
			'error'=>'Неизвестная ошибка'
		);
	}

	public static function join($user_id, $group_name, $group_code)
	{
		$user_id = (int) $user_id;

		if ($user_id) {

			$groups = self::get(array('group_name' => $group_name));

			if (is_array($groups) && count($groups) > 0) {

				$group = $groups[0];

				if (array_key_exists('code', $group) && $group['code'] == $group_code) {

					if (UserGroup::exists($user_id, $group['id'])) {
						$error = 'Вы уже в группе.';
					}
					else {
						$res = UserGroup::add($user_id, $group['id']);
						if ($res)
							return $group;
						else
							$error = 'Какая-то проблема';
					}
				}
				else {
					$error = 'Неверный код.';
				}
			}
			else {
				$error = 'Группа не найдена';
			}
		}

		return array('error'=>$error);
	}

	public static function delete($params)
	{
		$user_id = intval($params['user_id']);
		$group_id = intval($params['group_id']);

		UserGroup::deleteGroupUsers($group_id);

		$query = "DELETE FROM `tbl_group` WHERE id = ".intval($group_id);

		$res = Database::makeQuery($query);

		return true;
	}
}

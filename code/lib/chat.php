<?php

class Chat
{
	public static function getAll($params = array())
	{
		$filter = '';

		$query = "
			SELECT
				C.*, U.name as user_name
			FROM tbl_chat C
			INNER JOIN tbl_user U ON U.id = C.user_id
			WHERE (1=1) ".$filter."
			ORDER BY id DESC";

		$res = Database::makeQuery($query);

		$messages = array();

		if ($res)
		{
			while ($msg = mysqli_fetch_array($res))
				$messages[] = $msg;
		}

		return $messages;
	}

	public static function add($params)
	{
		$keys = "";
		$values = "";

		foreach ($params as $key => $value)
		{
			$keys .= (strlen($keys) > 0) ? ", ".$key."" : "".$key."";
			$values .= (strlen($values) > 0) ? ", '".$value."'" : "'".$value."'";
		}

		$sql = "INSERT INTO tbl_chat (".$keys.", created_at) VALUES (".$values.", NOW())";

		return Database::makeQuery($sql);
	}

}

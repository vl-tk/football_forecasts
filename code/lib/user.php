<?php

class User
{
	private $id = NULL;
	private $login = NULL;
	public static $members = array(1, 2, 3, 14, 16, 22);  // TODO - remove & use config.php

	public function __construct()
	{
	}

	public static function get($params = array())
	{
		if (array_key_exists("id", $params))
			$filter .= "AND id = '".$params["id"]."'";

		if (array_key_exists("login", $params)) {
			$filter .= "AND login = '".Database::escape($params["login"])."'";
		}

		$order = "";

		$res = Database::makeQuery("SELECT * FROM tbl_user U WHERE (1=1) ".$filter.$order);

		$users = array();

		if ($res) {
			while ($u = mysqli_fetch_array($res))
				$users[] = $u;
		}

		return $users;
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

		$query = "INSERT INTO `tbl_user` (".$keys.") VALUES (".$values.")";

		$res = Database::makeQuery($query);

		if ($res) {
			$id = mysqli_insert_id();
			return $id;
		}

		return false;
	}

	public static function getGroups($user_id)
	{
		$groups = array();

		$user_id = (int) $user_id;

		$query = "
		SELECT
			G.id, G.name, G.comment
		FROM tbl_group G
		LEFT JOIN tbl_user2group UG ON UG.group_id = G.id
		WHERE (1=1)
		AND UG.user_id = ".$user_id."
		ORDER BY G.id ASC
		";

		$res = Database::makeQuery($query);

		if ($res) {
			while ($g = mysqli_fetch_array($res))
				$groups[] = $g;
		}

		return $groups;
	}

	public static function getOwnedGroups($user_id)
	{
		$groups = array();

		$user_id = (int) $user_id;

		$query = "
		SELECT DISTINCT
			G.id, G.name, G.comment, G.code, COUNT(U.id) as user_count
		FROM tbl_group G
		LEFT JOIN tbl_user2group UG ON UG.group_id = G.id
                LEFT JOIN tbl_user U ON U.id = UG.user_id
		WHERE (1=1)
		AND G.user_id = ".$user_id."
                GROUP BY G.id
		ORDER BY G.id ASC
		";

		$res = Database::makeQuery($query);

		if ($res) {
			while ($g = mysqli_fetch_array($res))
				$groups[] = $g;
		}

		return $groups;
	}

	public static function update($id, $params)
	{
		$str = '';
		foreach ($params as $key => $value)
		{
			if (strlen($str) > 0)
				$str .= ', ';

			$str .= "".$key." = '".Database::escape($value)."'";
		}

		$query = "UPDATE `tbl_user` SET ".$str." WHERE id = ".intval($id);

		return Database::makeQuery($query);
	}

	public static function delete($id)
	{
		return Database::makeQuery("DELETE FROM tbl_user WHERE id = ".intval($id));
	}

	public function isAuthorized()
	{
		if (isset($_SESSION["sessid"]) && strlen($_SESSION["sessid"]) > 0)
			return true;
		else
			return false;
	}

	public function loginByHash($string)
	{
	    $data = explode(',', $string);

	    $login = $data[0];
	    $hash = $data[1];

	    if (md5($login.WHATEVER) === $hash)
	    {
			$escaped_login = Database::escape($login);
			$res = Database::makeQuery("SELECT * FROM tbl_user U WHERE login='".$escaped_login."'");

			while ($user = mysqli_fetch_array($res))
			{
				$this->id = $user['id'];
				$this->login = $user['login'];

				$_SESSION['id'] = $user['id'];
				$_SESSION['login'] = $user['login'];
				$_SESSION['sessid'] = $user['login'].','.md5($user['login'].WHATEVER);

				return true;
			}
	    }

        return false;
	}

	public static function generateSalt() {

		$salt = '';
		$length = 8; // длина соли (от 5 до 10 символов)

		$str = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM<>?:!@#$%^&*()_0123456789";

		for($i=0; $i<$length; $i++) {
			$randIndex = rand(0,strlen($str));
			$salt .= $str[$randIndex];
		}

		return $salt;
	}

	public function register($login, $name, $password, $password2)
	{
		Log::write('Попытка регистрации: login:'.$login.' name:'.$name.' pw:'.$password. ' pw2:'.$password2);

		if (strlen($login) <= 0)
		{
			$error[] = "No login specified";
			return $error;
		}

		if (!preg_match('/^[a-z0-9_]{3,16}$/', $login))
		{
			$error[] = "The login must consist of the characters a-z 0-9 _ and be 3-8 characters long";
			return $error;
		}

		$users = User::get(array('login'=>$login));

		if (count($users) > 0)
		{
			$error[] = "This login is busy. Use another.";
			return $error;
		}

		if (strlen($password) <= 5)
		{
			$error[] = "Password is too short";
			return $error;
		}

		if (trim($password) != trim($password2))
		{
			$error[] = "Passwords don't match";
			return $error;
		}

		/*
		# max count
		$groups = Group::get(array('user_id' => intval($params['user_id'])));
		if (count($groups) >= MAX_GROUPS_TO_OWN)
			return array(
				'status'=>'error',
				'error'=>'You cant create more than '.MAX_GROUPS_TO_OWN.' groups'
			);
		*/

		if (strlen($name) <= 0)
			$name = $login;

		$salt = self::generateSalt();
		$new_password = md5(md5($password).$salt);

		$user_id = self::add(array(
			'login' => $login,
			'password' => $new_password,
			'salt' => $salt,
			'name' => $name
		));

		if ($user_id) {
			UserGroup::add($user_id, 1);
			return true;
		}
		else {
			$error[] = "Неизвестная ошибка";
			return $error;
		}
	}

	public static function convertpw() {
		$users = User::get();

		foreach ($users as $id => $user) {
			$salt = self::generateSalt();
			$new_password = md5(md5($user['password']).$salt);

			User::update($user['id'], array('salt'=>$salt, 'password'=>$new_password));
		}
	}

	public function authorize($login, $password, $remember = '')
	{
		Log::write('Попытка авторизации: login:'.$login.' pw:'.$password);

		if (strlen($login) > 0 && strlen($password) > 0)
		{
			$escaped_login = Database::escape($login);
			$res = Database::makeQuery("SELECT * FROM tbl_user U WHERE login='".$escaped_login."'");

			while ($user = mysqli_fetch_array($res))
			{
				$hash = md5(md5($password).$user['salt']);

				/*
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/dmp.txt', 'a');
				fwrite($fp, print_r( "entered: ".$password ,true));
				fwrite($fp, print_r( "\n" ,true));
				fwrite($fp, print_r( "db: ".$user['password'] ,true));
				fwrite($fp, print_r( "\n" ,true));
				fwrite($fp, print_r( "calculated hash: ".$hash ,true));
				fwrite($fp, print_r( "\n--------".Date("d-M H:i:s")."-------\n" ,true));
				fclose($fp);
				*/

				// if (trim($password) === trim($user['password'])) - OLD
				if ($hash == $user['password'])
				{
					$this->id = $user['id'];
					$this->login = $user["login"];

					$_SESSION['id'] = $user['id'];
					$_SESSION['login'] = $user['login'];
					$_SESSION['sessid'] = $user['login'].','.md5($user['login'].WHATEVER);

					if ($remember === 'yes')
						setcookie('sessid', $user['login'].','.md5($user['login'].WHATEVER), time() + 60*60*24*30*60);

					Log::write('Authorization successful: User '.$this->login);

					return $this->id;
				}
				else
				{
					$error[] = "Wrong password";
					return $error;
				}
			}

			$error[] = "User not found";
			return $error;
		}
		else
		{
			$error[] = "No login or password";
			return $error;
		}
	}

	public function unauthorize()
	{
		unset($_SESSION['id']);
		unset($_SESSION['login']);
		unset($_SESSION['sessid']);

		setcookie('sessid', '', 0);

		$this->id = NULL;
		$this->login = NULL;
	}

	public function getLogin()
	{
		if (isset($_SESSION['login']))
			return $_SESSION['login'];
		else
			return $this->login;
	}

	public function getId()
	{
		if (isset($_SESSION["id"]))
			return $_SESSION["id"];
		else
			return $this->id;
	}

	public function isAdmin()
	{
		if (isset($_SESSION["id"]) && ($_SESSION["id"] == ADMIN_USER_ID))
			return true;
		else
			return false;
	}

	public function isManager()
	{
		$managers = unserialize(MANAGERS);

		if (isset($_SESSION["id"]) && (in_array($_SESSION["id"], $managers)))
			return true;
		else
			return false;
	}

	public function isTeamMember()
	{
		$team_members = unserialize(TEAM_MEMBERS);

		if (isset($_SESSION["id"]) && in_array($_SESSION["id"], $team_members))
			return true;
		else
			return false;
	}

	public function isGroupOwner($group_id)
	{
		$group_id = (int) $group_id;

		if (isset($_SESSION["id"]))
			$user_id = $_SESSION["id"];
		else
			$user_id = $this->id;

		$my_groups = self::getOwnedGroups($user_id);

		foreach ($my_groups as $id => $group) {
			if ($group_id == $group['id'])
				return true;
		}

		return false;
	}
}

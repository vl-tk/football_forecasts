<?php

class Database
{
	private static $connection = NULL;

	public static function makeQuery($sql)
	{
		if (isset($_GET["sql"]) && $_GET["sql"] == "Y")
		{
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/lib/sql.txt', 'a');
			fwrite($fp, print_r( $sql ,true));
			fwrite($fp, print_r( "\n--------".Date("d-M H:i:s")."-------\n" ,true));
			fclose($fp);
		}

		if (self::getConnection())
		{
			return $result = mysqli_query(self::$connection, $sql);
		}
		else
			return false;
	}

	public static function dump()
    {
    	Log::write('Дамп базы');

        $tables = array();
		$result = self::makeQuery('SHOW TABLES');
		while ($row = mysqli_fetch_array($result))
			$tables[] = $row[0];

		// cycle through the tables
		foreach ($tables as $table)
		{
			$result = self::makeQuery("SELECT * FROM `$table`");
			$num_fields = mysqli_num_fields($result);
			$num_rows = mysqli_num_rows($result);

			$return .= "--\n-- Structure for the table $table\n--\n\n";
			$return .= "DROP TABLE IF EXISTS `$table`;";
			$row2 = mysqli_fetch_array(self::makeQuery("SHOW CREATE TABLE `$table`"));
			$return .= "\n\n" . $row2 [1] . ";\n\n";

			if ($num_rows > 0)
				$return .= "--\n-- Data dump for the table $table\n--\n\n";

			$i = 0;

			while ($row = mysqli_fetch_array($result))
			{
				if ($i == 0)
					$return .= "INSERT INTO `$table` VALUES\n";

				$i++;

				for($j = 0; $j < $num_fields; $j ++)
				{
					if ($j == 0)
						$return .= '(';

					$row [$j] = addslashes($row[$j]);
					$row [$j] = mysqli_real_escape_string($row [$j]);

					if (isset($row[$j]))
						$return .= '"'.$row[$j].'"';
					else
						$return .= '""';

					if ($j < ($num_fields - 1))
						$return .= ',';
				}

				$return .= "), \n";
			}

			$return = substr($return, 0, -3);
			$return .= "; \n";
		}

		Log::write('Dump created');

		return $return;
	}

	public static function getConnection()
	{
		if (!is_null(self::$connection))
		{
			return self::$connection;
		}
		else
		{
			$dbhandle = mysqli_connect(HOST, USERNAME, PW);
                        mysqli_select_db($dbhandle, DB);
                        mysqli_query($dbhandle, "set names utf8");

			if (!$dbhandle)
			{
				return false;
			}
			else
			{
				self::$connection = $dbhandle;
				return self::$connection;
			}
		}
	}

	public function closeConnection()
	{
		self::$connection = NULL;
	}

    public function __clone()
    {
        trigger_error('Клонирование запрещено.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Десериализация запрещена.', E_USER_ERROR);
    }

	public static function escape($inp)
	{
	    if(is_array($inp))
	        return array_map(__METHOD__, $inp);

	    if(!empty($inp) && is_string($inp))
	        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);

	    return $inp;
	}
}


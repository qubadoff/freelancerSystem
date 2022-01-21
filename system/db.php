<?php

class DB
{
	static $pdo;

	/**
	 * @return PDO
	 */
	public static function query()
	{
		if (!static::$pdo) {
			$dsn = 'mysql:dbname=siyasat;host=localhost';
			$user = 'root';
			$password = '';
			try {
				$pdo = new PDO($dsn, $user, $password);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				static::$pdo = $pdo;
			} catch (PDOException $e) {
				echo "PDO error" . $e->getMessage();
				die();
			}
		}

		return static::$pdo;
	}
}

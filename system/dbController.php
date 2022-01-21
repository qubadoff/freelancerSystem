<?php
$dsn = 'mysql:dbname=admin_freelancer;host=localhost;charset=utf8';
$user = 'admin_freelancer';
$password = 'isi0055';

try
{
	$pdo = new PDO($dsn,$user,$password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	echo "PDO error".$e->getMessage();
	die();
}
?>

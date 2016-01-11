<?
$doc_root = join('/', array_slice(explode('/', __FILE__), 0, -7));
include($doc_root.'/config.php');
?>
<?php
    $database = $db_name;
    $mysql_user = $db_user;
    $mysql_password = $db_user_pass;
    $mysql_host = $db_host;
    $mysql_table_prefix = "sphider_";


//	error_reporting (E_ALL);

	
	function MySQLi_conn()
	{
		global $mysql_host, $mysql_user, $mysql_password,$database;
		$mysqli_conn = new mysqli($mysql_host, $mysql_user, $mysql_password,$database);

		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    die("MySQLi error!");
		}
		$mysqli_conn->query("SET NAMES 'utf8'");
		return $mysqli_conn;
	}

	$mysqli_conn=MySQLi_conn();

?>


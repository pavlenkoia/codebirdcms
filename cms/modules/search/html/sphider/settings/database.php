<?
include(dirname(__FILE__).'/../../../../../../config.php')
?>
<?php
	$database = $db_name;
	$mysql_user = $db_user;
	$mysql_password = $db_user_pass;
	$mysql_host = $db_host;
	$mysql_table_prefix = "sphider_";



	$success = mysql_pconnect ($mysql_host, $mysql_user, $mysql_password);
	if (!$success)
		die ("<b>Cannot connect to database, check if username, password and host are correct.</b>");
    $success = mysql_select_db ($database);
	if (!$success) {
		print "<b>Cannot choose database, check if database name is correct.";
		die();
	}

//mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';");
//mysql_query ("set character_set_client='utf8'");
//mysql_query ("set character_set_results='utf8'");
//mysql_query ("set collation_connection='utf8_general_ci'");
?>


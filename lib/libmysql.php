<?php
function connectToMySQL()
{
	$mysqlVars  = file('../var/mysql.conf');
	foreach($mysqlVars as $index => $line)
	{
		$line = strToLower(trim($line));
		$lineParts = explode("=", $line);
		if ($lineParts[0] == "host")
		{
			$mysql_host = $lineParts[1];
		} elseif ($lineParts[0] == "user")
		{
			$mysql_user = $lineParts[1];
		} elseif ($lineParts[0] == "password")
		{
			$mysql_pass = $lineParts[1];
		} elseif ($lineParts[0] == "database")
		{
			$mysql_db = $lineParts[1];
		}
	}
	
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die(mysql_error());
	mysql_select_db($mysql_db);
}
?>
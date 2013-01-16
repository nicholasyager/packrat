<?php

function loadAuth($recursionLevel)
{

	$loadAuthArray = file("../var/hash.conf");
	foreach($loadAuthArray as $index => $line)
	{
	        $lineArray = explode("=", $line);
	        $loadAuthArray[$index] = $lineArray[1];
	}
	$authKey = trim($loadAuthArray[0]);
	return($authKey);
}
?>

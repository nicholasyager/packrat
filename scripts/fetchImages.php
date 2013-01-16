<?php 

	require("../lib/libmysql.php");
	connectToMySQL();


	// Initiate all of the variables. Remeber to sanatize!

	$query = mysql_real_escape_string($_POST["query"]);
	$type = mysql_real_escape_string($_POST['type']);
	
	// Take the token, check to see it it's authentic, and then use it to look up the users ID number.

	$token = mysql_real_escape_string($_COOKIE['token']);
	$userQuery = "SELECT User_ID FROM users WHERE Password = '$token'";
	$userResource = mysql_query($userQuery);
	$userID = null;
	while($row = mysql_fetch_assoc($userResource)) {

		$userID = $row['User_ID'];

	}

	if ($query == "") {


	}

	if ($userID == null) {
	
		echo '{ "Status" : "0", Error : "Unknown User" }';
		die();

	}

	if ($type == "all") {

		$allQuery = "SELECT * FROM documents WHERE Owner='$userID'";
		$resource = mysql_query($allQuery) or die(mysql_error());

	} 

	if ($type == "query") {

		$queryString = str_replace(" ", "|", $query);

		$searchQuery = "SELECT * FROM documents WHERE Owner='$userID' AND Metadata REGEXP '$queryString' OR Tags REGEXP '$queryString' ";

		$resource = mysql_query($searchQuery) or die(mysql_error());
		

	}

	$JSONresult = "{";

	$initial = TRUE;

	while($row = mysql_fetch_assoc($resource)) {

		$Pages = json_decode($row['Pages'], TRUE);
		$dimensions = getimagesize("../uploads/".$Pages[1]);

		if ($initial == TRUE) {
			$initial = FALSE;
			$rowString = "";
		} else {
			$rowString = ", ";
		}
		$newWidth = 240;
		$newHeight = ( $dimensions[1] * $newWidth) / $dimensions[0];
		$rowString .= ' "' . $row['ID'] . '" : { "Width" : "'.$newWidth.'", "Height" : "'.$newHeight . '", "Metadata" : ' . $row['Metadata'] . ' , "Pages" : ' . $row['Pages'] . ' , "Tags" : ' . $row['Tags'] . ' }';

		$JSONresult .= $rowString;

	}
	$JSONresult .= "}";

	echo $JSONresult;
	`php ../uploads/resizeAll.php`;
	
?>


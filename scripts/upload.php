<?php

require("../lib/libauth.php");
require("../lib/libmysql.php");

connectToMySQL();

// Retreive variables
$Page = $_SERVER['HTTP_PAGE'];
$UploadID = $_SERVER['HTTP_UPLOADID'];
$Title = $_SERVER['HTTP_TITLE'];
$Category = $_SERVER['HTTP_CATEGORY'];
$Tags = $_SERVER['HTTP_TAGS'];
$Meta = $_SERVER['HTTP_META'];

$userToken = $_COOKIE['token'];

// Find the User_ID
$userQuery = "SELECT User_ID FROM users WHERE Password='$userToken'";
$userResource = mysql_query($userQuery) or die(mysql_error());
while ($row  = mysql_fetch_assoc($userResource)) {

	$User_ID = $row['User_ID'];

}


// Encode the tags
$Tag_Array = split(" ", $Tags);
$Tag_JSON = json_encode($Tag_Array);

// Encode the metadata
$Meta_Array = array();
$Meta_Blocks = split(",", $Meta);

// Add in information to find and remove preceeding whitespace.

foreach($Meta_Blocks as $characteristic) {

	$key_value_pairs = split(":", $characteristic);
	$key = $key_value_pairs[0];
	$value = $key_value_pairs[1];
	$Meta_Array[$key] = $value;

}
$Meta_Array['Title'] = $Title;
$Meta_Array['Category'] = $Category;
$Meta_JSON = json_encode($Meta_Array);

// Create a new filename
$newfn  = time();

// handle the file upload

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

$newfn = uniqid().".jpg";

if ($fn) {
	// AJAX Call
	file_put_contents(
		'../uploads/' . $newfn,
		file_get_contents('php://input')
	);
	$status = TRUE;
} else {

	// From submit
	$files = $_FILES['fileselect'];
	foreach ( $files['error'] as $id => $err) {
		if ($err == UPLOAD_ERR_OK) {

			$newfn = time();
			move_uploaded_file(
				$files['tmp_name'][$id],
				'uploads/'.$newfn
			);
			$status = TRUE;

		} else {

			$status = FALSE;

		}
	}
}


// Check to see if it is a new document

$docCheckQuery = "SELECT * FROM documents WHERE UploadID='$UploadID' FOR UPDATE";
$docCheckResource = mysql_query($docCheckQuery) or die(mysql_error());
$docCheckResult = mysql_num_rows($docCheckResource);
if ($docCheckResult == 0) {
	
	// There are no documents by that ID
	$page_array = array($Page => $newfn);
	$page_JSON = json_encode($page_array);
	$documentQuery = "INSERT INTO documents (UploadID,Metadata,Pages,Tags,Owner) VALUES ('$UploadID','$Meta_JSON','$page_JSON','$Tag_JSON','$User_ID')";

} else {

	// We got one!
	// Load the other version's page array.
	$pageQuery = "SELECT Pages FROM documents WHERE UploadID='$UploadID'";
	$pageResource = mysql_query($pageQuery) or die(mysql_error());
	while($rows = mysql_fetch_assoc($pageResource)) {

		$Pages_JSON = $rows['Pages'];

	}
	$Pages_Array = json_decode($Pages_JSON, TRUE);
	$Pages_Array[$Page] = $newfn;
	$Pages_JSON = json_encode($Pages_Array);
	$documentQuery = "UPDATE documents SET Pages='$Pages_JSON' WHERE UploadID='$UploadID'";

}

// Submit that motherfucker!
mysql_query($documentQuery) or die(mysql_error());

echo '{"Status" : "Success"}';

?>

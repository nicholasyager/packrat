<?php

	require("../lib/libresize.php");

	$path = $_GET['url'];
	$width = $_GET['width'];

	// Check to see if the file exists

	if (file_exists("../uploads/thumbnails/$path") == false ) {

		$image = new SimpleImage;
		$image->load("../uploads/".$path);
		$image->resizeToWidth($width);
		$image->save("../uploads/thumbnails/$path");

	}
	header("Location: ../uploads/thumbnails/$path");

?>

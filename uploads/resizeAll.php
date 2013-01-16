<?php

	set_time_limit(0);
	function getFiletype($file)
	{

		$fileSplit = explode(".",$file);
		return($fileSplit[1]);

	}

	function splitName($file)
	{
		$fileSplit = explode('.',$file);
		return($fileSplit[0]);
	}

	$crawlerDir = __dir__;
	$outputDir = __DIR__."/resampled/";

	$acceptableEndings = Array("jpg", "jpeg", "png");

	$dirResource = opendir($crawlerDir);

	while( false !== ($fileName = readdir($dirResource)))
	{

		$correctLength = 14;

		$nameLength = strlen($fileName);
		if ($nameLength == $correctLength )
		{
		
			$fileType = getFiletype($fileName);

			$width = 240;
			if ($fileType == "jpg" || $fileType == "jpeg")
			{
				$headerContent = "jpeg";
			} elseif ($fileType == "png") {
		
				$headerContent = "png";

			}
			list($width_original, $height_original) = getimagesize($fileName);
			$ratio = $width_original / $width;
			$height = $height_original / $ratio;
			
			$image_p = imagecreatetruecolor($width, $height);
			
			if ($fileType == "jpg" || $fileType == "jpeg")
			{
				$image = imagecreatefromjpeg($fileName);
			
			} elseif ($fileType == "png") {
				$image = imagecreatefrompng($fileName);
			}

			$status = false;

			$status = imagecopyresampled($image_p, $image, 0,0,0,0,$width, $height,$width_original, $height_original);
			if ($fileType == "jpg" || $fileType == "jpeg")
			{
				$status = imagejpeg($image_p, "resampled/".$fileName);
			
			} elseif ($fileType == "png") {
				$status = imagepng($image_p, "resampled/". $fileName);
			}

			$conv_status = ($status) ? 'true' : 'false';
			
			echo $fileName.": ".$conv_status."<br>"; 
			imagedestroy($image_p);
			imagedestroy($image);
		}

	}

?>

<?php


$image = $_GET[id];
$rotate = $_GET[rotate];
$maxHeight = 500;
$maxWidth = 800;
if(!$image==NULL){
	$im = new Imagick($image);
	$im->pingImage($image);
	$im->readImage($image);

	$w = $im->getImageWidth();
	$h = $im->getImageHeight();
	$fitbyWidth = (($maxWidth/$w)<($maxHeight/$h)) ?true:false;
	if($fitbyWidth){
		if($w>$maxWidth){
			$im->thumbnailImage($maxWidth, 0, false);
		}
		else{
			$im->thumbnailImage($w, 0, false);
		}
	}
	else{
		if($h>$maxHeight){
			$im->thumbnailImage(0, $maxHeight, false);
		}
		else{
			$im->thumbnailImage($w, 0, false);
		}
	}
	if(!$rotate==NULL){
		if($rotate>0){
			for($i=0;$i<$rotate;$i++){
				$im->rotateImage(new ImagickPixel(), 90);
			}
		}
		else {
			for($i=0;$i>$rotate;$i--){
				$im->rotateImage(new ImagickPixel(), 270);
			}
		}
	}
	header("Content-type: image/jpg");
	echo $im->getImageBlob();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("gamefunctions.php");
session_start();?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">
<?php
if(!isLoggedIn()){
	header('Location: login.php');
	die();
}

include("skrbl_functions.php");
$myImages = glob("skrbs/*.jpg");
$rotate = $_GET[rotate];
$total = (count($myImages) - 1);

$imgID = setImgID($_GET[img_id], $total);
$image = $myImages[$imgID];

printTableHead("Image Stuffs!");
printNavRow($imgID);
printRotatorRow($imgID, $rotate, $image);
printImage($image, $rotate);
?>

</div>

</body>
</html>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("basefunctions.php");
session_start();?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">
<?php
	bounce();
	echo "Character name must be between 4 and 30 characters. 
		Strength is used for melee attacks and damage and stuff.
		Dex is used for ranged attacks and damage and stuff.
		Con is used for damage resistance and HP each level.
		Wisdom is used for magic attacks and damage and stuff.
		Charisma is used for interacting at the the shop, and maybe other things.
		Luck is used for all sorts of luck stuff.";
?>
</div>
</body>
</html>

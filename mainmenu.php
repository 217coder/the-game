<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("basefunctions.php");
include("gamefunctions.php");
session_start();?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">
<?php
	bounce();
	$charData = loadCharacter();
	if($charData == NULL){//player doesn't have a character yet
		header('Location: createcharacter.php');
		die();
	}

	$items = loadItems($charData[id]);
	$charData = applyItems($charData, $items);

	printNavBar();

	if($_GET[action]=="sleep"){
		$charData=rest($charData);
	}

	printStatbar($charData);
	echo '<table class="cent">
		<tr><td><a href="inventory.php">Manage Inventory</a></td></tr>';
//		<tr><td>Use Potion</td></tr>';
	$state=$charData['mode'];

	if($state == ("encounter"||"rest"))
		echo '<tr><td><a href="encounter.php">Go looking for trouble</a></td></tr>';
	if($state == "rest"){
		echo '<tr><td><a href="itemshop.php">Go to Item Shop</a></td></tr>
			<tr><td><a href="?action=sleep">Sleep</a></td></tr>';
	}
	if($state == "levelup")
		echo '<tr><td><a href="levelup.php">Level Up</a></td></tr>';
	echo '<tr><td><a href="killcharacter.php">Kill Character</a></td></tr>
		</table>';
?>
</div>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php
include("basefunctions.php");
include("gamefunctions.php");
session_start();
?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">
<?php
	bounce();
	$charData=loadCharacter();
	$mode = $charData['mode'];
	if($mode != "rest" && $mode!="encounter"){
		header('Location: mainmenu.php');
		die();
	}
	else if($mode == "rest"){
		updateFeild("characters","mode","encounter","id",$charData['id']);
	}
	else if($mode == "encounter"){
		echo "why not me?";
		updateFeild("characters","mode","rest","id",$charData['id']);
	}
	printNavBar();
	printStatbar($charData);
	echo '<table>
		<tr><td><a href="explore.php">Explore</a></td></tr>
		<tr><td><a href="'.$town.'">Go back to Town</td></tr>
		</table>';
?>
</div>
</body>
</html>

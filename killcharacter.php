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
	$charData=loadCharacter();
	if($charData == NULL){
		header('Location: main.php');
		die();
	}
	printNavBar();
	printStatbar($charData);
	$password = $_POST['password'];
	if($password!=NULL){
		$data = fetchRow($_SESSION['userid'],"id","users");
		$hash = hash('sha256', $data['salt'].hash('sha256',$password));
		if($hash ==  $data['password']){
			$tmp = fetchRow($charData['id'],"owner","enemies");
			deleteRow($tmp['id'] ,"enemies");
			deleteRow($charData['id'], "characters");
			header('Location: main.php');
			die();
		}
		else{
			echo "password did not match";
		}
	}
	echo 'Are you sure you want to Kill/Delete your character?';
	echo '<form name="killcharacter" action="killcharacter.php" method="post">
		Confirm Password:<input type="password" name="password" />
		<input type="submit" value="KILL" />';
?>
</div>
</body>
</html>

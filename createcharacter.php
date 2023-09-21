<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("basefunctions.php");
session_start();
include("gamefunctions.php");?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">
<?php
	bounce();
	printNavBar();
	$userid=$_SESSION['userid'];
	$name= mysql_real_escape_string($_POST['charname']);
	$str= abs(floor(mysql_real_escape_string($_POST['str'])));
	$dex= abs(floor(mysql_real_escape_string($_POST['dex'])));
	$con= abs(floor(mysql_real_escape_string($_POST['con'])));
	$wis= abs(floor(mysql_real_escape_string($_POST['wis'])));
	$cha= abs(floor(mysql_real_escape_string($_POST['cha'])));
	$luck= abs(floor(mysql_real_escape_string($_POST['luck'])));

	if(loadCharacter()){
		header('Location: mainmenu.php');
		die();
	}
	else{
		if($name!=NULL){
			$sum=$str+$dex+$con+$wis+$cha+$luck;
			if($sum>'10')
				echo 'You cant have a total score more than 10';
			else if($sum<'10')
				echo 'You cant have a total score less than 10';
			else if(strlen($name<'4'))
				echo 'Your username must be more than 4 characts';
			else if(isValueInTable($name, "name", "characters"))
				echo 'Your character name is already in use.';
			else if($sum=='10'){
				$date = date( 'Y-m-d H:i:s');
				$hp = 10+floor($con/2);
				$query="INSERT INTO characters (
					owner, create_date, name, hp, max_hp, str, dex, con, wis, cha, luck)
					VALUES (
					'$userid',
					'$date',
					'$name',
					'$hp', '$hp',
					'$str', '$dex', '$con',
					'$wis', '$cha', '$luck');";
				if(!mysql_query($query))
					die('OH shit!!! '.mysql_error());
				header('Location: mainmenu.php');
			}
		}
		echo '<form name="chargen" action="createcharacter.php" method="post">
		<div class="registration">
		<table>
		<tr><td>Name:</td><td><input type="text" name="charname" maxlength="30"/></td></tr>
		<tr><td>Strength:</td><td><input type="text" name="str" size="2"/></td></tr>
		<tr><td>Dexterity:</td><td><input type="text" name="dex" size="2"/></td></tr>
		<tr><td>Constitution:</td><td><input type="text" name="con" size="2"/></td></tr>
		<tr><td>Wisdom:</td><td><input type="text" name="wis" size="2"/></td></tr>
		<tr><td>Charisma:</td><td><input type="text" name="cha" size="2"/></td></tr>
		<tr><td>Luck:</td><td><input type="text" name="luck" size="2"/></td></tr>
		<tr><td>
		<a href="#" onClick="MyWindow=window.open(\'charhelp.php\',
			\'MyWindow0\',
			\'toolbar=no, menubar=no, status=no, width=300,height=300\'); return false;">
			Help!</a></td><td><input type="submit" value="GENERATE"/></td></tr>
		</form>
		</table>
		</div>';
	}

?>
</div>
</body>
</html>

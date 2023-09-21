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

	$player=loadCharacter();
	if($player[mode]!="levelup"){
		header('Location: mainmenu.php');
		die();
	}

	printNavBar();
	printStatBar($player);
	$str= abs(floor(mysql_real_escape_string($_POST['str'])));
	$dex= abs(floor(mysql_real_escape_string($_POST['dex'])));
	$con= abs(floor(mysql_real_escape_string($_POST['con'])));
	$wis= abs(floor(mysql_real_escape_string($_POST['wis'])));
	$cha= abs(floor(mysql_real_escape_string($_POST['cha'])));
	$luck= abs(floor(mysql_real_escape_string($_POST['luck'])));

	$sum=$str+$dex+$con+$wis+$cha+$luck;
	if($sum!=NULL){
		if($sum>'2')
			echo 'You cant gain more than 2';
		else if($sum<'2')
			echo 'You cant gain less than 2';
		else if($sum=='2'){
			$hp=$player[max_hp]+10+floor($player[con]/2);
			$str+=$player[str];
			$dex+=$player[dex];
			$con+=$player[con];
			$wis+=$player[wis];
			$cha+=$player[cha];
			$luck+=$player[luck];
			$query="UPDATE characters
				SET hp='$hp', max_hp='$hp',
				str='$str', dex='$dex', con='$con',
				wis='$wis', cha='$cha', luck='$luck'
				WHERE id='$player[id]';";
			if(!mysql_query($query))
				die('OH shit!!! '.mysql_error());
			updateField("characters","mode","rest","id",$player[id]);
			header('Location: mainmenu.php');
		}
	}
	echo '<form name="levelup" action="levelup.php" method="post">
	<div class="registration">
	<table>
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
		Help!</a></td><td><input type="submit" value="LEVEL UP"/></td></tr>
	</form>
	</table>
	</div>';


?>
</div>
</body>
</html>

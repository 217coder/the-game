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
	bounceAdmin();
	printNavBar();
	$name=$_POST['name'];
	$str=$_POST['str'];
	$dex=$_POST['dex'];
	$con=$_POST['con'];
	$wis=$_POST['wis'];
	$cha=$_POST['cha'];
	$luck=$_POST['luck'];
	$hp = $_POST['hp'];
	$xpgain = $_POST['xpgain'];
	$max_damage=$_POST['max_damage'];


	if($name!=NULL){
		$query="INSERT INTO item_base (
			name, type, str, dex, con, wis, cha, luck,
			hp, xpgain, max_damage)
			VALUES (
			'$name','$type',
			'$str', '$dex', '$con',
			'$wis', '$cha', '$luck',
			'$hp', '$xpgain', '$max_damage');";
		if(!mysql_query($query)){
			echo $query;
			die('OH shit!!! '.mysql_error());}
		else
			echo 'Added '.$name.' to set.';
	}
	echo '<form name="chargen" action="itemweapon.php" method="post">
		<div class="registration">
		<table>
		<tr><td>Name:</td><td><input type="text" name="name" maxlength="30"/></td></tr>
		<tr><td>Type:</td><td><input type="text" name="type"/></td></tr>
		<tr><td>Strength:</td><td><input type="text" name="str" size="2"/></td></tr>
		<tr><td>Dexterity:</td><td><input type="text" name="dex" size="2"/></td></tr>
		<tr><td>Constitution:</td><td><input type="text" name="con" size="2"/></td></tr>
		<tr><td>Wisdom:</td><td><input type="text" name="wis" size="2"/></td></tr>
		<tr><td>Charisma:</td><td><input type="text" name="cha" size="2"/></td></tr>
		<tr><td>Luck:</td><td><input type="text" name="luck" size="2"/></td></tr>
		<tr><td>HP:</td><td><input type="text" name="hp" size="2"/></td></tr>
		<tr><td>XPGAIN:</td><td><input type="text" name="xpgain" size="2"/></td></tr>
		<tr><td>MaxDamage:</td><td><input type="text" name="max_damage" size="2"/></td></tr>
		<tr><td></td><td><input type="submit" value="GENERATE"/></td></tr>
		</form>
		</table>
		</div>';

?>
</div>
</body>
</html>

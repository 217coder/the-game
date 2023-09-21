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
	$player = loadCharacter();
	if(!$player){
		header('Location: mainmenu.php');
		die();
	}

	printNavBar();

	$items = loadItems($player[id]);
	$mainFields = array("id", "name", "str", "dex", "con", "wis", "cha", "luck",
		"hp","max_hp", "xpgain", "max_damage", "Requirements");
	$reqFields = array("min_str", "min_dex", "min_con", "min_wis", "min_luck", "min_lvl");

	$equip = $_GET[equip];
	if($equip=="oneshot"){
		//drink potiono
		$player=drinkPotion(itemByID($items,$_POST[id]),$player);
		echo "consume!";
	}
	else if($equip){
		$item=itemByID($items,$_POST[id]);
		equipItem($item,$equip,$player);
	}

	printItemEquipBar($items, $player, "weapon");
	printItemTable($items, "weapon", array($player[weapon]));
	printItemEquipBar($items, $player, "tricket1");
	printItemEquipBar($items, $player, "tricket2");
	printItemTable($items, "trinket", array($player[trinket1], $player[trinket2]));
	printItemEquipBar($items, $player, "oneshot");
	printItemTable($items, "oneshot");



	//Equiped Weapon / Change
	//Weapons List
	//Equiped Trinket / Change
	//Trinket List
	//Potion List
?>
</div>
</body>
</html>

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
	$player=loadCharacter();
	if($player==NULL){
		header('Location: createcharacter.php');
		die();
	}
	$items = loadItems($player[id]);
	$shopItems = loadItems("0");
	$mode = $_GET[mode];

	if($_GET[item]!=NULL){
		if($mode=="sell"){
			$item=itemByID($items, $_GET[item]);
			echo "Sell item:".$item[name];
			$player = sellItem($item, $player);
			array_splice($items, array_search($item, $items), 1);
		}
		else if($mode=="buy"){
			$item=itemByID($shopItems, $_GET[item]);
			echo "Buy item:".$item[name];
			$player = buyItem($item, $player);
			array_splice($shopItems, array_search($item, $shopItems), 1);
		}
	}


	$player = applyItems($player, $items);

	printNavBar();
	printStatbar($player);

//	echo "PHP_SELF:".$_SERVER['PHP_SELF']."|querystring:".$_SERVER['QUERY_STRING'];

	echo '<table class="blocky"><tr>
		<td><a href="?mode=sell">Sell Items</a></td>
		<td><a href="?mode=buy">Buy Items</a><td>
		</tr></table>';
	if($mode=="buy"){
		//print shop list
		printItemTable($shopItems, "all", NULL, "buy");
	}
	else if($mode=="sell"){
		//print inventory
		printItemTable($items, "all", array($player[weapon],$player[trinket1],$player[trinket2]), "sell");
	}

?>
</div>
</body>
</html>

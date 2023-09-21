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
	$player=loadCharacter();
	$mode = $player['mode'];
	$attack = $_GET['attack'];
	$flee = $_GET['flee'];

	if(!$player){
		header('Location: mainmenu.php');
		die();
	}

	$items = loadItems($player[id]);
	$player = applyItems($player, $items);

	if($mode == "rest"){
		updateField("characters","mode","encounter","id",$player['id']);
		$player[mode]="encounter";
	}
	else if($mode =="levelup"){
		header('Location: levelup.php');
		die();
	}
	else if($mode != "encounter"){
		header('Location: mainmenu.php');
		die();
	}
	else{ //already in enounter mode
	}
	printNavBar();
	$enemy = fetchEnemy($player);
	if($attack||$flee){
		$player=incrementTurns($items,$player);
	}
	if($attack){
		//PLAYER ATTACK
		echo "You attack ".$enemy[name]." using ".$attack."!<br>";
		$enemy=characterAttack($player,$enemy,$attack);
		//ENEMY ATTACK
		if($enemy[isDead]){
			$player=victory($player,$enemy);
		}
		else{
			$player=enemyAttack($enemy, $player);
		}
	}
	//FLEE
	else if($flee){
		echo "You attempt to run<br>";
		$player=attemptFlee($player, $enemy);
		if($player[flee]){
			echo "You escape!<br>";
			$player[victory]=true;
		}
		else{
			echo "You weren't quick enough<br>";
			$player=enemyAttack($enemy, $player);
		}
	}
	//DEFAULT
	else{
		echo "You encounter ".$enemy['name'].". WHAT DO YOU DO?!";
	}
//	echo '<table class="cent">
//		<tr><td class="img"><img src="skrbs/turtleabe.jpg" height="300"></td></tr></table>';

/*	if($player[weapon]){
		printEnemyStats(itemByID($items, $player[weapon]));
	}
*/	printStatBar($player);
	printEnemyStats($enemy);
	echo '<table class="blocky">';
	echo '<tr><td><a href="inventory.php">Go to Items</a></td></tr>';
	if($player[mode]=="levelup"){
		echo '<tr><td><a href="levelup.php">Level Up</a></td></tr>';
	}
	else if($player[victory]){
		updateField("characters", "mode", "rest", "id", $player[id]);
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'">MORE</a></td></tr>';
	}
	else if($player[defeat]){
		updateField("characters", "mode", "rest", "id", $player[id]);
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'">Try looking again</a></td></tr>';
	}
	else{
		echo '<tr><td><a href="'.$_SERVER['PHP_SELF'].'?attack=melee">Attack with Melee</a></td></tr>
			<tr><td><a href="'.$_SERVER['PHP_SELF'].'?attack=ranged">Attack with Ranged</a></td></tr>
			<tr><td><a href="'.$_SERVER['PHP_SELF'].'?attack=magic">Attack with Magic</a></td></tr>
			<tr><td><a href="'.$_SERVER['PHP_SELF'].'?attack=charm">Attack with Charm</a></td></tr>
			<tr><td><a href="'.$_SERVER['PHP_SELF'].'?flee=1">Flee!</a></td></tr>';
	}
	echo '<tr><td><a href="'.$town.'">Go to main menu</td></tr></table>';

?>
</div>
</body>
</html>

<?php
$town = "mainmenu.php";

function printStatbar($data){
	echo '<table class="stats"><tr>
		<td>Name:</td><td>'.$data['name'].'</td>
		<td>State:</td><td>'.$data['mode'].'</td>
		<td>Lvl:</td><td>'.$data[level].'</td>
		<td>Gold:</td><td>'.$data['gold'].'</td>
		<td>XP:</td><td>'.$data['xp'].'/'.($data[level]*500).'</td>
		<td>HP:</td><td>'.$data['hp'].'/'.$data[max_hp].'</td>
		<td>Str:</td><td>'.$data['str'].'</td>
		<td>Dex:</td><td>'.$data['dex'].'</td>
		<td>Con:</td><td>'.$data['con'].'</td>
		<td>Wis:</td><td>'.$data['wis'].'</td>
		<td>Cha:</td><td>'.$data['cha'].'</td>
		<td>Luck:</td><td>'.$data['luck'].'</td>
		<td>KiaR:</td><td>'.$data[kills_in_a_row].'</td>
		<td>KillCount:</td><td>'.$data[kill_count].'</td>
		<td>xpgain:</td><td>'.$data[xpgain].'</td>
		</tr></table>';
}
function printEnemyStats($data){
	echo '<table class="stats"><tr>';
	while(list($key, $val) = each($data)){
		echo '<td>'.$key.':</td><td>'.$val.'</td>';
	}
	echo '<td>LVL:</td><td>'.determineEnemyLevel($data).'</td>';
	echo '</tr></table>';
}

function loadCharacter(){
	$userid=$_SESSION['userid'];
	$query = "SELECT *
		FROM characters
		WHERE owner = '$userid';";
	$data = mysql_query($query);
	if(mysql_num_rows($data)<1){
		return NULL;
	}
	$data = mysql_fetch_array($data, MYSQL_ASSOC);
	return $data;
}

function loadItems($id){
	$query = "SELECT * FROM items WHERE owner='$id';";
	$data = mysql_query($query);
	$items = array();
	while($row = mysql_fetch_assoc($data)){
		array_push($items,$row);
	}
	return $items;
}

function fetchEnemy($player){
	$query = "SELECT *
		FROM enemies
		WHERE owner='$player[id]';";
	$enemy = mysql_query($query);
	if(mysql_num_rows($enemy)<1){ //no enemy yet
		$enemy = generateCreature($player[level]);
		addEnemyToDB($enemy, $player[id]);
		return $enemy;
		//add to enemy list
	}
	$enemy = mysql_fetch_array($enemy, MYSQL_ASSOC);
	return $enemy;
}

function addEnemyToDB($e, $id){
	$name = $e['name'];
	$str = $e['str'];
	$dex = $e['dex'];
	$con = $e['con'];
	$wis = $e['wis'];
	$cha = $e['cha'];
	$hp = $e['hp'];
	$luck = $e['luck'];

	$query = "INSERT INTO enemies (name, str, dex, con, wis, cha, luck, hp, owner)
		VALUES ('$name','$str','$dex','$con','$wis','$cha',
		'$luck','$hp','$id');";
	mysql_query($query);
}

function addItemToDB($item, $id){
	$query = "INSERT INTO items (owner";
	$item[id]=0; //strip item id, as one will be generated as it is inserted
	while (list($key, $val) = each($item)){
		if($val){
			$query=$query.", ".$key;
		}
	}
	$query=$query.") VALUES ('".$id."'";
	reset($item); //reset array pointer back to begining.
	while (list($key, $val) = each($item)){
		if($val){
			$query=$query.", '".$val."'";
		}
	}
	$query=$query.");";

	if(!mysql_query($query)){
		die("OH SHIT ERROR:".mysql_error());
	}
}

function generateCreature($level){
	$cLevel = $level+6;
	while($cLevel > ($level+3)){
//		echo "blah?";
		$query = "SELECT * FROM creature_types;";
		$creature = fetchRandomRow($query, "creature_types");
		$query = "SELECT * FROM creature_classes;";
		$bonus = fetchRandomRow($query, "creature_classes");
		$creature = addBonuses($creature, $bonus);
		$cLevel = determineEnemyLevel($creature);
		$creature[hp]=$cLevel*(10+floor($creature[con]/2));
	}

	return $creature;
}

function generateItem($level){
	$itemLevel=$level+1;
	while($itemLevel > $level){
//		echo "we are the item gnomes, and we work all day<br>";
		$query = "SELECT * FROM item_base;";
		$item = fetchRandomRow($query, "item_base");
		$query = "SELECT * FROM item_postfix;";
		$bonus = fetchRandomRow($query, "item_postfix");
		$item = addBonuses($bonus, $item);

		//if potion, adjust health stat
		if($item[type]=="oneshot"){
			$item[hp]+=$item[max_hp];
			$item[max_hp]=0;
		}
		$itemLevel = determineItemLevel($item);
	}
	return $item;
}

function addBonuses($base, $bonus){
	while (list($key, $val) = each($bonus)){
		if($base[$key]!=NULL){
			if((filter_var($base[$key], FILTER_VALIDATE_INT))||($base[$key]=='0')){
				if(filter_var($val, FILTER_VALIDATE_INT)){
					$base[$key]+=$val;
				}
				else{
					//$base[$key]= $base[$key].' '.$val;//I think this is adding the funky space
				}
			}
			else{
				$base[$key]= $val.' '.$base[$key];
			}
		}
		else{
			$base[$key]=$bonus[$key];
		}
	}
	return $base;
}

function characterAttack($player, $enemy, $type){
	//CHARM
	if($type =="charm"){
		$success = charm("You", $player, $enemy);
		if($success){
			echo "You were able to charm ".$enemy[name]." and they walk away.<br>";
//			$player=victory($player,$enemy);
			$enemy[isDead]=true;
		}
		else{
			echo "You have failed to charm".$enemy[name]."<br>";
		}
	}
	//ATTACK
	else{
		if($type=="melee"){
			$stat = "str";
		}
		else if($type=="ranged"){
			$stat = "dex";
		}
		else if($type=="magic"){
			$stat = "wis";
		}
		else{
			die("Bug off".$type);
		}
		$pStat = $player[$stat];
		$enemyAC = determineAC($enemy[$stat],$enemy);
		$enemyDamgRes = determineDamgRes($enemy);
		$max_damage = $player[damage]+floor($pStat/2);

		$damage=attack("You", $pStat, $player[luck], $enemyAC, $enemyDamgRes, $max_damage);
		if($damage >= $enemy[hp]){ //kill enemy
			echo "You killed ".$enemy[name].". Good Job.<br>";
			$enemy[isDead]=true;
//			$player=victory($player,$enemy);
		}
		else{ //deal damage
			$enemy=dealDamage($damage, $enemy, "enemies");
		}
	}
	return $enemy;
}

function victory($player, $enemy){
	resetMob($enemy);
	$player=gainXp($enemy, $player);
	$player[victory]=true;
	$player=incrementKillCount($player);
	$reward=generateReward(determineEnemyLevel($enemy));
	echo "You got a ".$reward[item][name]." and ".$reward[gold]." gold.<br>";
	$player[gold] += $reward[gold];
	updateField("characters","gold",$player[gold],"id",$player[id]);
	if(tooManyItems($player[id])){
		echo "You have too many items, ".$reward[item][name]." will be sold in shop for ".itemValue($reward[item]).".";
		sellItem($reward[item], $player);
		//sell item to shop
	}
	else{
		addItemToDB($reward[item], $player[id]);
	}
//	printEnemyStats($reward[item]);
	return $player;
}

function determineAC($stat, $stats){
	$AC = floor($stat/2) + floor($stats[con]/2) + 10 + floor(rand(0,$stats[luck])/2);
	return $AC;
}

function determineDamgRes($stats){
	$damgRes = floor($stats[con]/2);
	return $damgRes;
}

function enemyAttack($enemy, $player){
	$stat = pickBestCombatStat($enemy);
	if($enemy[$stat]>$enemy[cha]){
		printEnemyAttack($stat, $enemy[name]);
		$pAC = determineAC($stat, $player);
		$pDamgRes = determineDamgRes($player);
		$max_damage = $enemy[damage]+floor($enemy[$stat]/2);
		$damage=attack("The ".$enemy[name], $enemy[$stat], $enemy[luck], $pAC, $pDamgRes, $max_damage);
		if($damage >= $player[hp]){ //kill player
			echo $enemy[name]." kills you. Bummer.<br>";
			$player=killPlayer($player);
			$player[defeat]=true;
			resetMob($enemy);
		}
		else if($damage>'0'){ //deal damage
			$player=dealDamage($damage, $player, "characters");
		}
		else{ //miss
		}
	}
	else{
		$success=charm($enemy[name], $enemy, $player);
		if($success){ //charm
			echo $enemy[name]." charmed the pants off you. ".$enemy[name]." slowly walks away in delight.<br>";
			$player=resetKillCount($player);
			$player[defeat]=true;
			resetMob($enemy);
		}
		else{
			echo $enemy[name]." failed to charm you.<br>";
		}
	}
	return $player;
}

function printEnemyAttack($stat, $name){
	if($stat=="str")
		$type="melee";
	else if($stat=="dex")
		$type="ranged";
	else
		$type="magic";
	echo "The ".$name." attacks you with ".$type.".<br>";
}

function pickBestCombatStat($data){
	if($data[str]>$data[dex]){
		if($data[str]>$data[wis]){
			$type= "str";
		}
		else{
			$type= "wis";
		}
	}
	else if($data[dex]>$data[wis]){
		$type= "dex";
	}
	else{
		$type= "wis";
	}
	return $type;
}

function attack($title, $cStat, $luck, $eAC, $eDamgRes, $combatDice){
	$roll = rand(1,20) + rand(0,floor($luck/3));
	echo $title." rolled a ".$roll.".<br>";
	if(($roll + $cStat)>$eAC){
		$damage = floor($cStat/2) + rand(1,$combatDice) - $eDamgRes;
		if($damage<0)
			$damage=0;
		echo $title." hit and did ".$damage." damage.<br>";
	}
	else {
		echo $title." missed<br>";
	}
	return $damage;
}

function charm($title, $c, $enemy){
	$cChar = $c[cha] + rand(0,floor($c[luck]/3));
	$eChar = $enemy[cha] + rand(0,floor($enemy[luck]/3));
	$roll = rand(1,20);
	echo $title." rolled a ".$roll.".<br>";
	if(($roll + floor($cCha/2)) > ($eChar+10) ){
		return true;
		//echo $title." charmed ".$enemy[name];
	}
	else{
		return false;
		//echo $title." failed to charm ".$enemy[name];
	}
}

function dealDamage($damage, $enemy, $table){
	if($damage){
		$enemy[hp]-=$damage;
		$hp = $enemy[hp];
		$id = $enemy[id];
		updateField($table, "hp", $hp, "id", $id);
	}
	return $enemy;
}

function killPlayer($player){
	$player=resetKillCount($player);
	$player=resetHP($player);
	return $player;
}

function rest($player){
	$player=resetHP($player);
	return $player;
}

function resetHP($player){
	$player[hp]=$player[max_hp];
	updateField("characters", "hp", $player[max_hp], "id", $player[id]);
	return $player;
}

function resetKillCount($player){
	$player[kills_in_a_row]=0;
	updateField("characters", "kills_in_a_row", '0', "id", $player[id]);
	return $player;
}

function incrementKillCount($player){
	$id=$player[id];
	$player[kills_in_a_row]+=1;
	$player[kill_count]+=1;
	updateField("characters","kills_in_a_row",$player[kills_in_a_row],"id",$id);
	updateField("characters","kill_count",$player[kill_count],"id",$id);
	if($player[kills_in_a_row]>$player[best_kiar]){
		updateField("characters","best_kiar",$player[kills_in_a_row],"id",$id);
	}
	return $player;
}

function resetMob($enemy){
	deleteRow($enemy[id],"enemies");
}

function attemptFlee($player, $enemy){
	$roll = rand(1,20) + floor($player[dex]/2) + rand(0,floor($player[luck]/2));
	echo "You roll a total of ".$roll."<br>";
	if($roll >= (10 + floor($enemy[dex]/2) )){
		resetMob($enemy);
		$player[flee]=true;
	}
	return $player;
}

function determineEnemyLevel($enemy){
	$level = $enemy[str]+$enemy[dex]+$enemy[con]+
		$enemy[wis]+$enemy[cha]+$enemy[luck];
	$level = floor(($level-10)/2)+1;
	if($level<=0)
		$level=1;
	return $level;
}

function gainXp($enemy, $player){
	$lvlDifference = determineEnemyLevel($enemy)-$player[level];
	$value = $lvlDifference*100;
	if($value<=0)
		$value=50;
	if($player[xpgain]){
		$value *= (1 + ($player[xpgain]/100));
	}
	echo "The enemy was worth ".$value." XP.<br>";
	$player[xp]+=$value;
	if(($player[xp])>=($player[level]*500)){
		$player[xp]-=($player[level]*500);
		$player=levelUp($player);
	}
	updateField("characters","xp",$player[xp],"id",$player[id]);
	return $player;
}

function levelUp($player){
	$player[level]+=1;
	$player[mode]="levelup";
	updateField("characters","mode","levelup","id",$player[id]);
	updateField("characters","level",$player[level],"id",$player[id]);
	return $player;
}

function generateReward($level){
	$reward[item]=generateItem($level);
	$value = determineItemLevel($reward[item]);
	if($value<$level){
		$diff = $level - $value;
		$diff *= 100; //100gp potential per level of difference
		$reward[gold] = rand(0, $diff);
	}
	else{
		$reward[gold] = 0;
	}
	return $reward;
}

function determineItemLevel($item){
	$sum=0;
	while (list($key, $val) = each($item)){
	 if($item[$key]!=NULL){
	  if(filter_var($val, FILTER_VALIDATE_INT)){
	   if($key!="id"&&$key!="owner"){
	    if($key=="hp"){
		$sum+=($val/10);}
	    else if($key=="xpgain"){
		$sum+=(($val/15)*2);}
	    else if($key=="max_damage"){
		$sum+=1+(($val-6)/2);}
	    else if($key=="turns"&&$val>0){
		$sum+=(($val-2)/2);}
	    else{
		$sum+=($val/2);}
	   }
	  }
	 }
	}
	if($sum==0){
		$sum=0.5;
	}
	return $sum;
}

function itemValue($item){
	$level=determineItemLevel($item);
	$value= $level*100;
	return $value;
}

function tooManyItems($id){
	$query = "SELECT * FROM items WHERE owner='$id'";
	$data = mysql_query($query);
	if(mysql_num_rows($data)>=30){
		return true;
	}
	else{
		return false;
	}
}

function printItemTable($items, $type, $ids, $mode){
	$fields = array("id", "name", "str", "dex", "con", "wis", "cha", "luck",
		"hp","max_hp", "xpgain", "max_damage", "turns", "Requirements", "Value");
	$reqFields = array("min_str", "min_dex", "min_con", "min_wis", "min_luck", "min_lvl");

	if($mode)
		array_push($fields, $mode);

	echo "<table>";
	$headers = $fields;
	$headers[0]=$type."s";
	printTableHeader($headers);
	for($i=0;$i<count($items);$i++){
		if(($items[$i][type]==$type)||($type=="all")){
			$item=$items[$i];
			if(in_array($item[id],$ids))
				echo '<tr class="highlight">';
			else
				echo "<tr>";
			for($j=0;$j<count($fields);$j++){
				if($item[$fields[$j]]!=NULL){
					echo "<td>".$item[$fields[$j]]."</td>";
				}
			}
			echo "<td>";
			for($j=0;$j<count($reqFields);$j++){
				if($item[$reqFields[$j]]){
					echo $reqFields[$j].": ".$item[$reqFields[$j]]." ";
				}
			}
			echo "</td>";
			echo '<td>'.itemValue($item).'</td>';
//			echo '<td>'.determineItemLevel($item).'</td>';
			if($mode)
				echo '<td><a href="'.$_SERVER['PHP_SELF'].'?'.addToHref("item",$item[id]).'">'.$mode.'</a></td>';
			echo "</tr>";
		}
	}
	echo "</table>";

}

function printItemEquipBar($items, $player, $type){
	$item = itemByID($items, $player[$type]);
	echo '<table class="stats"><tr>';
	if($type!="oneshot")
		echo '<td>Current '.$type.':</td><td>'.$item[name].'</td>';
	echo '<td>
		<form action="?equip='.$type.'" method="post">
		<select name="id">';
	for($i=0;$i<count($items);$i++){
		if($items[$i][type]==$type){
			echo '<option value="'.$items[$i][id].'">'.$items[$i][name].'</option>';
		}
	}
	echo '</select>
		</td>
		<td>';
	if($type=="oneshot")
		echo '<input type="submit" value="Use">';
	else
		echo '<input type="submit" value="Equip">';
	echo '</form>
		</td>
		</tr>
		</table>';
}


function equipItem($item, $slot, $player){
	$fail=0;
	if($item[min_str]>$player[str])
		$fail=1;
	if($item[min_dex]>$player[dex])
		$fail=1;
	if($item[min_con]>$player[con])
		$fail=1;
	if($item[min_wis]>$player[wis])
		$fail=1;
	if($item[min_cha]>$player[cha])
		$fail=1;
	if($item[min_luck]>$player[luck])
		$fail=1;
	if($item[min_lvl]>$player[level])
		$fail=1;
	if($fail){
		echo "You have failed to meet the requirements to equip ".$item[name].".<br>";
	}
	else{
		updateField("characters", $slot, $item[id], "id", $player[id]);
	}
}

function applyItems($player, $items){
	if($player[weapon]){
		$item=itemByID($items, $player[weapon]);
//		echo "name:".$item[name];
		$player=addItemBonus($player,$item);
	}
	if($player[trinket1]){
		$item=itemByID($items, $player[trinket1]);
		$player=addItemBonus($player,$item);
	}
	if($player[trinket2]){
		$item=itemByID($items, $player[trinket1]);
		$player=addItemBonus($player,$item);
	}
/*	for($i=0;$i<count($items);$i++){
		if($item[$i][type]=="buff"){

		}
	}
*/	return $player;
}

function addItemBonus($player, $item){
	//Add up base stats and HP
	$core = array("str", "dex", "con", "wis", "cha", "luck", "max_hp");
	for($i=0; $i<count($core);$i++){
		$player[$core[$i]]+=$item[$core[$i]];
	}
	//If a weapon, set maxdamage, else, add to it
	if($item[type]=="weapon"){
		$player[max_damage]=$item[max_damage];
	}
	else{
		$player[max_damage]+=$item[max_damage];
	}
	//xpboost
	if($player[xpgain]){
		$player[xpgain]+=$item[xpgain];
	}
	else{
		$player[xpgain]=$item[xpgain];
	}

	return $player;
}

function incrementTurns($items, $player){
	for($i=0;$i<count($items);$i++){
		$item=$items[$i];
		if($item[type]=="buff"){
			if($item[turns]>0){
				$player=healPlayer($item[hp],$player);
				$player=addItemBonus($player,$item);
				updateField("items", "turns", ($item[turns]-1), "id", $item[id]);
			}
			else{
				deleteRow($item[id], "items");
			}
		}
	}
	return $player;
}

function itemByID($items, $id){
	for($i=0;$i<count($items);$i++){
		if($items[$i][id]==$id){
			$item=$items[$i];
		}
	}
	return $item;
}

function drinkPotion($item, $player){
	$item[type]="buff";
	$player=addItemBonus($player, $item);
	$player=healPlayer($item[hp],$player);
	updateField("items", "type", "buff", "id", $item[id]);
	return $player;
}

function healPlayer($hp, $player){
	if($hp){
		$sum = $hp+$player[hp];
		if($sum>$player[max_hp]){
			$player[hp]=$player[max_hp];
		}
		else{
			$player[hp]=$sum;
		}
		echo "mork".$player[hp]." heal".$sum."!";
		updateField("characters", "hp", $player[hp], "id", $player[id]);
	}
	return $player;
}

function sellItem($item, $player){
	$player[gold] += itemValue($item);
	updateField("items","owner",0,"id",$item[id]);//set item owner to 0, aka, Shop
	updateField("characters","gold",$player[gold],"id",$player[id]);//give player gold for item
	return $player;
}

function buyItem($item, $player){
	$value = itemValue($item);
	$sum = $player[gold]-$value;
	if($sum<0){ //player can't afford Item
		echo "You are too poor and can't afford ".$item[name].".<br>";
	}
	else{
		$player[gold]=$sum;
		updateField("items","owner",$player[id],"id",$item[id]);//set itme owner to player
		updateField("characters","gold",$sum,"id",$player[id]);//adjust player's gold
	}
	return $player;
}

function printTopUser(){
	$query = "SELECT * FROM characters;";
        $data = mysql_query($query);
        if(mysql_num_rows($data)<1){
                return NULL;
        }

	$highestKIAR=0;
	while($row = mysql_fetch_array($data, MYSQL_ASSOC)){
		if($row[best_kiar]>$highestKIAR){
			$highestKIAR=$row[best_kiar];
			$bestRow = $row;
		}
	}
	if($bestRow){
		echo $bestRow[name]." is the character with the most kills in a row without dying at ".$bestRow[best_kiar]." kills.<br>";
	}
}

?>


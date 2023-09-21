<?php

include("dbpassword.php");

$dbhost = 'localhost';
$dbname = 'gamev2';
$dbuser = 'root';
$dbpass = getDBpass();


$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname); //create sql connection
if ($mysqli->connect_errno) {
        die("ERRORROROR: Connect Failed ".$mysqli->connect_error);
}

$mysqli->select_db($dbname);


function createSalt(){
	$string = md5(uniqid(rand(),true));
	return substr($string, 0, 3);
}

function registerUser($username, $hash, $salt){
	$username = mysqli_real_escape_string($mysqli, $username);
	$date = date( 'Y-m-d H:i:s');
	$query = "INSERT INTO users ( username, password, salt, create_date )
		VALUES ( '$username','$hash','$salt','$date' );";
	if(!$mysqli->query($query)){
                die('OH NOES:'.$mysqli->error);}
}

function isValueInTable($value, $column, $table){
	$value = mysqli_real_escape_string($mysqli, $value);
	$column = mysqli_real_escape_string($mysqli, $column);
	$table = mysqli_real_escape_string($mysqli, $table);
	$query = "SELECT ".$column.
		" FROM ".$table.
		" WHERE ".$column."='".$value."';";
	$result = $mysqli->query($query);
	$result = mysqli_fetch_array($result);
	if($result==NULL){
		return false;
	}
	return true;
}

function fetchRow($value, $column, $table){
	$value = mysqli_real_escape_string($mysqli, $value);
	$column = mysqli_real_escape_string($mysqli, $column);
	$table = mysqli_real_escape_string($mysqli, $table);
	$query = "SELECT *
		FROM ".$table."
		WHERE ".$column."='".$value."';";
	$result = $mysqli->query($query);
	$result = mysqli_fetch_array($result);
	return $result;
}

function updateField($table, $field, $data, $column, $id){
	//no inject code??
	$query = "UPDATE ".$table.
		" SET ".$field."='".$data."'
		WHERE ".$column."='".$id."';";
	if(!$mysqli->query($query)){
                die('OH NOES:'.$mysqli->error);}
}

function deleteRow($id, $table){
	//no inject code??
	$query = "DELETE FROM ".$table.
		" WHERE id='".$id."';";
	if(!$mysqli->query($query)){
                die('OH NOES:'.$mysqli->error);}
}

function printNavBar(){
	echo '<table>
		<th><a href="myaccount.php">'.$_SESSION["username"].'</a></th>
		<tr class="cent">
		<th><a href="userlist.php">Users</a></th>
		<th><a href="skrbs.php">skrbs</a></th>
		<th><a href="mainmenu.php">Game</a></th>
		<th><a href="logout.php">Logout</a></th>
		</tr></table>';
}

function loginUser($username, $password){
	if(isValueInTable($username, "username", "users")){//username matches
		$data = fetchRow($username, "username", "users"); //value, column, table
		$hash = hash('sha256', $data['salt'].hash('sha256',$password));
		if($hash == $data['password']){//password matches
			validateUser($data);
			header('Location: main.php');
		}
	}
	echo "username or password did not match";
}

function isCodeValid($code){
	$code = mysqli_real_escape_string($mysqli, $code);
	$data = fetchRow($code, "code", "codes"); //value, column, table
	if($data['valid']){ //magic number/field that is set in the sql database to say if this invite code has been used in the past.
		mysql_query("UPDATE codes SET valid='0'
			WHERE code='$code';");
		return true;
	}
	return false;
}

function isUsernameTake($name){
	return isValueInTable($name, "username", "users"); //value, column, table
}


function generateCode(){
	$code = rand(10000000,99999999);
	$mysql->query("INSERT INTO codes (code) VALUES ('".$code."');");
	//does 'valid' need to be set to 1?
}

function printTable($query, $header){
	//does this need no inject???
	$result=$mysql->query($query);
	//seems unnecesary? v
	//$total_rows = $result->num_rows;
	echo "<table>";
	//deprecated? v
	//printTableHeader($header);
	echo '<tr class="left">';
	for($i=0;$i<count($header);$i++){
		echo '<th>'.$header[$i].'</th>';
	}
	echo "</tr>";
	while($row = mysqli_fetch_array($result)){
		echo '<tr>';
		for($i=0;$i<count($row);$i++){
			echo '<td>'.$row[$i].'</td>';
		}
		echo '</tr>';
	}
	echo "</table>";
}

function printTableHeader($names){
	echo '<tr class="left">';
	for($i=0;$i<count($names);$i++){
		echo '<th>'.$names[$i].'</th>';
	}
	echo '</tr>';
}


function fetchRandomRow($query, $table){
	//no inject?
	$result = $mysqli->query($query);
	$num = $result->num_rows;
	if($num > 0){
		$id_array = array();
		for ($i=0;$i<$num;$i++){
			$row = mysqli_fetch_array($result);
			$id_array[$i] = $row['id'];
		}
		$query = "SELECT * FROM ".$table." WHERE id =".$id_array[rand(0,(count($id_array)-1))].";";
		$result = $mysqli->query($query);
		$row =  mysqli_fetch_array($result);
	}
	return $row;
}

function addToHref($item, $value){
	//maybe this could be done better? idk, little goofy, but I think it works...
	$query = $_SERVER['QUERY_STRING'];
	$qArray = explode('&',$query);
	$inQuery=0;
	if($qArray[0]!=NULL){
		for($i=0;$i<count($qArray);$i++){
			$x = explode('=',$qArray[$i]);
			if($x[0]==$item){
				$x[1]=$value;
				$inQuery=1;
			}
			if($i==0){
				$final=$x[0]."=".$x[1];}
			else{
				$final=$final."&".$x[0]."=".$x[1];}
		}
	}
	if(!$inQuery&&$item!=NULL&&$value!=NULL){
		if($qArray[0]==NULL){
			$final=$item."=".$value;}
		else{
			$final=$final."&".$item."=".$value;}
	}
	//$final=$_SERVER['PHP_SELF']."?".$final;
	return $final;
}

function validateUser($data){
	session_regenerate_id(); //why do I call this?
	if($data['admin'])
		$_SESSION['admin']=1;
	$_SESSION["valid"]=1;
	$_SESSION["userid"]=$data['id'];
	$_SESSION["username"]=$data['username'];

	$date = date( 'Y-m-d H:i:s');
	$ip = $_SERVER[HTTP_CLIENT_IP];
	updateField("users", "last_login", $date, "id", $data[id]);
	updateField("users", "last_ip_used", $ip, "id", $data[id]);
}


function isLoggedIn(){
	if($_SESSION["valid"])
		return true;
	return false;
}

function bounce(){
	if(!isLoggedIn()){
		header('Location: login.php');
		die();
	}
}

function bounceAdmin(){
	if(!$_SESSION["admin"]){
		header('Location: main.php');
		die();
	}
}

function logout(){
	$_SESSION = array();
	session_destroy();
}
?>


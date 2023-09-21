<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("basefunctions.php");
session_start();?>
<link rel="stylesheet" type="text/css" href="game.css"/>
<title>The GAME</title>
</head>

<body>
<div class="center">

<?php
	$code = $_POST['code'];
	$username = $_POST['username'];
	$pass1 = $_POST['pass1'];
	$pass2 = $_POST['pass2'];
	if($code!=NULL){
		if($pass1!=$pass2){
			echo "error: passwords don't match";}
		else if(strlen($username)>30){
			echo "error: username too long";}
		else if(strlen($username)<4){
			echo "error: username too short";}
		else if(strlen($pass1)<4){
			echo "error: password too short";}
		else if(!isCodeValid($code)){
			echo "your code doesn't work";}/*
		else if(isUsernameTaken($username)){
			echo "your name has been taken already";}*/
		else{
			$hash=hash('sha256',$pass1);
			$salt=createSalt();
			$hash=hash('sha256',$salt.$hash);
			registerUser($username, $hash, $salt);
			header('Location: login.php');
		}
	}
	echo '<form name="register" action="register.php" method="post">
		<div class="registration">
		<table>
		<tr><td>USERNAME:</td><td> <input type="text" name="username" maxlength="30"/></td></tr>
		<tr><td>PASSWORD:</td> <td><input type="password" name="pass1" /></td></tr>
		<tr><td>AGAIN:</td><td> <input type="password" name="pass2" /></td></td>
		<tr><td>CODE:</td> <td><input type="text" name="code" /></td></tr>
		<tr><td></td><td><input type="submit" value="REGISTER" /></td></tr>
		</form>
		</div>';

?>
</div>
</body>
</html>

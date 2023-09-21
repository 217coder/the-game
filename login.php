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
	echo '<div class="login">
		<table>
		<form name="login" action="login.php" method="post">
		<tr><th>USERNAME:</th><th><input type="text" name="username" /></th>
		<th>PASSWORD:</th><th><input type="password" name="password" /></th>
		<th><a href="register.php">REGISTER</a></th>
		<th><input type="submit" value="LOGIN" /></th></tr>
		</form>';
	$username = $_POST['username'];
	$password = $_POST['password'];


	if($username!=NULL)
		loginUser($username, $password);

?>
</div>
</body>
</html>

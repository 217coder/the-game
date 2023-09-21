<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include("basefunctions.php");
session_start();
?>
</head>
<body>
<?php
	logout();
	header('Location: login.php');
	die();
?>
</body>
</html>

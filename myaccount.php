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
bounce();
printNavBar();
echo '<table>
	<tr><td>Account</td></tr>
	<tr><td>Options</td></tr>
	<tr><td>Go</td></tr>
	<tr><td>Here</td></tr>
	</table>';

?>
</div>
</body>
</html>

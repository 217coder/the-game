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
	bounceAdmin();
	printNavBar();
	printTable("SELECT * FROM codes;", array("Code","Valid"));
	echo '<table><tr class="cent">
		<td><a href="generatecode.php">Generate Code</a></td>
		</tr></table>';
?>

</div>
</body>
</html>

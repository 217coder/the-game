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

	$zero = 0;
	$one = 1;
	$null = NULL;

	if($zero){
		echo "zero";
	}
	if($one){
		echo "one";
	}
	if($null){
		echo "null";
	}

?>
</div>
</body>
</html>

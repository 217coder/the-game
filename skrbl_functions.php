<?php

function setImgID($imgID, $total){
	if($imgID==NULL){
		$imgID = 0;}
	else if($imgID=="last"){
		$imgID = $total;}
	else if ($imgID =="first"){
		$imgID = 0;}
	else if ($imgID =="random"){
		$imgID = rand(0, $total);}
	else if($imgID>$total){
		$imgID = $total;}
	else if($imgID<0){
		$imgID = 0;}
	else{
	}
	return $imgID;
}

function printTableHead($title){
echo '<table>
<tr class="cent">
<th><a href="myaccount.php">MyAccount</a></th>
<th><a href="userlist.php">Users</a></th>
<th><a href="skrbs.php">skrbs</a></th>
<th><a href="forum.php">Forum</a></th>
<th><a href="logout.php">Logout</a></th>
</tr>';
}

function printNavRow($imgID){
	print "<table><tr class=\"cent\">
		<td><a href=".$_SERVER['PHP_SELF']."?img_id=first>First</a></td>
		<td><a href=".$_SERVER['PHP_SELF']."?img_id=".($imgID-1).">Prev</a></td>
		<td><a href=".$_SERVER['PHP_SELF']."?img_id=random>RANDOM!</a></td>
		<td><a href=".$_SERVER['PHP_SELF']."?img_id=".($imgID + 1).">Next</a></td>
		<td><a href=".$_SERVER['PHP_SELF']."?img_id=last>Last</a></td>
		</tr>";
}

function printRotatorRow($imgID, $rotate, $image){
	print "<tr>
		<th><a href=".$_SERVER['PHP_SELF']."?img_id=".$imgID."&rotate=".($rotate-1).">Rotate Left</a></th>
		<th colspan=3>".basename($image)."</th>
		<th><a href=".$_SERVER['PHP_SELF']."?img_id=".$imgID."&rotate=".($rotate+1).">Rotate Right</a></th>
		</tr>";
}

function printImage($image, $rotate){
	print "<tr>
		<td class=\"img\" colspan=5><img src=\"grabSkrbl2.php?id=".$image."&rotate=".$rotate."\"></td>
		</tr>
		</table>";
}
?>

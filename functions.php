<?php

function findCharacter($id){
	$query = "SELECT id
		FROM characters
		WHERE owner = '$id';"
	$result = mysql_querry($query);
	if(mysql_num_rows($result)<1)
		return NULL;
	$data = mysql_fetch_array($result, MYSQL_ASSOC);
	return $data['id'];
}

?>

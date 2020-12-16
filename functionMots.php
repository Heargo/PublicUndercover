<?php
function chargemots($c){
	//requete
	$sql="SELECT * FROM mots";
	$result=  mysqli_query($c, $sql);

	//on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

function insertMot($c,$mot1,$mot2,$fromPlayer,$private){
	$sql="INSERT INTO `mots` (`id`,`mot1`,`mot2`,`fromPlayer`,`private`) VALUES (NULL,'$mot1','$mot2',$fromPlayer,$private);";
	#var_dump($sql);
	mysqli_query($c, $sql); //on fait la requete
}

function deleteMot($c,$id){
	$sql="DELETE INTO `mots` WHERE `id` = $id;";
	mysqli_query($c, $sql);
}
?>
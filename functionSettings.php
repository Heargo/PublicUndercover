<?php
function chargesettings($c){
	//requete
	$sql="SELECT * FROM settings";
	$result=  mysqli_query($c, $sql);

	//on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

function insertSettings($c,$maxPlayer,$listeCategories,$nbMembre,$nbUndercover,$nbWhiteman){
	$encodedlisteCategories = json_encode($listeCategories);
	$sql="INSERT INTO `settings` (`maxPlayer`,`listeCategories`,`nbMembre`,`nbUndercover`,`nbWhiteman`) VALUES ($maxPlayer,$encodedlisteCategories,$nbMembre,$nbUndercover,$nbWhiteman);";
	#var_dump($sql);
	mysqli_query($c, $sql); //on fait la requete
}


?>
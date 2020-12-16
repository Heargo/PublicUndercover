<?php

function chargedatapartie($c){
	//requete
	$sql="SELECT * FROM dataPartie";
	$result=  mysqli_query($c, $sql);

	//on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

function chargeGame($c,$roomID){
	//on charge la game
	$sql = "SELECT * FROM rooms INNER JOIN datapartie ON rooms.idDataPartie = datapartie.id WHERE rooms.roomid =".$roomID.";";
	$result= mysqli_query($c, $sql);
	$GAME=mysqli_fetch_assoc($result);
	return $GAME;
}


function getUserName($c,$id){
	$sql="SELECT users.username FROM users WHERE users.id = '$id';";
    $result =  mysqli_query($c, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row["username"];
}

function ecritMot($c,$id,$newPlayersMots){
	$sql = "UPDATE `datapartie` SET `playersMots` = '".$newPlayersMots."' WHERE `datapartie`.`id` = $id;";
	$result =  mysqli_query($c, $sql);
	var_dump($newPlayersMots);
	var_dump($sql);
}

function nextPlayer($c,$GAME){
	$order = json_decode($GAME["listeOrdredejeu"],true);
	var_dump($order);
	$playerPlaying = $GAME["playerPlaying"];
	$id =$GAME["idDataPartie"];
	//on se place sur le joueur suivant
	$player=current($order); // on se met a debut du tableau
	while ( $player !=$playerPlaying) {//on avance jusqu'au joueur 
		$player=next($order);
	}
	//on prend le suivant
	$playerPlaying	= next($order);
	//on prend le suivant tant qu'on a pas atteint un joueur vivant
	$morts = json_decode($GAME["morts"],true);
	while (in_array($playerPlaying,$morts)){
		$playerPlaying= next($order);
	}
	

	if ($playerPlaying==False and $GAME["avancementPartie"]=="tour"){
		// on défini le joueur qui commence après
		$playerPlaying=json_decode($GAME["listeOrdredejeu"],true);
		$trouve=false;
		$i=0;
		while (!$trouve){
			$player=$playerPlaying[$i];
			if ( !in_array($player, $morts) ){
				$trouve=true;
			}
			$i = $i+1;
		}
		

		$sql = "UPDATE `datapartie` SET `avancementPartie` = 'vote',`playerPlaying` = $player WHERE `datapartie`.`id` = $id;";

	}else{
		$sql = "UPDATE `datapartie` SET `playerPlaying` = $playerPlaying WHERE `datapartie`.`id` = $id;";
	}
	$result =  mysqli_query($c, $sql);


}

function vote($c,$sender,$target,$GAME){
	$id =$GAME["idDataPartie"];
	$morts = json_decode($GAME["morts"],true);
	if (!in_array($sender,$morts) and $GAME["playerPlaying"]==$sender ){
		$playerVotes = json_decode($GAME["playersVote"],true);
		$votesRecus = json_decode($GAME["listeVoteRecu"],true);
		$playerVotes[$sender]=true;
		$votesRecus[$target] = $votesRecus[$target]+1;
		$playerVotes = json_encode($playerVotes);
		$votesRecus = json_encode($votesRecus);
		$sql = "UPDATE `datapartie` SET `listeVoteRecu` = '$votesRecus', `playersVote` = '$playerVotes' WHERE `datapartie`.`id` = $id;";
		$result =  mysqli_query($c, $sql);
		nextPlayer($c,$GAME);
	}
}

//à chaque vote cette fonction est appelée. Si le joueur est le dernier a voter alors la fin de vote se déclanche
function checkEndVote($c,$roomID){
	$GAME= chargeGame($c,$roomID);
	$id =$GAME["idDataPartie"];
	$playerVotes = json_decode($GAME["playersVote"],true);
	$morts = json_decode($GAME["morts"],true);
	$voteEnded = True;
	//on regarde si il reste des personne qui n'on pas voté
	foreach ($playerVotes as $playerID => $voted) {
		if (!$voted && !in_array($playerID, $morts) ){$voteEnded=False;}
	}
	//si tout le monde à voté
	if ($voteEnded){
		$votesRecus = json_decode($GAME["listeVoteRecu"],true);
		$eliminate = -1;
		$nbVotesMax = -1;
		//on regarde qui à le plus de vote
		foreach ($votesRecus as $playerID => $nbVotes) {
			if ($nbVotes >$nbVotesMax){
				$eliminate = $playerID;
				$nbVotesMax = $nbVotes;
			}
			elseif ($nbVotes == $nbVotesMax){ //en cas d'égalité, personne n'est éliminé
				$eliminate = -1;
			}
		}
		//si une personne est à éliminer, on l'élimine
		if ($eliminate >=0){
			array_push($morts, "$eliminate");
			$morts = json_encode($morts);
			$sql = "UPDATE `datapartie` SET `morts` = '$morts' WHERE `datapartie`.`id` = $id;";
			$result =  mysqli_query($c, $sql);

		}

		//on met la game en jour
		$GAME= chargeGame($c,$roomID);//on recharge (pour la liste des morts a jour)

		$playerVotes = json_decode($GAME["playersVote"],true);
		$votesRecus = json_decode($GAME["listeVoteRecu"],true);
		$playersMots = json_decode($GAME["playersMots"],true);
		$morts = json_decode($GAME["morts"],true);

		//on init les votes et les mots
		foreach ($playerVotes as $playerid => $value) {
			$playerVotes[$playerid]=false;
			$votesRecus[$playerid] = 0;
			$playersMots[$playerid] = "";
		}

		// on défini le joueur qui commence après
		$playerPlaying=json_decode($GAME["listeOrdredejeu"],true);
		$trouve=false;
		$i=0;
		while (!$trouve){
			$player=$playerPlaying[$i];
			if ( !in_array($player, $morts) ){
				$trouve=true;
			}
			$i = $i+1;
		}

		//on encode en json ce qui a besoin d'être encodé
		$playerVotes = json_encode($playerVotes);
		$votesRecus = json_encode($votesRecus);
		$playersMots = json_encode($playersMots);

		//on met a jour la table
		$sql = "UPDATE `datapartie` SET `avancementPartie` = 'tour',`listeVoteRecu` = '$votesRecus', `playersVote` = '$playerVotes',`playersMots` = '$playersMots',`playerPlaying` = $player WHERE `datapartie`.`id` = $id;";
		$result =  mysqli_query($c, $sql);	
	}
}

function checkEndGame($c,$roomID){
	//on met la game en jour
	$GAME= chargeGame($c,$roomID);
	$id =$GAME["idDataPartie"];
	//le nombre d'intruts restants
	$joueurs = json_decode($GAME["listeOrdredejeu"],true);
	$undercovers = json_decode($GAME["undercovers"],true);
	$membres = json_decode($GAME["membres"],true);
	$morts = json_decode($GAME["morts"],true);
	$nbIntrusAlive=0;
	$nbMembresAlive=0;
	foreach ($joueurs as $i => $playerid) {
		//si c'est un undercover
		if (!in_array($playerid, $morts) && in_array($playerid, $undercovers)) {
			$nbIntrusAlive++;
		}
		//si c'est un membre
		if (!in_array($playerid, $morts) && in_array($playerid, $membres)){
			$nbMembresAlive++;
		}
	}
	//on change le statut de la partie si c'est la fin
	$time = time();
	if ($GAME["endGameTime"]==0){
		if ($nbIntrusAlive==0){
			$sql = "UPDATE `datapartie` SET `avancementPartie` = 'finMembres',`endGameTime` = '$time' WHERE `datapartie`.`id` = $id;";
			$result =  mysqli_query($c, $sql);
			addScore($c,$membres,$morts);
		}
		if ($nbMembresAlive==1){
			$sql = "UPDATE `datapartie` SET `avancementPartie` = 'finUndercovers',`endGameTime` = '$time' WHERE `datapartie`.`id` = $id;";
			$result =  mysqli_query($c, $sql);
			addScore($c,$undercovers,$morts);
		}
		if ($nbMembresAlive==1 or $nbIntrusAlive==0){
			$sql = "UPDATE `rooms` SET `start` = 0 WHERE `rooms`.`roomid` = $roomID;";
			$result =  mysqli_query($c, $sql);
		}
	}
	
}

function addScore($c,$players,$morts){
	foreach ($players as $i => $playerID) {
		#on recup les données du joueur
		$sql = "SELECT users.score from `users` WHERE users.id = $playerID;";
		$result =  mysqli_query($c, $sql);
		$data = mysqli_fetch_assoc($result)["score"];
		#on met a jour le score
		if (in_array($playerID, $morts)){
			$bonus=0;
		}else{
			$bonus=0.5;
		}
		$data = $data + 1 +$bonus;
		#on met a jour dans la base de donnée
		$sql = "UPDATE `users` SET `score` = '$data' WHERE users.id = $playerID;";
		$result =  mysqli_query($c, $sql);


	}

}

function insertDataPartie($c,$motMembre,$motUndercover,$morts,$undercovers,$membres,$avancementPartie,$listeVoteRecu,$playersVote,$playersMots,$listeOrdredejeu,$playerPlaying){

	$encodedmorts=json_encode($morts);
	$encodedundercovers=json_encode($undercovers);
	$encodedmembres=json_encode($membres);
	$encodedlisteVoteRecu=json_encode($listeVoteRecu);
	$encodedplayersVote=json_encode($playersVote);
	$encodedplayersMots=json_encode($playersMots);
	$encodedlisteOrdredejeu=json_encode($listeOrdredejeu);

	$sql="INSERT INTO `dataPartie` (`id`,`motMembre`,`motUndercover`,`morts`,`undercovers`,`membres`,`avancementPartie`,`listeVoteRecu`,`playersVote`,`playersMots`,`listeOrdredejeu`,`playerPlaying`) VALUES (NULL,'$motMembre','$motUndercover','$encodedmorts','$encodedundercovers','$encodedmembres','avancementPartie','$encodedlisteVoteRecu','$encodedplayersVote','$encodedplayersMots','$encodedlisteOrdredejeu',$playerPlaying);";
	mysqli_query($c, $sql); //on fait la requete

}

?>

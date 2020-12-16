<?php 
session_start();
include 'functionDataPartie.php';
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

//on test la fin de la game
checkEndGame($c,$_SESSION["room_id"]);
//on charge la game
$GAME=chargeGame($c,$_SESSION['room_id']);

//genere l'affichage
echo "<div class ='data-container'>";


//on gère l'affichage durant la partie
if ($GAME["avancementPartie"]!="finMembres" and $GAME["avancementPartie"]!="finUndercovers"){
	//mot et role
	if (in_array($_SESSION["idUser"], json_decode($GAME["undercovers"])) ) { //si le joueur est undercover
		echo "<img class='image-titre' src='images/spy.png'><strong><h2>UNDERCOVER</h2></strong>";
		echo "<h2> Votre mot : " . $GAME["motUndercover"] . "</h2>";
	}else{//si il est membre
		echo "<img class='image-titre' src='images/membre.png'><strong><h2>MEMBRE</h2></strong>";
		echo "<h2> Votre mot : " . $GAME["motMembre"] . "</h2>";	
	}
	

	//joueurs + leurs mot
	$playersInOrder= json_decode($GAME["listeOrdredejeu"]);
	echo "<ul class ='game-list'>";
	$morts = json_decode($GAME["morts"],true);
	$undercover = json_decode($GAME["undercovers"],true);
	$membres = json_decode($GAME["membres"],true);

	foreach ($playersInOrder as $i => $playerid) {
		$username = getUserName($c,$playerid);
		$lswords = json_decode($GAME["playersMots"],true);
		$votes = json_decode($GAME["playersVote"],true);
		$voted = $votes[$_SESSION["idUser"]]; //si l'user a voté
		$nbVoteContre = json_decode($GAME["listeVoteRecu"],true)[$playerid];
		$word = htmlspecialchars($lswords[$playerid], ENT_QUOTES);
		if($playerid == $GAME["playerPlaying"]){
			$playing = "playing";
			$aVousDeJouer = "À vous !";
		}
		else{
			$playing = "";
			$aVousDeJouer = "";
		}
		if (!in_array($playerid, $morts) and $GAME["avancementPartie"]=="vote"){
			echo "<li class='vote $playing' onclick=\"vote('".$voted ."','" .$playerid ."');\" ><img class='image-game' src='images/inconnu.png'><strong><p>" . $username . "</p></strong><p>" . $word . "</p><p>Votes : ".$nbVoteContre ."</p></li>";
		}elseif(!in_array($playerid, $morts) and $GAME["avancementPartie"]!="vote"){
			if ($word == "" ){
				echo "<li class='$playing'><img class='image-game' src='images/inconnu.png'><strong><p>" . $username . "</p></strong></li>";
			}
			else{
				echo "<li class='$playing'><img class='image-game' src='images/inconnu.png'><strong><p>" . $username . "</p></strong><p>" . $word ."</p></li>";
			}


		}

		elseif(in_array($playerid, $morts)){
			if(in_array($playerid,$undercover)){
				echo "<li class='dead'><img class='image-game' src='images/spy.png'><strong><p>" . $username . "</p></strong>DEAD</li>";
			}
			elseif(in_array($playerid,$membres)){
				echo "<li class='dead'><img class='image-game' src='images/membre.png'><strong><p>" . $username . "</p></strong>DEAD</li>";
			}
			
		}
		
	}
	echo "</ul>";
	if($_SESSION["idUser"] == $GAME["playerPlaying"]){
		if($GAME["avancementPartie"]=="vote"){
			echo "<h2>À vous de voter !</h2>";
		}
		else{
			echo "<h2>À vous de jouer !</h2>";
		}
		
	}
	
	
}
//on gére l'affichage de fin de partie
else{
	
	//on affiche l'équipe gagnante
	if ($GAME["avancementPartie"]=="finMembres"){
		echo "<div class='win'>";
		echo "<h2>Victoire des Membres !</h2>";
		echo "<p>Mot des membres : " . $GAME["motMembre"] . "</br>";
		echo "Mot des undercover : " . $GAME["motUndercover"] ."</p>";
		echo "<h4>Gagnants</h4>";
		echo "</div>";
		echo "<ul class='game-list'>";
		foreach (json_decode($GAME["membres"],true) as $i => $playerid) {
			$username = getUserName($c,$playerid);
			echo "<li><img class='image-game' src='images/membre.png'><strong><p>" . $username . "</p></strong></li>";
		}
		echo "</ul>";
		
	}
	if ($GAME["avancementPartie"]=="finUndercovers"){
		echo "<div class='win'>";
		echo "<h2>Victoire des Undercovers !</h2>";
		echo "<p>Mot des membres : " . $GAME["motMembre"] . "</br>";
		echo "Mot des undercover : " . $GAME["motUndercover"] ."</p>";
		echo "<h4>Gagnants</h4>";
		echo "</div>";
		echo "<ul class='game-list'>";
		foreach (json_decode($GAME["undercovers"],true) as $i => $playerid) {
			$username = getUserName($c,$playerid);
			echo "<li><img class='image-game' src='images/spy.png'><strong><p>" . $username . "</p></strong></li>";
		}
		echo "</ul>";
		
	}
	
	//on redirige sur le hub
	//a faire

}
echo "</div>";
?>
<?php 
function table($x,$y){
	for ($i=0; $i <$y ; $i++) { 
		echo "<tr>";
		for ($j=0; $j <$x ; $j++) { 
			echo "<td></td>";
		}
		echo "</tr>";
	}
}


function afficheFormCreateRoom(){
	echo "<section class='form-center'>";
	echo "<article>";
	echo "<h2>Créer une partie</h2>";
	echo "<form action='index.php' method='post' autocomplete='off'>";
	//name
	echo "<p>";
	echo "<input class='formInput' type='text' name='name' id='name' placeholder='Nom de la partie'>";
	echo "</p>";
	//password
	echo "<p>";
	echo "<br/><label for='password'>(champ optionnel)<label><br/> ";
	echo "<input autocomplete='new-password' class='formInput' type='password' name='password' id='password' placeholder='Mot de passe de la partie'>";
	echo "</p>";
	//submit bouton
	echo "<p>";
	echo "<input type='submit' value='Créer la salle' name='createRoom'>";
	echo "</p>";
	echo "</form>";
	echo "</article>";
	echo "</section>";
}
function afficheFormRejoindreRoom(){
	?>
	<section class="form-center">
		<article>
			<h2>Rejoindre une Room</h2>
			<form action="index.php" method="post" autocomplete='off'>
				<p>
					<input class='formInput' type="text" name="idRoom" id="idRoom" placeholder="Id de la Room">
				</p>
				<p>
					<br/><label for='mdp'>(champ optionnel)<label><br/>
					<input autocomplete='new-password' class='formInput' type="text" name="mdp" id="mdp" placeholder="Mot de passe de la Room">
				</p>
				<p>
					<input type="submit" value="Connexion" name="RejoindreRoom">
				</p>
			</form>
		</article>
	</section>
	<?php
}
function afficheFormSettings(){
	?>
	<section class="form-settings">
		<article>
			<h2>Paramètres</h2>
			<form>
				<p>
					<input class='formInput' type="text" placeholder="Id de la Room">
				</p>
				<p>
					<input class='formInput' type="password" placeholder="Mot de passe">
				</p>
			</form>
		</article>
	</section>
	<?php
}
//charge les rooms
function chargerooms($c){
	//requete
	$sql="SELECT * FROM rooms";
	$result=  mysqli_query($c, $sql);

	//on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

//verifie si le nom n'existe pas 
function isroom($rooms,$name){
	$exist=false;
	foreach ($rooms as $id => $roomdata) {
		if ($name==$roomdata["name"]) {
			$exist=true;
		}
	}
	return $exist;
}

function createRoom($c,$name,$password,$hoteID){
	//hash le mdp si il n'est pas vide
	if (trim($password)) {
		$hash=crypt($password,"ddazddfzeà+@Qé&kç6djopv4k8kr");
	}else{
		$hash="";
	}
	//on encode en json la liste des joueurs
	$arrayPlayers=array($hoteID);
	$connectedTime = array($hoteID => time());
	$connectedTime = json_encode($connectedTime);
	$encodedArray = json_encode($arrayPlayers);
	$sql="INSERT INTO `rooms` (`roomid`,`name`,`password`,`players`,`hote`,`idsettings`,`idDataPartie`,`start`,`connectedTime`) VALUES (NULL,'$name','$hash','$encodedArray','$hoteID',0,0,0,'$connectedTime');";
	mysqli_query($c, $sql); //on fait la requetev
	var_dump($sql);
}

function deleteRoom($c,$id){
	$sql="DELETE FROM `rooms` WHERE `roomid` = $id;";
	//var_dump($sql);
	mysqli_query($c, $sql);
}


function get_room_id($c, $room_name){
	$sql="SELECT rooms.roomid FROM rooms WHERE rooms.name = '$room_name';";
	$result =  mysqli_query($c, $sql);
	$row = mysqli_fetch_assoc($result);
	return $row["roomid"];
	
}

function affiche_room_id($c,$room_id){
	$room=recupRoom($room_id,$c);
	$room_name =$room["name"];
	$room_id = $_SESSION["room_id"];
	echo "<h2>Salle: $room_name. ID: $room_id</h2>";

}


function deleteDataPartie($c,$idDataPartie){
	$sql="DELETE FROM `datapartie` WHERE `id` = $idDataPartie;";
	mysqli_query($c, $sql);
}


function createDataPartie($c){

    #on charge les info du hub
    $room = recupRoom($_SESSION["room_id"],$c);
    #on clear la précédente datapartie si besoin
    if ($room["idDataPartie"]!=0){
			deleteDataPartie($c,$room["idDataPartie"]);
	}
    $players = json_decode($room["players"],true);
    #on tire la paire de mots
    $mots = tirerMots($c);
    $mot_membre = $mots[0];
    $mot_undercover = $mots[1];


    
    $votes= array();
    $votesRecus= array();
    $playersmots=array();
    #on répati les undercover
    $membres = array();
    $undercovers = array();
    shuffle($players);
    $nbUndercovers=intdiv(count($players), 3);
    #pour chaque joueur
    foreach ($players as $i => $playerid) {
    	#on répati les roles
    	if ($i+1 <=$nbUndercovers){
    		array_push($undercovers, $playerid);
    	}else{
    		array_push($membres, $playerid);
    	}
    	#on init les votes a 0
    	$votes[$playerid]=False;
    	$votesRecus[$playerid]=0;
    	$playersmots[$playerid]="";
    }
    #on choisi un ordre de jeu et on init le joueur qui joue
    shuffle($players);
    $playerPlaying=$players[0];

    #on encode ce qui doit l'être
    $votes = json_encode($votes);
    $votesRecus = json_encode($votesRecus);
    $playersmots = json_encode($playersmots);
    $membres = json_encode($membres);
    $undercovers = json_encode($undercovers);
    $players = json_encode($players);

    $sql = "INSERT INTO `datapartie` (`id`, `motMembre`, `motUndercover`, `morts`, `undercovers`, `membres`, `avancementPartie`, `listeVoteRecu`, `playersVote`, `playersMots`, `listeOrdredejeu`, `playerPlaying`, `endGameTime`) VALUES (NULL, '$mot_membre', '$mot_undercover', '[]', '$undercovers', '$membres', 'tour', '$votesRecus', '$votes', '$playersmots', '$players', $playerPlaying, 0);";
    mysqli_query($c, $sql);
    
    //on recupère l'id de la partie
    $sql="SELECT datapartie.id FROM datapartie WHERE datapartie.playerPlaying = $playerPlaying;";
	$result =  mysqli_query($c, $sql);
	$id = mysqli_fetch_assoc($result)["id"];
    return $id;
    

}

function tirerMots($c){
    #On tire le couple de mot au hasard

    $sql = "SELECT * FROM mots";
    $result = mysqli_query($c,$sql);
    $row = mysqli_fetch_assoc($result);
	$tab=array();
    while ($row=mysqli_fetch_assoc($result)) {
    	$tab[]=$row;
    }
    $paire=$tab[array_rand($tab,1)];
    $mot_membre = $paire["mot1"];
    $mot_undercover = $paire["mot2"];
    $mots = array($mot_membre,$mot_undercover);

    return $mots;


}

function startGame($c,$id){
	$room = recupRoom($id,$c);
	if (count(json_decode($room["players"],true)) >2){
		if ($room["idDataPartie"]!=0){
			deleteDataPartie($c,$room["idDataPartie"]);
		}
		$idDataPartie = createDataPartie($c);
		$sql= "UPDATE `rooms` SET `start` = '1',`idDataPartie`= $idDataPartie WHERE rooms.roomid = $id;";
		$result =  mysqli_query($c, $sql);
	}
	
}

function removePlayer($c,$id,$roomID){
	// On récupere les infos de la room
	$room=recupRoom($roomID,$c);
	//on la decode
	$IDlist = json_decode($room["players"]);
	//on remove l'id
	$newIdlist=array();
	foreach ($IDlist as $key => $value) {
		if ($value!=$id) {
			array_push($newIdlist, $value);
		}
	}
	//var_dump($newIdlist);

	//on l'encode
	$newIdList=json_encode($newIdlist);
	//on met à jour la data base
	if ($id!=$room["hote"] ){
		$sql= "UPDATE `rooms` SET `players` = '$newIdList' WHERE `rooms`.`roomid` = $roomID;";
	}elseif($id==$room["hote"] && count($newIdlist) >0){
		$newHote=$newIdlist[0];
		$sql ="UPDATE `rooms` SET `players` = '$newIdList', `hote` = '$newHote' WHERE `rooms`.`roomid` = $roomID;";
	}elseif(count($newIdlist)==0){
		//------------------------------------------------------------------------------------------------------------------------
		//il faut aussi delete la datapartie si il y en avait une qui restait (quand tt le monde quitte après la dernière game, la datapartie n'est pas écrasée)
		deleteDataPartie($c,$room["idDataPartie"]);
		deleteRoom($c,$roomID);
	}
	if(count($newIdlist)>0){
		$result =  mysqli_query($c, $sql);
		//on l'enleve du dico connectedTime
		$connectedTime = json_decode($room["connectedTime"],true);
		unset($connectedTime[$id]);
		$connectedTime = json_encode($connectedTime);
		$sql = "UPDATE `rooms` SET `connectedTime` = '$connectedTime' WHERE `rooms`.`roomid` ='$roomID' ";
		$result =  mysqli_query($c, $sql);
	}

	//$_SESSION["room_id"]=-1;
}



function get_players_names($c,$room_id){
	// On récupere la liste des id de joueurs d'une salle
	$sql="SELECT rooms.players FROM rooms WHERE rooms.roomid = '$room_id';";
	$result =  mysqli_query($c, $sql);
	$row = mysqli_fetch_assoc($result);


	$players_id_list = json_decode($row["players"]);

	// On récupere la liste des noms des joueurs avec les id trouvé precedement
	$tab_player_name = array ();
	for ($i=0; $i < count($players_id_list); $i++) { 
		$player_id = intval($players_id_list[$i]);  //Convertion en entier
		$sql="SELECT users.username FROM users WHERE users.id = '$player_id';";
		$result =  mysqli_query($c, $sql);
		$row = mysqli_fetch_assoc($result);

		$tab_player_name[] = $row["username"];
	}

	return $tab_player_name;
	
}

function affiche_players_name_in_room($tab_player_name){
	echo "<section>";
	echo "<ul>";
	for ($i=0; $i < count($tab_player_name) ; $i++) { 
		$player_name = $tab_player_name[$i];
		echo "<li>$player_name</li>";
	}
	echo "</ul>";
	echo "</section>";

}

function rejoindreRoom($c,$idRoom,$password,$idUser){
	$joined = false;
	if (recupRoom($idRoom,$c)!=null){
		//hash le mdp s'il n'est pas vide
		if (trim($password)) {
			$hash=crypt($password,"ddazddfzeà+@Qé&kç6djopv4k8kr");
		}else{
			$hash="";
		}

		$info_room = recupRoom($idRoom,$c);
		//On test si le mot de passe est bon
		$mdp_correct = $hash == $info_room["password"];
		
		//On récupe les membres déjà dans le groupe
		$membresDansGroupe = getMembresDansRoom($c,$idRoom);
		$membres = json_decode($membresDansGroupe);

		
		//si le jouer n'as pas déja rejoint
		if (!in_array($idUser, $membres)){
			array_push($membres,$idUser);
			$new_membres = json_encode($membres);

			if ($mdp_correct == true) { //rajouter $in_game == true
				//on l'ajoute du dico connectedTime
				$connectedTime = json_decode($info_room["connectedTime"],true);
				$connectedTime[$idUser]=time();
				$connectedTime = json_encode($connectedTime);
				$sql = "UPDATE `rooms` SET `players` = '$new_membres',`connectedTime`= '$connectedTime' WHERE `rooms`.`roomid` ='$idRoom' ";
				mysqli_query($c, $sql);
				$joined = true;
			}
		}else{
			$joined=true;
		}
	}
	return $joined;	
}

//Récup une room en particulier
function recupRoom($id,$c){
	//requete
	$sql="SELECT * FROM rooms WHERE rooms.roomid = '$id';";
	$result=  mysqli_query($c, $sql);
	$row = mysqli_fetch_assoc($result);
	return $row;
}

function getMembresDansRoom($c,$idRoom){
	$sql = "SELECT rooms.players FROM rooms WHERE rooms.roomid = '$idRoom';";
	$result =  mysqli_query($c, $sql);
	$row = mysqli_fetch_assoc($result);
	return $row["players"];
}


function checkAndCleanRoom($c,$user){
	$sql = "SELECT rooms.roomid FROM rooms WHERE rooms.hote = '$user';";
	$result =  mysqli_query($c, $sql);
	var_dump($sql);
	$row = mysqli_fetch_assoc($result);
	$id = $row["roomid"];
	deleteRoom($c,$id);
}

?>

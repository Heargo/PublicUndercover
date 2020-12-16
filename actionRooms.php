<?php 

//si on veut créer une salle
if (isset($_POST) && isset($_POST["createRoom"])) {
	$errorInForm=False;
	$rooms=chargerooms($c);
	//on recupère le nom de la salle 
	if (isset($_POST["name"]) && trim($_POST["name"]) && !isroom($rooms,$_POST["name"]) ) {
		$name=htmlspecialchars($_POST["name"],ENT_QUOTES);
		$_SESSION["room_name"] = $name;  // On met le nom de la room dans une variable de session
	}else{
		$errorInForm=True;
	}

	//même chose pour le mot de passe
	$password=$_POST["password"];
	
	if ($errorInForm){
		header("Location: ./index.php?page=play");
	}else{
		$hoteID=$_SESSION["idUser"]; 
		checkAndCleanRoom($c,$_SESSION["idUser"]); // détruit la salle ou l'utilisateur est hote avant d'en créer une nouvelle
		createRoom($c,$name,$password,$hoteID);
		$_SESSION["room_id"] = get_room_id($c, $name);// On met l'id de la room dans une variable de session
		$_SESSION["tab_player_name"] = get_players_names($c,$_SESSION["room_id"]);// On met la liste des nom des joueurs ds la bdd dans une variable de session
		header("Location: ./index.php?page=hub&id=".$_SESSION["room_id"]);
	}


}

//Rejoindre une salle

if (isset($_POST) && isset($_POST["RejoindreRoom"])) {
	$errorInForm=False;
	echo($_POST["RejoindreRoom"]);
	//On récupère l'id de la salle
	if (isset($_POST["idRoom"]) && trim($_POST["idRoom"])) {
		$idRoom = $_POST["idRoom"];
	}else{
		$errorInForm = True;
	}
	$password = $_POST["mdp"];
	$idUser = $_SESSION["idUser"];
	if ($errorInForm) {
		header("Location: ./index.php?page=play");
	}else{
		checkAndCleanRoom($c,$_SESSION["idUser"]); // détruit la salle ou l'utilisateur est hote avant d'en rejoindre une nouvelle
		$joined = rejoindreRoom($c,$idRoom,$password,$idUser);
		if ($joined==True){
			$_SESSION["room_id"]=$idRoom;
			header("Location: ./index.php?page=hub&id=$idRoom");	
		}
		else{
			header("Location: ./index.php?page=play&wantTo=join");
		}
		
	}
}


?>

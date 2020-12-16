<?php 
session_start();
include 'functionRooms.php';
include 'functionDataPartie.php';
include 'functionScore.php';


$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

$players=get_players_names($c,$_SESSION["room_id"]);

$GAME = chargeGame($c,$_SESSION["room_id"]);
$status=$GAME["start"];

$res= array("status" => $status, "players" => afficheHubPlayers($c,$_SESSION["room_id"]));
echo json_encode($res);

//on change le status de la game
if (isset($_POST["status"]) && $_POST["status"]==1) {
	startGame($c,$_POST["roomID"]);
}
if (isset($_POST["leave"]) && $_POST["leave"]==1){
	removePlayer($c,$_SESSION["idUser"],$_POST["roomID"]);
}


//on fait des modifs sur la game (vote/mot ecrit/joueur suivant/fin)

//le joueur choisi son mot
if (isset($_POST["word"]) && trim($_POST["word"]) && $GAME["playerPlaying"]==$_SESSION["idUser"] && $GAME["avancementPartie"]=="tour" ) {
	//on ecrit le mot de l'user dans la base de donnée
	$mots = json_decode($GAME["playersMots"], true);
	var_dump($mots);
	$mots[$_SESSION["idUser"]] = $_POST["word"];
	echo "--";
	var_dump($mots);
	ecritMot($c,$GAME["idDataPartie"],addslashes(json_encode($mots,JSON_UNESCAPED_UNICODE)));
	//c'est au tour du joueur suivant.
	nextPlayer($c,$GAME);
}

//le joueur vote
if (isset($_POST["voted"]) && !$_POST["voted"]) {
	vote($c,$_SESSION["idUser"],$_POST["playerid"],$GAME);
	checkEndVote($c,$_SESSION["room_id"]); 
	checkEndGame($c,$_SESSION["room_id"]);
}



?>

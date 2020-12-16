<?php 

session_start();
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

include 'functionRooms.php';
include 'functionDataPartie.php';

$playerID = $_SESSION["idUser"];
$roomID = $_SESSION["room_id"];


//on charge la room
$DATA = recupRoom($roomID,$c);
if ($DATA["start"]==1){
	$DATA = chargeGame($c,$roomID);
}

//on met a jour
$connectedTime = json_decode($DATA["connectedTime"],true);
$now = time();
$connectedTime[$playerID]=$now;

$connectedTime = json_encode($connectedTime);
$sql = "UPDATE `rooms` SET `connectedTime` = '$connectedTime' WHERE `rooms`.`roomid` ='$roomID' ";
$result =  mysqli_query($c, $sql);


//on re-charge la room pour prendre en compte la modif
var_dump($roomID);
$DATA = recupRoom($roomID,$c);
//var_dump($DATA);
if ($DATA["start"]==1){
	$DATA = chargeGame($c,$roomID);
}
$connectedTime = json_decode($DATA["connectedTime"],true);
//on compare le timestamp précédent
$players=json_decode($DATA["players"]);
//var_dump($players);
foreach ($players as $i => $ID) {

	//si il n'a pas donné signe de vie de puis plus de 20 secondes,
	if ($connectedTime[$ID] +20 < time()) {
		//on l'enlève du hub si la partie n'a pas commencée
		if ($DATA["start"]==0){
			echo "je remove ". $ID;
			removePlayer($c,$ID,$roomID);
		}else{//on le met mort si il y avait une game
			$morts = json_decode($DATA["morts"],true);
			if (!in_array($ID,$morts)){
				$idData =$DATA["idDataPartie"];
				array_push($morts, "$ID");
				$morts = json_encode($morts);
				$sql = "UPDATE `datapartie` SET `morts` = '$morts' WHERE `datapartie`.`id` = $idData ;";
				$result =  mysqli_query($c, $sql);
				//si le joueur qui à déco est celui qui jouais, on passe au joueur suivant
				if ($DATA["playerPlaying"]==$ID){
					nextPlayer($c,$DATA);
				}
			}

		}
	}
}


?>
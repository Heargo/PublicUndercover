<?php 
session_start();
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");
include 'functionDataPartie.php';
include 'functionRooms.php';

$GAME = chargeGame($c,$_SESSION["room_id"]);

if ($GAME["endGameTime"] +10 < time() and $GAME["endGameTime"]!=0 ){
	$data = array('redirect',$_SESSION["room_id"] );
	echo json_encode($data);
}else{
	$data = array('notover');
	echo json_encode($data);
}


?>
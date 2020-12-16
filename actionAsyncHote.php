<?php 
session_start();
include 'functionRooms.php';
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

$room =recupRoom($_POST["roomID"],$c);

if ($room["hote"]==$_SESSION["idUser"]){
			//on affiche le bouton start
			echo "<div class='room-hub-form'>";
			echo "</div>";
			?>
			<button type="button" onclick="startGame();"class="start">START !</button>
			<?php
		}
?>

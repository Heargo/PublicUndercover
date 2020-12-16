<?php 
session_start();
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

if(isset($_POST["changeTheme"]) and isset($_SESSION["userLogedIn"])){
	if (trim($_POST["theme"]) and $_SESSION["userLogedIn"]) {
		$idUser = $_SESSION["idUser"];
		$theme=$_POST["theme"];
		$sql="UPDATE `users` SET `theme` = '$theme' WHERE `users`.`id` = $idUser;";
		$done = mysqli_query($c, $sql); //on fait la requete
		if ($done){
			$_SESSION["theme"]=$_POST["theme"];
		}
	}
}
header("Location: ./index.php?page=settings");
?>

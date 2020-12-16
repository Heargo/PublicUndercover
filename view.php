<!DOCTYPE html>
<html>
<head>
	<title>
		Undercovers
	</title>
	<?php 
	$style="black-green";
	if (isset($_SESSION["theme"]) and trim($_SESSION["theme"])){
		$style=$_SESSION["theme"];
	}
	echo "<link rel='stylesheet' href='css/$style.css' media='screen'/>";
	?>
	<meta charset="UTF-8">
</head>
<body>

<?php
	if (!in_array($page, array("home", "hub", "game"))){
		?>
		<button type="button" onclick="location.href='index.php?page=home'" class="home">Home</button>
		<?php
	}
?>

<?php 
// on affiche la page home
if ($page=="home") {
	include("pages/accueil.php");
}
elseif($page == "compte"){
	include("pages/compte.php");
}
elseif($page == "credits"){
	include("pages/credits.php");
}
elseif($page == "settings"){
	include("pages/settings.php");
}
elseif($page == "help"){
	include("pages/help.php");
}
elseif ($page=="play") {
	
	if(isset($_GET["wantTo"])){
		echo "<div class='form-login-signup'>";
		if($_GET["wantTo"] == "create"){
			afficheFormCreateRoom();
		}
		elseif($_GET["wantTo"] == "join"){
			afficheFormRejoindreRoom();
		}
		echo "</div>";
	}
	else{
		include("pages/play.php");
	}
	
}
elseif ($page=="login") {
	//si on est connect�
	if ($_SESSION["userLogedIn"]) {
		$username=$_SESSION['username'];
		echo "<h2>". $username . "</h2>";
		afficheLogout();
		
	}
	//si on n'est pas connect�
	else{
		
		//et que on a choisi entre sign-up/sign-in
		if (isset($_GET) && isset($_GET["signoption"])) {
			echo "<div class='form-login-signup'>";
			if ($_GET["signoption"]=="in"){
				afficheFormMDP();
			}elseif ($_GET["signoption"]=="up") {
				afficheFormSignUp();
			}
			echo "</div>";
			//on clear les var de sessions
			unset($_SESSION['username']);
			unset($_SESSION['mail']);
			unset($_SESSION['password']);
		}
		
	}
}elseif ($page == "hub"){
	//si le joueur est connect� a la salle a laquelle il veux acc�der
	if ($_SESSION["room_id"]==$_GET["id"]){
		$roomData=recupRoom($_SESSION["room_id"],$c);
		echo "<div class='room-hub-container'><div class='room-form-list'><div class='room-hub-name-id'><h1 class='hub'>HUB</h1>";
		affiche_room_id($c,$_SESSION["room_id"]);
		echo "</div>";
		echo "<div class='room-hub-list'><h2 class='joueurs'>Joueurs</h2><ul id='playersList'></ul></div>";		
		echo "</div>";
		echo "</div>";
		
		?>
		<button type="button" class="leave-button" onclick="playerLeave();">Quitter</button>
		<?php
		echo "<script src='./js/jsAsyncDeco.js'></script>";
		echo "<script src='./js/jsAsyncPlayers.js'></script>";
	}
	//sinon on le redirige vers play
	else{
		header("Location: ./index.php?page=play");
	}
}
elseif ($page=="game") {
	echo "<div class='screen-game-container'>";
	echo "<div class='game-container'><div class='before-element'></div></div>";
	//champ pour entrer un mot
	?>
	
	<form id="word-form">
		<input type="text" name="word" maxlength="30" autocomplete="off" placeholder="Votre mot...">
		<input type="submit" name="choseWord">
	</form>

	<?php
	echo "</div>";
	echo "<script src='./js/jsAsyncDeco.js'></script>";
	echo "<script src='./js/jsAsyncGame.js'></script>";
}

//on redirigre sur le menu si l'utilisateur n'est pas connect� mais qu'il est sur un page qui necessite une connection
if(isset($_SESSION["userLogedIn"]) && !$_SESSION["userLogedIn"]){
		if ($page=="game" || $page=="play" || $page=="hub" || $page=="compte"){
			header("Location: ./index.php?page=home");
		}
}

?>

</body>
</html>
<!-- Icons made by <a href="http://www.freepik.com/" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon"> www.flaticon.com</a> -->
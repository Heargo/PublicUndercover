<?php
//initialise la session comme non login
if (!isset($_SESSION["userLogedIn"])){
	$_SESSION["userLogedIn"]=false;
	$_SESSION["username"]="";
}

//initialise la page ou la défini en fonction du choix de l'utilisateur
if (isset($_GET) && isset($_GET["page"])){
	$page=$_GET["page"];
}else{
	$page="home";
}

//si on veut se connecter
if (isset($_POST) && isset($_POST["wantToLog"]) && $_POST["wantToLog"]=="Login") {
	//on charge les users
	$users=chargeusers($c);
	$errorInForm=False;
	//on recupère l'username et on vérifie si il n'y a pas d'erreurs
	if (isset($_POST["username"]) && trim($_POST["username"]) && isuser($users,$_POST["username"])) {
		$_SESSION["username"]=htmlspecialchars($_POST["username"],ENT_QUOTES);
	}else{
		$_SESSION["username"]="error";
		$errorInForm=True;
	}

	//même chose pour le mot de passe
	if (!$errorInForm &&isset($_POST["password"]) && trim($_POST["password"]) && password_verify($_POST["password"], getpassword($users,$_SESSION["username"]))) {
		$_SESSION["userLogedIn"]=true;
		$_SESSION["idUser"]=getUserID($c,$_SESSION["username"]);
		$_SESSION["theme"]=getTheme($c,$_SESSION["idUser"]);
		header("Location: ./index.php?page=home");
	}else{
		$_SESSION["password"]="error";
		$errorInForm=True;
	}
	
	if ($errorInForm){
		var_dump(isuser($users,$_POST["username"]));
		var_dump(password_verify($_POST["password"], getpassword($users,$_SESSION["username"])));
		header("Location: ./index.php?page=login&signoption=in&error=true");
	}


}

//si on veut se creer un compte
if ( isset($_POST) && isset($_POST["wantToLog"]) && $_POST["wantToLog"]=="SignUp") { 
	$users=chargeusers($c);
	$errorInForm=False;
	//on recupère l'username et on vérifie si il n'y a pas d'erreurs
	if (isset($_POST["username"]) && trim($_POST["username"]) && !isuser($users,$_POST["username"])) {
		$_SESSION["username"]=htmlspecialchars($_POST["username"],ENT_QUOTES);
	}else{
		$_SESSION["username"]="error";
		$errorInForm=true;
	}

	//même chose pour le mail
	if (isset($_POST["mail"]) && trim($_POST["mail"]) && preg_match(" /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $_POST["mail"]) && mailIsValid($users,$_POST["mail"])) {
		$_SESSION["mail"]=htmlspecialchars($_POST["mail"],ENT_QUOTES);
	}else{
		$_SESSION["mail"]="error";
		$errorInForm=true;
	}

	//même chose pour le mot de passe
	if (isset($_POST["password"]) && trim($_POST["password"])) {
		// on hash le mdp
		$hash=crypt($_POST["password"],"XXXXSALTXXXX");
		$_SESSION["password"]=$_POST["password"];
	}else{
		$_SESSION["password"]="error";
		$errorInForm=true;
	}

	//si les champs sont valides
	var_dump($errorInForm);
	if (!$errorInForm) {
		//on ajoute l'user
		insertUser($c,$_SESSION["username"],$_SESSION["mail"],$hash);
		//on detruit les variables de sessions inutiles
		unset($_SESSION['mail']);
		unset($_SESSION['password']);
		//on reviens sur la page login, connecté 
		$_SESSION["userLogedIn"]=false;
		header("Location: ./index.php?page=login&signoption=in");
		
	}
	else{
		header("Location: ./index.php?page=login&signoption=up&error=true");
	}
		
}

//si on veut se déconnecter
if (isset($_GET) && isset($_GET["logout"]) && $_GET["logout"] == "true") {
		$_SESSION["userLogedIn"]=false;
		$_SESSION["username"]="";
		$_SESSION["idUser"]=-1;
		unset($_SESSION["theme"]);
		header("Location: ./index.php");
}
?>

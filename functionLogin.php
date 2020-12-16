<?php
//charge les utilisateurs
function chargeusers($c){
	//requete
	$sql="SELECT * FROM users";
	$result=  mysqli_query($c, $sql);

	//on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

//verifie si user existe 
function isuser($users,$user){
	$exist=false;
	foreach ($users as $id => $userdata) {
		if ($user==$userdata["username"]) {
			$exist=true;
		}
	}
	return $exist;
}

function getUserID($c,$user){
	$sql="SELECT users.id FROM users WHERE users.username = '$user';";
    $result =  mysqli_query($c, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row["id"];
}

function getUserMail($c,$id){
	$sql="SELECT users.mail FROM users WHERE users.id = '$id';";
    $result =  mysqli_query($c, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row["mail"];
}

function getTheme($c,$id){
	$sql="SELECT users.theme FROM users WHERE users.id = '$id';";
    $result =  mysqli_query($c, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row["theme"];
}

//verifie si l'adresse mail n'est pas déjà utilisée
function mailIsValid($users,$mail){
	$dontexist=true;
	foreach ($users as $id => $userdata) {
		if ($mail==$userdata["mail"]) {
			$dontexist=false;
		}
	}
	return $dontexist;
}

//recupère le password de user
function getpassword($users,$user){
	foreach ($users as $id => $userdata) {
		if ($user==$userdata["username"]) {
			$password=$userdata["password"];
		}

	}
	return $password;
}

function insertUser($c,$username,$mail,$hash){
	$sql="INSERT INTO `users` (`id`,`username`,`mail`,`password`, `score`,`theme`) VALUES (NULL,'$username','$mail','$hash',0,'black-green');";
	var_dump($sql);
	mysqli_query($c, $sql); //on fait la requete

}

function afficheUserChamp($login){
	echo "<p>";
	//si il y a une erreur dans le champ
	if (isset($_SESSION["username"]) && $_SESSION["username"]=='error'){
		if ($login){
			echo "<p>Username inconnu</p>";
		}else{
			echo "<p>Username déjà utilisé</p>";
		}
		echo "<input class='formInput errorFormulaire' type='text' name='username' id='username' placeholder='Username'>";
	}
	//si c'est la première fois que la page est chargée
	elseif (!isset($_SESSION["username"])) {
		echo "<input class='formInput' type='text' name='username' id='username' placeholder='Username'>";
	}
	//si la champs est valide
	else{
		echo "<input class='formInput' type='text' name='username' id='username' placeholder='Username' value=".$_SESSION["username"].">";
	}
	echo "</p>";
}

function afficheMailChamp(){
	echo "<p>";
	//si il y a une erreur dans le champ
	if (isset($_SESSION["mail"]) && $_SESSION["mail"]=='error'){
		echo "<p>Mail invalide ou déjà utilisé</p>";
		echo "<input class='formInput errorFormulaire' type='mail' name='mail' id='mail' placeholder='Mail'>";
	}
	//si c'est la première fois que la page est chargée
	elseif (!isset($_SESSION["mail"])) {
		echo "<input class='formInput' type='mail' name='mail' id='mail' placeholder='Mail'>";
	}
	//si la champs est valide
	else{
		echo "<input class='formInput' type='mail' name='mail' id='mail' placeholder='Mail' value=".$_SESSION["mail"].">";
	}
	echo "</p>";

}

function affichePasswordChamp(){
	echo "<p>";
	//si il y a une erreur dans le champ
	if (isset($_SESSION["password"]) && $_SESSION["password"]=='error'){
		echo "<p>Mot de passe incorret</p>";
		echo "<input class='formInput errorFormulaire' type='password' name='password' id='password' placeholder='Password'>";
	}
	//si c'est la première fois que la page est chargée
	elseif (!isset($_SESSION["password"])) {
		echo "<input class='formInput' type='password' name='password' id='password' placeholder='Password'>";
	}
	//si la champs est valide
	else{
		echo "<input class='formInput' type='password' name='password' id='password' placeholder='Password' value=".$_SESSION["password"].">";
	}
	echo "</p>";
}


function afficheFormSignUp(){
	echo "<section class='form-center'>";
	echo "<article>";
	echo "<h2>Sign up</h2>";
	echo "<form action='index.php' method='post'>";
	//user
	afficheUserChamp(false);
	//mail
	afficheMailChamp();
	//password
	affichePasswordChamp();
	//submit bouton
	echo "<p>";
	echo "<input type='submit' value='SignUp' name='wantToLog'>";
	echo "</p>";
	echo "</form>";
	echo "</article>";
	echo "</section>";
}


function afficheFormMDP(){
	echo "<section class='form-center'>";
	echo "<article>";
	echo "<h2>Login</h2>";
	echo "<form action='index.php' method='post'>";
	//user
	afficheUserChamp(true);
	//password
	affichePasswordChamp();
	//submit bouton
	echo "<p>";
	echo "<input type='submit' value='Login' name='wantToLog'>";
	echo "</p>";
	echo "</form>";
	echo "</article>";
	echo "</section>";
}




?>
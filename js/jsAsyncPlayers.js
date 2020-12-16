function $_GET(param) {
	var vars = {};
	window.location.href.replace( location.hash, '' ).replace( 
		/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
		function( m, key, value ) { // callback
			vars[key] = value !== undefined ? value : '';
		}
	);

	if ( param ) {
		return vars[param] ? vars[param] : null;	
	}
	return vars;
}

function playerExist(player){
	res=false;
	var lis = document.getElementsByClassName("player");
	for (const li of lis){
		if (player==li.innerHTML){
			res=true;
		}
	}
	return res;
}

function clearPlayers(players){
	var lis = document.getElementsByClassName("player");
	for (const li of lis){
		if (players.indexOf(li.innerHTML) < 0){
			li.remove();
		}
	}
}

function startGame(){
	var xhr = new XMLHttpRequest();
	xhr.open("POST","./actionAsync.php",true);
	xhr.responseType="json"
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xhr.send("status=1&roomID="+$_GET('id'));
	console.log("touché");
}

function playerLeave(){
	var xhr = new XMLHttpRequest();
	xhr.open("POST","./actionAsync.php",true);
	xhr.responseType="json"
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xhr.send("leave=1&roomID="+$_GET('id'));
	window.location.replace("./index.php?page=home")
}

function checkHost(){
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function(){
		if (this.readyState ==4 && this.status ==200) {
			if (document.getElementsByClassName('room-hub-form').length ==0) {
				var element= document.getElementsByClassName("room-hub-list")[0];
				element.insertAdjacentHTML('afterend', this.response);
		}
		}
		else if (this.readyState ==4){
			console.log("ERREUR");
			console.log(this);
		}
	};

	xhr.open("POST","./actionAsyncHote.php",true);
	xhr.responseType="text";
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xhr.send("roomID="+$_GET('id'));
	
}

function loadplayers(){
	var xhr = new XMLHttpRequest();
	var ul = document.getElementById("playersList");
	xhr.onreadystatechange = function(){
		if (this.readyState ==4 && this.status ==200) {
			//on met à jour la liste des joueurs
			console.log(this.response);
			for (const player of this.response["players"]) {
			  	if (!playerExist(player)){
				  	var li = document.createElement("li");
					li.classList.add("player");
					li.appendChild(document.createTextNode(player));
					ul.appendChild(li);
				}
			}
			//on supprime les joueurs qui ne sont pas dans le hub
			clearPlayers(this.response["players"]);
			//on redirige si la partie a commencé et qu'on est sur le hub
			if (this.response["status"]==1 && $_GET('page')=="hub") {
				window.location.replace("./index.php?page=game&id="+$_GET('id'));
			}
			
			
		}
		else if (this.readyState ==4){
			console.log("ERREUR");
			console.log(this);
		}
	};

	xhr.open("POST","./actionAsync.php",true);
	xhr.responseType="json"
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xhr.send("roomID="+$_GET('id'));

}

//au chargement de la page
loadplayers();
checkHost();

//boucle toute les 1 sec
setInterval(function(){
    loadplayers() 
    checkHost()
}, 1000);

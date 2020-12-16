function sendAlive(){
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if (this.readyState ==4 && this.status ==200) {
			console.log(this.response);
		}
		else if (this.readyState==4){
			console.log("ERREUR");
			console.log(this);
		}
	};

	xhr.open("GET","./actionAsyncAlive.php",true);
	xhr.responseType="text"
	xhr.send();

}

//au chargement de la page
sendAlive();

//boucle toute les 1 sec
setInterval(function(){
    sendAlive(); 
}, 1000);
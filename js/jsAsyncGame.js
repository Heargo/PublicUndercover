function loadGameScreen(){
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if (this.readyState ==4 && this.status ==200) {
			if (document.getElementsByClassName("data-container").length == 1 ){
				var element= document.getElementsByClassName("data-container")[0];
				element.remove();
			}
			var element= document.getElementsByClassName("before-element")[0];
			element.insertAdjacentHTML('afterend', this.response);
		}
		else if (this.readyState ==4){
			console.log("ERREUR");
			console.log(this);
		}
	};

	xhr.open("GET","./actionDataPartie.php",true);
	xhr.responseType="text"
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded")
	xhr.send();

}

function eventEntry(){
	//on ajoute un event listener au bouton submit du formulaire
	document.getElementById("word-form").addEventListener("submit",function(e){

		e.preventDefault();
		var data = new FormData(this);
		//data = decodeURI(data);
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(){
			if (this.readyState ==4 && this.status ==200) {
				console.log(this.response);
			}
		};

		xhr.open("POST","./actionAsync.php",true);
		xhr.responseType="text"
		xhr.send(data);


	return false
	});

}

function vote(voted,playerid){
	if(!voted){
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function(){
			// if (this.readyState ==4 && this.status ==200) {
			// 	console.log(this.response);
			// 	console.log(voted,playerid)
			// 	console.log("j'ai voté");
			// }
		};

		xhr.open("POST","./actionAsync.php",true);
		xhr.responseType="text";
		xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xhr.send("voted="+voted+"&playerid="+playerid);
	}

}


function checkRedirect(){
	var xhr = new XMLHttpRequest();
	console.log("try")
	xhr.onreadystatechange = function(){

		if (this.readyState ==4 && this.status ==200) {
			console.log(this.response[0])
			if (this.response[0]=="redirect"){

				window.location.replace("./index.php?page=hub&id="+this.response[1]);
			}

		}
		else if (this.readyState ==4){
			console.log("ERREUR");
			console.log(this);
		}
	};

	xhr.open("GET","./actionTime.php",true);
	xhr.responseType="json";
	xhr.send();
}

//au chargement de la page
loadGameScreen();
eventEntry();
checkRedirect();
//boucle toute les 1 sec
setInterval(function(){
    loadGameScreen() 
    checkRedirect();
}, 1000);
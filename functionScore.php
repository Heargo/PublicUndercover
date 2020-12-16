<?php 

function chargePlayersByScore($c){
	$sql="SELECT * FROM users ORDER BY score DESC";
	//var_dump($sql);
    $result =  mysqli_query($c, $sql);
    //on met dans un tableau
	$tableau = [];
	while ($row=mysqli_fetch_assoc($result)) {
		$tableau[] = $row;
	}
	return $tableau;
}

function afficheHubPlayers($c,$idRoom){
	$users=chargePlayersByScore($c);
	$players=getMembresDansRoom($c,$idRoom);
	$players = json_decode($players,true);

	$tableau = [];
	foreach ($users as $i => $player) {
		if (in_array($player["id"], $players)){
			$score = json_decode($player["score"],true)["score"];
			$tableau[]= "".$player["score"] ." ". $player["username"] ."";
		}
	}
	return $tableau;
}

function recupScorePlayer($c,$idPlayer){
    $users=chargePlayersByScore($c);

    foreach ($users as $i => $player) {
        if ($player["id"]==$idPlayer){
            $score = $player["score"];
        }
    }
    return $score;
}
function afficheTop10Players($c){
	$users =chargePlayersByScore($c);
	echo "<div class='top10'>";
	echo "<h2>Top 10 des meilleurs joueurs</h2>";
	echo "<hr class='rounded'><ul>";
	for ($i=0;$i<10;$i++){
		echo "<li>" . $users[$i]["score"] . " - " . $users[$i]["username"] . "</li>";
	}
	echo "</ul></div>";
}

?>
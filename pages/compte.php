<div class="center_main">
	<div class="informations">
	    <h1>Informations</h1>
	    <hr class="rounded">
	    <?php 
	    echo "<p>Pseudo : " . $_SESSION["username"] . "</p>";
	    echo "<p>Mail : " . getUserMail($c,$_SESSION["idUser"]). "</p>";
	    echo "<p>Votre score : " . recupScorePlayer($c,$_SESSION["idUser"]) . "</p>";
	    ?>
	</div>
<?php 
afficheTop10Players($c);
?>
</div>



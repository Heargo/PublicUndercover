<div class="top">
	<?php
	if(isset($_SESSION["userLogedIn"])){
		if ($_SESSION["userLogedIn"]){
			?>
			<button type="button" onclick="location.href='index.php?logout=true'" class="login">Logout</button>
			<button type="button" onclick="location.href='index.php?page=compte'" class="compte-signup">Compte</button>
			<?php
		}
		else{
			?>
			<button type="button" onclick="location.href='index.php?page=login&signoption=in'" class="login">Login</button>
			<button type="button" onclick="location.href='index.php?page=login&signoption=up'" class="compte-signup">Sign-Up</button>
			<?php
		}
	}
	?>
	</div>
	
	<div class="center_main">
		<img src="images/spy.png">
		<?php 	
		if(isset($_SESSION["userLogedIn"]) && $_SESSION["userLogedIn"]){
		?>
		<button type="button" onclick="location.href='index.php?page=play'" class="play">PLAY</button>
		<?php 
		}
		?>
	</div>

	<div class="bottom">
	<button type="button" onclick="location.href='index.php?page=help'"class="help">Aide</button>
	<?php
	if(isset($_SESSION["userLogedIn"])){
		if ($_SESSION["userLogedIn"]){
		?>
		<button type="button" onclick="location.href='index.php?page=settings'" class="settings"><img src="images/settings.png"></button>
		<?php
		}
	}
	?>
	
	<button type="button"onclick="location.href='index.php?page=credits'" class="credits">Crédits</button>
</div>

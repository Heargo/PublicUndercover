<?php
// visialiser erreur
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//connection
session_start();
//on se connecte qui si on est un user
$c = mysqli_connect("X", "X", "X", "X");
mysqli_set_charset($c, "utf8");

include("functionLogin.php");
include("functionRooms.php");
include("functionScore.php");
include("actionLogin.php");
include("actionRooms.php");
include("view.php");

?>

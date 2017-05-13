<?php header('Content-Type: text/html; charset=UTF-8');
 ?>
	<head>
		<title>Importation de cartes créées par des contributeurs d'Innovalangues ou des collègues</title>
	</head>
	<body>
<?php
error_reporting(E_ALL);//error_reporting(0); désactiver
ini_set('display_errors', '1');
require('./models/card.class.php');
include_once("./provided_cards.php");
if(file_exists('./enregistrements/exported_cards.php')){
	include_once("./enregistrements/exported_cards.php");
}
else{
	echo "No user cards to import (add them to './exported_cards.php', if you have some).";
}
?>

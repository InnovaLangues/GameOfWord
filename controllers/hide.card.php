<?php
	session_start();
	require_once("../sys/db.class.php");
	require_once("../models/user.class.php");
	$user = user::getInstance();
	if(($user->id == $_GET['user_id']) && isset($_GET['card_id'])){
		$db = db::getInstance();
		$db->query("UPDATE `GoW`.`cartes` SET `dateSuppression` = CURRENT_TIMESTAMP, `idEraser` = '".$user->id."' WHERE `cartes`.`idCarte` = '".$_GET['card_id']."';");
		if($db->affected_rows() == 1){
			echo "OK";
		}
		else{
			echo $db->affected_rows()." enregistrements affectés (≠1, pourquoi?)";
		}
	}
	else{
		print_r($lang);
		echo $user->id." ≠ ".$_GET['user_id']." : Problème de connexion, vous n'êtes plus vous-même…";
	}
?>
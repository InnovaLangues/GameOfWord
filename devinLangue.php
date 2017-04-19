<?php
	session_start();
	require_once("models/user.class.php");
	require_once("sys/db.class.php");
	require_once('./controllers/notificationMessage.php');
	require_once('./models/userlvl.class.php');
	require_once('./languages/language.php');
	require_once('./sys/load_iso.php');
	$lang_iso = new IsoLang();

	$_SESSION["langDevin"] = $_POST["lang"];

	$user = user::getInstance();
	$userlang = $user->langGame;
	$id = $user->id;

	$sql = 'SELECT * FROM `score` WHERE langue="'.$lang_iso->french_for($_SESSION["langDevin"]).'" AND userid='.$id;

	$db = db::getInstance();
	$resultat=$db->query($sql);

	if (!$resultat->num_rows>0){
		$score=0;
		$sql = "INSERT INTO `score`(`userid`, `scoreGlobal`, `scoreOracle`, `scoreDruide`, `scoreDevin`, `langue`) VALUES ($id,$score,$score,$score,$score,'".$lang_iso->french_for($_SESSION["langDevin"])."')";

		$db->query($sql);
	}


	$mess = "Diviner : ".$lang["languePlay"].$lang_iso->french_for($_SESSION["langDevin"]);


	$notif = new Notification();
	$notif->addNotifGAME($user->id,$mess,"./profil/diviner.jpg");

echo $_SESSION["langDevin"];
?>

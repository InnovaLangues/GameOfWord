<?php
	session_start();
	require_once("models/user.class.php");
	require_once("sys/db.class.php");
	require_once('./controllers/notificationMessage.php');
	require('./models/userlvl.class.php');
	require('./languages/language.php');
	require_once('./sys/load_iso.php');
  $lang_iso = new IsoLang();

	$user = user::getInstance();

	$sql = "UPDATE user SET userlang_game ='"
			. $_GET['userlang_game']
			. "' WHERE userid = '" . intval($user->id)."'";
	$db = db::getInstance();
	echo $sql;
	if ($db->query($sql)){
		echo "update succed";

		$mess = $_GET['page_not']." : ".$lang["languePlay"].$lang_iso->french_for($_GET['userlang_game']);
		$role = strtolower($_GET['page_not']);
		$role = "profil/".$role;

		$notif = new Notification;
		$notif->initNotif();
		$notif->addNotifGAME($user->id,$mess,$role);


	} else {
		echo "update fails";
	}
	$_SESSION["langDevin"] = $_GET['userlang_game'];
?>

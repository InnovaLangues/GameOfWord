<?php
	session_start();
	require_once("models/user.class.php");
	require_once("sys/db.class.php");
	require_once('./controllers/notificationMessage.php');
	require('./models/userlvl.class.php');
	require('./languages/language.php');
	$user = user::getInstance();
	$db = db::getInstance();
	if(isset($_GET['page_not'])){
		$role = "profil/".strtolower($_GET['page_not']);
		if(isset($_GET['userlang_game'])){
			require_once('./sys/load_iso.php');
			$lang_iso = new IsoLang();
			$sql =  "UPDATE user SET userlang_game ='"
					. $_GET['userlang_game']
					. "' WHERE userid = '" . intval($user->id)."'";
			echo $sql;
			if ($db->query($sql)){
				echo "update succed";

				$mess = $_GET['page_not']." : ".$lang["languePlay"].$lang_iso->french_for($_GET['userlang_game']);
				$notif = new Notification;
				$notif->initNotif();
				$notif->addNotifGAME($user->id,$mess,$role);
			}
			else {
				echo "update fails";
			}
			$_SESSION["langDevin"] = $_GET['userlang_game'];
		}
		elseif (isset($_GET['game_lvl'])) {
			$sql =  "UPDATE `user` SET `userlvl` ='"
					. $_GET['game_lvl']
					. "' WHERE userid = '" . intval($user->id)."'";
			echo $sql;
			if ($db->query($sql)){
				echo "update success";

				$mess = $_GET['page_not']." : ".$lang["levelChange"].$lang["level_".$_GET['game_lvl']]."→".$lang[$_GET['page_not']."_".$_GET['game_lvl']];

				$notif = new Notification;
				$notif->initNotif();
				$notif->addNotifGAME($user->id,$mess,$role);
			}
			else {
				echo "update fails";
			}
			$_SESSION["langDevin"] = $_GET['userlang_game'];
		}
		else{
			echo "no query...";
		}
	}
	else{
		echo "missing origin…";
	}
?>

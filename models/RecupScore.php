<?php
	//don't really know what this is for… will see later…
	function score($role){
		require_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();

		$db = db::getInstance();
		$user = user::getInstance();
		$langue = $user->langGame;

		if(isset($_SESSION["langDevin"]) && $_SESSION["langDevin"]!=""){
			$langue = $_SESSION["langDevin"];
		}

		$roleUt = "score_".$role;

		$sql = 'SELECT `'.$roleUt.'` FROM `stats` WHERE `userid`="'.$user->id.'" AND langue="'.$lang_iso->french_for($langue).'"';

		$resultat = 'err';
		if($db->query($sql)){
			$resultat = $db->fetch_assoc($res);
			$resultat = $resultat[$roleUt];
		}

		return $resultat;

	}
?>

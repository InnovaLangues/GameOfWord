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

		$roleUt = "score".$role;

		$sql = 'SELECT '.$roleUt.' FROM `score` WHERE `userid`="'.$user->id.'" AND langue="'.$lang_iso->french_for($langue).'"';

		$res = $db->query($sql);
		$resultat = mysqli_fetch_assoc($res);

		return $resultat[$roleUt];

	}
?>

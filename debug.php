<?php
//user.class.php
//print_r($this->get_lang_lvl("es").($this->get_lang_lvl("sdfjlkkjls")===false ? "yippie" :"argh"));

/*include("./controllers/update_score_coeff.php");
$gh= new GameHandler();
echo $gh->get_mastery_mult("C1","Débutant");

require_once("./sys/load_iso.php");
$lang_iso = new IsoLang();
echo "<pre>".
  $lang_iso->language_code_for("Espagnol")."→".$lang_iso->french_for("es")."\n".
  $lang_iso->language_code_for("Avestique")."→".$lang_iso->french_for("ae")."\n".
  $lang_iso->language_code_for("sdf,lmsgdlk,")."→".$lang_iso->french_for("sdee")."\n".
  "</pre>";
  print_r($lang_iso->get_all_codes());
*/
function myLog($txt){
	$fich = fopen("./debug.html","a");
   fwrite($fich, "<p>".$txt."</p>");
   fclose($fich);
}

function logScores($user, $language=false,$title=false){
	require_once("./models/score.class.php");
	if($language !== false){
		$score = new GlobalScoreTable($user);
	}
	else{
		$score = new ScoreTable($user, $language);
	}
	$score->get_score_table(false);
	$fich = fopen("./debug.html","a");
	if($title !== false){
		fwrite($fich, "<h2>$title</h2>\n");
	}
   fwrite($fich, $score);
   fclose($fich);
}
/*  fwrite($fich, "blabla".$fileName."\n".$ext);
  fclose($fich);
  require_once("./sys/db.class.php");
  $db = db::getInstance();
  $query = "SET @id_enr := null;
UPDATE `parties` SET  `reussie`='non', `enregistrementID` = (SELECT @id_enr := `enregistrementID`) WHERE idDevin='4' ORDER BY tpsDevin DESC LIMIT 1;
SELECT @id_enr;";
  $db->multi_query_last_result($query);
  $record_id = $db->fetch_assoc()["@id_enr"];
  //$record_id = $record_id["@id_enr"];
  echo "<h1>$record_id</h1>";*/
  //$score = array(true => "youpiii", false => "perduuuu");
  //echo $score[true]."/".$score[false];
?>

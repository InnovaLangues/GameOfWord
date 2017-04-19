<?php
function myLog($txt){
	$fich = fopen("./debug.html","a");
   fwrite($fich, "<p>".$txt."</p>");
   fclose($fich);
}

function myConsole($xt){
	echo "<script>console.log($this->query);</script>";
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
?>

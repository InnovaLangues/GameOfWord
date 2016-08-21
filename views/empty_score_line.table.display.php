<?php /* Global $scoreLine sent by ScoreLine class (controllers/score.class.php) → $res used then in ScoreLine class*/
if($scoreLine->highlight){
	$class = " class='highlight'";
}
else{
	$class = "";
}
$res = "<tr$class><td colspan='6'>…</td></tr>";
?>

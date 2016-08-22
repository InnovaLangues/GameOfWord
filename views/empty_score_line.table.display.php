<?php /* Global $scoreLine sent by ScoreLine class (controllers/score.class.php) → $res used then in ScoreLine class*/
if($scoreLine->highlight){
	$class = " class='highlight'";
}
else{
	$class = "";
}
if($scoreLine->isGlobal()){
	$width = 7;
}
else{
	$width = 6;
}
$res = "<tr$class><td colspan='$width'>…</td></tr>";
?>

<?php /* Global $scoreLine sent by ScoreLine class (controllers/score.class.php) → $res used then in ScoreLine class*/
if($scoreLine->highlight){
	$class = " class='highlight'";
}
else{
	$class = "";
}
$res = "<tr$class>".
		"<td>".$scoreLine->get_position()."</td>".
		"<td style = 'font-weight:bold;' >".$scoreLine->get_user_name()."</td>".
		"<td>".$scoreLine->get_oracle_forfeits()."</td>".
		"<td>".$scoreLine->get_oracle_errors()."</td>".
		"<td>".$scoreLine->get_oracle_recordings()."</td>".
		"<td>".$scoreLine->get_oracle_perc()."</td>".
		"<td>".$scoreLine->get_oracle_score()."</td>".
		"<td>".$scoreLine->get_druid_arbitration()."</td>".
		"<td>".$scoreLine->get_druid_arbitration_err()."</td>".
		"<td>".$scoreLine->get_druid_cards()."</td>".
		"<td>".$scoreLine->get_druid_score()."</td>".
		"<td>".$scoreLine->get_augur_recordings()."</td>".
		"<td>".$scoreLine->get_augur_perc()."</td>".
		"<td>".$scoreLine->get_augur_score()."</td>".
		"<td style = 'text-decoration:italics' >".$scoreLine->get_global_score()."</td>".
		"<td>".$scoreLine->get_language_list()."</td>".
		"</tr>";
?>

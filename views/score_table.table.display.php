<?php
if(!isset($lang)){
	include('./languages/language.php');
}
$res = "<table class='table table-hover sortable' id='Score".$scoreTable->get_language()."'>
	<thead>
		<tr>
			<th class='header'>".$lang['classement']."</th>
			<th class='header'>".$lang['userName']."</th>
			<th><span class='header'>".$lang['score_oracle']."</span><br /><br />".$lang['abandons']."</th>
			<th>".$lang['erreurs']."</th>
			<th>".$lang['enr_deposes']."</th>
			<th>".$lang['w-l-perc']."</th>
			<th>".$lang['score']."</th>
			<th><span class='header'>".$lang['score_druid']."</span><br /><br />".$lang['arbitrages']."</th>
			<th>".$lang['err_arb']."</th>
			<th>".$lang['carte_crees']."</th>
			<th>".$lang['score']."</th>
			<th><span class='header'>".$lang['score_diviner']."</span><br /><br />".$lang['parties']."</th>
			<th>".$lang['w-l-perc']."</th>
			<th>".$lang['score']."</th>
			<th class='header'>".$lang['score_global']."</th>
		</tr>
	</thead>
	<tbody>";
		foreach($scoreTable->the_scores as $score){
			$res .= $score."\n";
		}
$res .= '	</tbody>
</table>';
?>

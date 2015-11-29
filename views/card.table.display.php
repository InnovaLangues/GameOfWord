<?php /* Global $card sent by Card class (models/card.class.php) → $res used then in Card class*/
$res =   "
				<thead>
					<tr>
						<td>
							<p>".$card->get_word()."<p>
						</td>
					</tr>
				</thead>
				<tbody style='background-color:#F8E1E8;'>";
foreach($card->get_forbidden_words() as $word){
				$res .= "
					<tr>
						<td>
							<p><img src='style/default.css/imgs/forbidden.png'></p><p>$word</p><br/>
						</td>
					 </tr>";
}
$res .= "
				</tbody>";
?>
<?php /* Global $card sent by Card class (models/card.class.php) → $res used then in Card class*/
include('./languages/language.php');
require_once('./controllersJS/card.inline.admin.js');
include_once('./views/card.inline.admin.display.style.html');

$res = "<div  class='lacarte' id='carte".$card->get_id()."'>
			<div>
				<div class='niveauMin'>".$card->get_level()."</div>
				<div class='lalangue'>ID&nbsp;: ".$card->get_id()." (".$card->get_lang().")</div>
				<div class='close' data-user='".$user->id."' data-id='".$card->get_id()."'>×</div>
				<!--div class='categoriecat'>".$card->get_cat()."</div-->
				<div class='thethemes'>
					<header>".$lang['theme']."&nbsp;:</header><div>";
					foreach($card->get_themes() as $theme){
						$res .= "<span>$theme</span>";
					}
				$res.="</div></div>			
			</div>

			<div>
				<div class='motCleCle motVedette'>
					<header>".$lang['word_to_find']."&nbsp;:</header>".$card->get_word().
				"</div>
				<div class='tabous'>
					<header>".$lang["wordForbid"]."&nbsp;:</header>";
					foreach($card->get_forbidden_words() as $word){
						$res .= "<div class='motCleCle'>$word</div>\n";
					}
				$res .= "</div>".
						"<div class='overlay-loader'>
							<div class='loader'>
								<div></div>
								<div></div>
								<div></div>
								<div></div>
								<div></div>
								<div></div>
								<div></div>
							</div>
						</div>".
			"</div>
			<!--div>TODO
				<div class='auteur'>".$card->get_author()." (".$card->get_time().")</div>
			</div-->
		</div>";
?>
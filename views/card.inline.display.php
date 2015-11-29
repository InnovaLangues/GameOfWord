<?php /* Global $card sent by Card class (models/card.class.php) → $res used then in Card class*/
include('./languages/language.php');
echo "<style>.lacarte{margin-bottom:1em;padding:3px; position:relative;background-color:lightgrey;;display: flex;flex-direction: column;justify-content:space-between;width:250px;margin-right:auto;margin-left:auto}
	   .lacarte div{margin:0;padding-bottom:4px; padding-top:4px; padding-left:1em; padding-right:1em;}
	   .lacarte>div{padding:0;}
	   .lalangue{font-weight:bold; background-color:grey;color:white;}
	   .niveauMin{text-align: right; color:white; position:absolute;top:3px;right:3px; background-color:black}
	   .categoriecat{background-color:white; margin-bottom:3px}
	   .motCleCle{text-align:center;font-size:larger;}
	   .motVedette{font-weight:bold; background-color:#93CEDE}
	   .thethemes div{font-weight:bold}
	   .lacarte header{font-weight:normal;font-size:small;text-align:left;}
	   .auteur{background-color:white; text-align:left;}</style>";
$res =   "<div  class='lacarte'>
			<div>
				<div class='niveauMin'>".$card->get_level()."</div>
				<div class='lalangue'>ID&nbsp;: ".$card->get_id()." (".$card->get_lang().")</div>
				<!--div class='categoriecat'>".$card->get_cat()."</div-->
				<div class='thethemes'>
					<header>".$lang['theme']."&nbsp;:</header>";
					foreach($card->get_themes() as $theme){
						$res .= "<div>$theme</div>";
					}
				$res.="</div>			
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
				$res .= "</div>
			</div>
			<!--div>TODO
				<div class='auteur'>".$card->get_author()." (".$card->get_time().")</div>
			</div-->
		</div>";
?>
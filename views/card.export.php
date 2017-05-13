<?php /* Global $card sent by Card class (models/card.class.php) → $res used then in Card class*/
	include('./languages/language.php');
	$res = "\n\$tempObj = new Card('".$card->get_lang()."',  NULL, \"".$card->get_level().'", "'.$card->get_cat().'", "1", "'.$card->get_word().'",'."\n\t".' array("'.implode('","', $card->get_forbidden_words()).'"),'."\n\t".' array("'.implode('","', $card->get_themes()).'"));'."\n";
	$res .= "\t\$tmpObj->store(true);\$nb++;";
?>

<?php
//user.class.php
//print_r($this->get_lang_lvl("es").($this->get_lang_lvl("sdfjlkkjls")===false ? "yippie" :"argh"));

include("./controllers/update_score_coeff.php");
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

  /**/$fich = fopen("./debug.txt","w");
  /**/fwrite($fich, "blabla".$fileName."\n".$ext);
  /**/fclose($fich);
?>

<?php
session_start();
error_reporting(E_ALL);//error_reporting(0); désactiver
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');
require('./sys/utils.func.php');
require('./sys/db.class.php');
require('./sys/constants.php');
require('./models/user.class.php');
require('./languages/language.php');
//C'est pas beau mais sans mvc ici

$user = user::getInstance();
$userlogged = $user->logged_in();
if ( !$userlogged || $user->username != 'admin'){
	header('Location: index.php');
}
else{
	$admin = (isset($_GET["admin"]) ||
		(!isset($_GET["export"]) && !isset($_GET["admin"])) );
	$export = isset($_GET["export"]);
	$ugc = isset($_GET["UGC"]);
	$title = 'Game of Words / Administration';
	include('./views/page.header.html');
	echo "<script type='text/javascript' src='./controllersJS/menu_lang.js'> </script>";
	$h1 = "";
	if($admin){
		echo "<h1>".$lang['admin']."</h1>";
	}
	require_once('./models/item.factory.class.php');
	$cardFactory = new ItemFactory($user->id,$user->langGame);
	if($ugc){
		$card_ids = $cardFactory->get_card_ids(ItemFactory::ALL_UG_CARDS);
	}
	else{
		$card_ids = $cardFactory->get_card_ids(ItemFactory::ALL_CARDS);
	}
	require_once('./models/card.class.php');
	if($export){
		$export_txt = "&lt;?php echo \"&lt;h2&gt;User created cards&lt;/h2&gt;\";\n";
		$export_txt .= "\$nb=0;\ntry{\n";
	}
	foreach ($card_ids as $card_id){
		$card = new Card($card_id);
		if($admin){
			$card->set_view("./views/card.inline.admin.display.php");
			echo $card;
		}
		if($export){
			$card->set_view("./views/card.export.php");
			$export_txt .= $card;
		}
	}
	if($export){
		$export_txt .= "\n}catch(Exception \$e){echo \$e;}";
		$export_txt .= "\n\necho \"&lt;p&gt;&lt;strong&gt;\$nb&lt;/strong&gt; user cards imported :D&lt;/p&gt;\";";
		$export_txt .= "?&gt;";

		echo "<h1>".$lang['export']."</h1>";
		echo "<p>".$lang['export_disclaimer']."</p>";
		$fich = fopen("./enregistrements/exported_cards.php","w");
		fwrite($fich, str_replace(array("&lt;", "&gt;"), array("<",">"), $export_txt));
		fclose($fich);
		echo "<pre style='clear:both'>$export_txt</pre>";
	}
}
?>

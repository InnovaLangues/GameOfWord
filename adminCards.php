<?php
session_start();
error_reporting(E_ALL);//error_reporting(0); désactiver
ini_set('display_errors', '1');
header('Content-Type: text/html; charset=UTF-8');
require('./sys/utils.func.php');
require('./sys/db.class.php');
require('./sys/constants.php');
require('./models/user.class.php');
require('./models/userlvl.class.php');
require('./languages/language.php');
//C'est pas beau mais sans mvc ici

$user = user::getInstance();
$userlogged = $user->logged_in();
if ( !$userlogged ){
	header('Location: index.php');  
}
else{
	$title = 'Game of Words / Administration';
	include('./views/page.header.html');
	include('./controllersJS/menu_lang.js');
	include('./sys/load_iso.php');
	echo "<h1>".$lang['admin']."</h1>";
	require_once('./models/item.factory.class.php');
	$cardFactory = new ItemFactory($user->id,$user->langGame);
	$card_ids = $cardFactory->get_card_ids(ItemFactory::ALL_CARDS);
	require_once('./models/card.class.php');
	foreach ($card_ids as $card_id){
        $card = new Card($card_id);
		$card->set_view("./views/card.inline.admin.display.php");
		echo $card;
	}
    echo "<script>$('.close').click(function() {
  $( this ).slideUp();
});</script>";
}
?>

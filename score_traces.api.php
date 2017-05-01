<?php
session_start();
error_reporting(E_ALL);//error_reporting(0); désactiver
ini_set('display_errors', '1');
require_once('./models/user.class.php');
require('./languages/language.php');
require_once('./controllers/score.handler.class.php');

$res = false;
$msg = $lang["AJAX_query_fail"] ;

if(!isset($_GET["action"])){
	throw new Exception("API call with no action");
}
else{
		switch($_GET["action"]){
			case "see_card":
				if(isset($_GET["card_id"])
				 && isset($_GET["game_level"])){
					$sh = new ScoreHandler2();
					require_once('./models/card.class.php');
					$card = new Card($_GET["card_id"]);
					if($sh->see_card($card, $_GET["game_level"])){
						$res = true;
						$msg = "";
					}
					else{
						$msg.="see_card";
					}
				}
				else{
					$msg.="see_card";
				}
				break;
			default ;
				$msg = $lang["AJAX_noquery"];
		}
}

echo json_encode(array(
	"status" => $res,
	"msg" => $msg
));

?>

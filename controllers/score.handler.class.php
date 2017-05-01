<?php
require_once('./sys/db.class.php');
require_once('./models/user.class.php');
require_once('./models/recording.class.php');
require_once('./models/card.class.php');
require_once('./sys/load_iso.php');
require_once('./models/userlvl.class.php');
/**/require_once("./controllers/update_score_coeff.php");/**///transition to new scoreHandler

class ScoreHandler2{
	private $db;
	private $oracle = false;
	private $druid = false;
	private $augur = false;
	private $recording;
	private $user;
	private $lang_iso;
	private $gh;//game handler
	const ORACLE = 1;
	const DRUID = 2;
	const AUGUR = 3;

	/*Except for the druid card creation, the score handler basically requires two users and a recording, to be able to process the scores, the constructor allows to set the appropriate variables*/
	//if $user1 is true we are considering the logged in user
	//otherwise we expect a user id.
	//For druid card creation we only need the language concerned (otherwise we get it through the recording)
	public function __construct(){
		$this->db = db::getInstance();
		$this->lang_iso = new IsoLang();
		$this->gh = new GameHandler();
		$this->user = user::getInstance();
	}

	public function see_card($card, $gameLevel){
		try{
			$lang = $this->lang_iso->any_to_iso($card->get_lang());
			$user_level = $this->user->get_lang_lvl($lang);
		}catch(Exception $E){
			throw new Exception("$lang ne semble pas être une langue…");
		}
		//once a card is seen it is stored in db for score and next play
		$query = "INSERT INTO `enregistrement` (`idOracle`, `OracleLang`, `carteID`, `nivcarte`, `nivpartie`, `validation`, `mise`)
					 VALUES ('".$this->user->id."','".
									$lang."', '".
									$card->get_id()."', '".
									$card->get_level()."', '".
									$gameLevel."','given up', '".
									$this->gh->get_stake($gameLevel, $card->get_level(), $user_level).
		"');";
		//ICITE j'en suis là et il faudrait que je fasse 1/3 de la somme des stakes de ceux qui sont given-up ou mieux…
		if(!$this->db->query($query)){
			$res = false;
			throw new Exception("“$query” could not be performed.");
		}
		else{
			$res = true;
		}
		return $res;
	}


}?>

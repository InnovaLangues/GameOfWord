<?php
require_once('./sys/db.class.php');
require_once('./models/user.class.php');
require_once('./models/recording.class.php');
require_once('./models/card.class.php');
require_once('./sys/load_iso.php');
require_once('./models/userlvl.class.php');
require_once("./controllers/notificationMessage.php");
/**/require_once("./controllers/update_score_coeff.php");/**///transition to new scoreHandler
/**/require_once("./debug.php");

class TracesHandler{
	private $db;
	private $oracle = false;
	private $druid = false;
	private $augur = false;
	private $recording;
	private $user;
	private $lang_iso;
	private $sv;//game handler
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
		$this->sv = new ScoreValues();
		$this->user = user::getInstance();
		$this->notif = new Notification(false);
		require_once('./languages/language.fn.php');
		$this->messages = get_messages($this->user->get_lang(), $this->db);
	}

	public function oracle_see_card($card, $gameLevel){
		try{
			$lang = $this->lang_iso->any_to_iso($card->get_lang());
			$user_level = $this->user->get_lang_lvl($lang);
		}catch(Exception $E){
			throw new Exception("$lang ne semble pas être une langue…");
		}
		//once a card is seen it is stored in db for score and next play
		$queries = array();
		$stakes = $this->sv->get_stake($gameLevel, $card->get_level(), $user_level);
			//create recording
		array_push($queries, "INSERT INTO `enregistrement` (`idOracle`, `OracleLang`, `carteID`, `nivcarte`, `nivpartie`, `validation`, `mise`)
					 VALUES ('".$this->user->id."','".
									$lang."', '".
									$card->get_id()."', '".
									$card->get_level()."', '".
									$gameLevel."','given up', '$stakes');");
		//score
		array_push($queries, "SET @recording_id = LAST_INSERT_ID();");
		array_push($queries, "UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`=@recording_id), `nbAbandons_oracle`=`nbAbandons_oracle`+1, `nbJeux_oracle`=`nbJeux_oracle`+1 WHERE `userid`='".$this->user->id."' AND `langue`='$lang';");
		//notification
		array_push($queries, $this->notif->addNotifGAME(
				$this->user->id,$this->messages['Oracle_started'].$stakes." pts.",
				$this->messages['img_oracle'],
				$this->messages['notif']['Oracle_started']
			));
		/**/myLog("<h2>see card</h2><pre>".print_r($queries,true)."</pre>");
		if(!$this->db->transaction($queries)){
			$res = false;
			throw new Exception("“".print_r($queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function post_record($cardId, $rec_path, $rec_length){
		$queries = array();
		//update recording
		array_push($queries, "SELECT @previous_recording_score := ".$this->sv->get_recording_score_sql_formula().", @recording_id := `enregistrementID`, @lang := `OracleLang` FROM `enregistrement` WHERE `enregistrement`.`idOracle` = ".$this->user->id." AND `enregistrement`.`carteID`='$cardId';");
		array_push($queries, "UPDATE `enregistrement` SET `cheminEnregistrement` = '$rec_path', `duration`='$rec_length', `tpsEnregistrement`=CURRENT_TIMESTAMP, `validation` = 'limbo' WHERE  `enregistrementID`=@recording_id;");
		//score
		array_push($queries, "UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`=@recording_id)-(@previous_recording_score), `nbAbandons_oracle`=`nbAbandons_oracle`-1, `nbEnregistrements_oracle`=`nbEnregistrements_oracle`+1 WHERE `userid`='".$this->user->id."' AND `langue`=@lang;");
		//notification
		array_push($queries, $this->notif->cancelLastNotifOfType(
			$this->user->id,
			$this->messages['notif']['Oracle_started']));
		array_push($queries, $this->notif->addNotifGAME(
			$this->user->id,
			$this->messages['Oracle_posted'],
			$this->messages['img_oracle'],
			$this->messages['notif']['Oracle_posted']
		));
		/**/myLog("<h2>post</h2><pre>".print_r($queries,true)."</pre>");
		if(!$this->db->transaction($queries)){
			$res = false;
			throw new Exception("“".print_r($queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function abort_record($recording_path){
		//TODO ICITE
		$query = "UPDATE `enregistrement` SET `validation` = 'given up', `tpsEnregistrement`=CURRENT_TIMESTAMP WHERE `enregistrement`.`cheminEnregistrement` = '$recording_path'; ";
		if(!$this->db->query($query)){
			$res = false;
			throw new Exception("“".$query."” could not be performed.\n".$this->db->get_error());
		}
		elseif ($this->db->affected_rows != 1) {
			$res = false;
			throw new Exception("“".$query."” affected ".$this->db->affected_rows.".\nDatabase consistence jeopardized");
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function druid_validate($recordingID, $validate=true, $revoke=false){
		$queries = array();
		//insert new arbitrage
		array_push($queries, "INSERT INTO `arbitrage` (`enregistrementID`, `idDruide` , `validation`)
			VALUES('$recordingID', '".$this->user->id."', '".$this->sv->get_druid_string($validate)."');");
		if($revoke){
			array_push($queries, "SET @arbitrage_id = LAST_INSERT_ID();");
		}

		//update recording
			//previous score and variables
		array_push($queries, "SELECT @previous_recording_score := ".$this->sv->get_recording_score_sql_formula().", @language:=`OracleLang`, @oracle_id:= `idOracle` FROM `enregistrement` WHERE `enregistrementID`='$recordingID';");
			//new recording validation
		array_push($queries, "UPDATE `enregistrement` SET `validation` =  '".
			$this->sv->get_druid_string($validate)."' WHERE `enregistrementID`='$recordingID';");
			//update oracle score
		if($validate){
			$nb_err = "";
			if($revoke){
				$nb_err = ", `nbErreurs_oracle` = `nbErreurs_oracle` - 1";
			}
			array_push($queries, "UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`='$recordingID')-(@previous_recording_score)$nb_err");
		}
		else{
			$nb_err = "";
			if($revoke){
				$nb_err = ", `nbErreurs_oracle` = `nbErreurs_oracle` + 1";
			}
			array_push($queries, "UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`='$recordingID')-(@previous_recording_score)$nb_err");
		}

		//update druid score
		array_push($queries, "UPDATE `stats` SET `nbArbitrages_druide` = `nbArbitrages_druide`+1, `score_druide` = `score_druide`+".$this->sv->get_druid_verification_score()." WHERE `stats`.`userid` = '".$this->user->id."' AND `stats`.`langue` = @language;");
		//update former druids score
		if($revoke){
			array_push($queries, "UPDATE `stats` SET `nbErrArbitrage_druide` = `nbErrArbitrage_druide`+1, `score_druide` = IF()`score_druide`-".$this->sv->get_druid_verification_error_score()." WHERE `stats`.`userid` IN (SELECT `idDruide` FROM `arbitrage` WHERE `enregistrementID`='$recordingID' AND `arbitrageID` < @arbitrage_id AND 'validation'!='".
				$this->sv->get_druid_string($validate).");");
			array_push($queries, "UPDATE `stats` SET `nbErrArbitrage_druide` = `nbErrArbitrage_druide`+1, `score_druide` = IF()`score_druide`+".$this->sv->get_druid_verification_error_score()." WHERE `stats`.`userid` IN (SELECT `idDruide` FROM `arbitrage` WHERE `enregistrementID`='$recordingID' AND `arbitrageID` < @arbitrage_id AND 'validation'='".
				$this->sv->get_druid_string($validate)."');");
		}
		/**/myLog("<pre>".print_r($queries,true)."</pre>");
	}
}?>

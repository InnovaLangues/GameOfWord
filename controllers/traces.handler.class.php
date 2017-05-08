<?php
require_once('./sys/db.class.php');
require_once('./models/user.class.php');
require_once('./models/recording.class.php');
require_once('./models/card.class.php');
require_once('./sys/load_iso.php');
require_once('./models/userlvl.class.php');
require_once("./controllers/notificationMessage.php");
/**/require_once("./controllers/update_score_coeff.php");/**///transition to new scoreHandler

class TracesHandler{
	private $db;
	private $oracle = false;
	private $druid = false;
	private $augur = false;
	private $queries;
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
		$this->reinit_queries();
	}

	public function reinit_queries(){
		$this->queries = array();
	}

	public function add_query($query){
		array_push($this->queries, $query);
	}

	public function oracle_see_card($card, $gameLevel){
		try{
			$lang = $this->lang_iso->any_to_iso($card->get_lang());
			$user_level = $this->user->get_lang_lvl($lang);
		}catch(Exception $E){
			throw new Exception("$lang ne semble pas être une langue…");
		}
		//once a card is seen it is stored in db for score and next play
		$this->reinit_queries();
		$stakes = array("V" => $this->sv->get_stake($gameLevel, $card->get_level(), $user_level, true),
							 "D" => $this->sv->get_stake($gameLevel, $card->get_level(), $user_level, false));
			//create recording
		$this->add_query("INSERT INTO `enregistrement` (`idOracle`, `OracleLang`, `carteID`, `nivcarte`, `nivpartie`, `validation`, `miseD`, `miseV`)
					 VALUES ('".$this->user->id."','".
									$lang."', '".
									$card->get_id()."', '".
									$card->get_level()."', '".
									$gameLevel."','given up', '".$stakes['D']."', '".$stakes['V']."');");
		//score
		$this->add_query("SET @recording_id = LAST_INSERT_ID();");
		$this->add_query("UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`=@recording_id), `nbAbandons_oracle`=`nbAbandons_oracle`+1, `nbJeux_oracle`=`nbJeux_oracle`+1 WHERE `userid`='".$this->user->id."' AND `langue`='$lang';");
		//notification
		$this->add_query($this->notif->addNotifGAME(
				$this->user->id,$this->messages['Oracle_started'].$stakes["V"]." pts.",
				$this->messages['img_oracle'],
				$this->messages['notif']['Oracle_started']
			));
		if(!$this->db->transaction($this->queries)){
			$res = false;
			throw new Exception("“".print_r($this->queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function post_record($cardId, $rec_path, $rec_length){
		$this->reinit_queries();
		//update recording
			//prepare score
		$this->add_query("SELECT @previous_recording_score := ".$this->sv->get_recording_score_sql_formula().", @recording_id := `enregistrementID`, @lang := `OracleLang` FROM `enregistrement` WHERE `enregistrement`.`idOracle` = ".$this->user->id." AND `enregistrement`.`carteID`='$cardId';");
			//actual recording update
		$this->add_query("UPDATE `enregistrement` SET `cheminEnregistrement` = '$rec_path', `duration`='$rec_length', `tpsEnregistrement`=CURRENT_TIMESTAMP, `validation` = 'limbo' WHERE  `enregistrementID`=@recording_id;");
		//score update
		$this->add_query("UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`=@recording_id)-(@previous_recording_score), `nbAbandons_oracle`=`nbAbandons_oracle`-1, `nbEnregistrements_oracle`=`nbEnregistrements_oracle`+1 WHERE `userid`='".$this->user->id."' AND `langue`=@lang;");
		//notifications
		$this->add_query($this->notif->cancelLastNotifOfType(
			$this->user->id,
			$this->messages['notif']['Oracle_started']));
		$this->add_query($this->notif->addNotifGAME(
			$this->user->id,
			$this->messages['Oracle_posted'],
			$this->messages['img_oracle'],
			$this->messages['notif']['Oracle_posted']
		));
		if(!$this->db->transaction($this->queries)){
			$res = false;
			throw new Exception("“".print_r($this->queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function abort_record($recording_path){
		$this->reinit_queries();
		//update recording
			//prepare score
		$this->add_query("SELECT @previous_recording_score := ".$this->sv->get_recording_score_sql_formula().", @recording_id := `enregistrementID`, @lang := `OracleLang` FROM `enregistrement` WHERE `enregistrement`.`cheminEnregistrement` = '$recording_path';");
			//actual recording update
		$this->add_query("UPDATE `enregistrement` SET `validation` = 'given up', `tpsEnregistrement`=CURRENT_TIMESTAMP WHERE `enregistrement`.`cheminEnregistrement` = '$recording_path'; ");
		//score update
		$this->add_query("UPDATE `stats` SET `score_oracle`=`score_oracle`+(SELECT ".$this->sv->get_recording_score_sql_formula()." FROM `enregistrement` WHERE `enregistrementID`=@recording_id)-(@previous_recording_score), `nbAbandons_oracle`=`nbAbandons_oracle`+1, `nbEnregistrements_oracle`=`nbEnregistrements_oracle`-1 WHERE `userid`='".$this->user->id."' AND `langue`=@lang;");
		//notifications
		$this->add_query($this->notif->cancelLastNotifOfType(
			$this->user->id,
			$this->messages['notif']['Oracle_posted']));
		$this->add_query($this->notif->addNotifGAME(
			$this->user->id,
			$this->messages['Oracle_abort'],
			$this->messages['img_oracle'],
			$this->messages['notif']['Oracle_abort']
		));
		if(!$this->db->transaction($this->queries)){
			$res = false;
			throw new Exception("“".print_r($this->queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	public function druid_validate($recordingID, $validate=true, $revoke=false){
		$this->reinit_queries();
		//insert new arbitrage
		$this->add_query("INSERT INTO `arbitrage` (`enregistrementID`, `idDruide` , `validation`) VALUES(
			'$recordingID',
			'".$this->user->id."',
			'".$this->sv->get_druid_string($validate)."');");
		if($revoke){
			$this->add_query("SET @arbitrage_id = LAST_INSERT_ID();");
		}

		//update recording
			//previous score and variables
		$this->add_query("SELECT @previous_recording_score := ".$this->sv->get_recording_score_sql_formula().", @lang:=`OracleLang`, @oracle_id:= `idOracle` FROM `enregistrement` WHERE `enregistrementID`='$recordingID';");
			//new recording validation
		$this->add_query("UPDATE `enregistrement` SET `validation` =  '".
			$this->sv->get_druid_string($validate)."' WHERE `enregistrementID`='$recordingID';");
		//update oracle score
			//the score itself
		$this->add_query("SELECT @recording_score_diff :=  ".$this->sv->get_recording_score_sql_formula()."-(@previous_recording_score) FROM `enregistrement` WHERE `enregistrementID`='$recordingID';");
		$nb_err = "";
		if($revoke){
			if($validate){
				$nb_err = ", `nbErreurs_oracle` = `nbErreurs_oracle` - 1";
			}
			else{
				$nb_err = ", `nbErreurs_oracle` = `nbErreurs_oracle` + 1";
			}
		}
		elseif (!$validate) {
			$nb_err = ", `nbErreurs_oracle` = `nbErreurs_oracle` + 1";
		}
		$this->add_query("UPDATE `stats` SET `score_oracle`=`score_oracle`+@recording_score_diff$nb_err WHERE `userid`=@oracle_id AND `langue`=@lang;");

		//update druid score
		$this->add_query("UPDATE `stats` SET `nbArbitrages_druide` = `nbArbitrages_druide`+1, `score_druide` = `score_druide`+".$this->sv->get_druid_verification_score()." WHERE `stats`.`userid` = '".$this->user->id."' AND `stats`.`langue` = @lang;");
		//update former druids score
		if($revoke){
			$this->add_query("UPDATE `stats` SET `nbErrArbitrage_druide` = `nbErrArbitrage_druide`+1, `score_druide` = IF(`score_druide`-".$this->sv->get_druid_verification_error_score()." WHERE `stats`.`langue` = @lang AND `stats`.`userid` IN (SELECT `idDruide` FROM `arbitrage` WHERE `enregistrementID`='$recordingID' AND `arbitrageID` < @arbitrage_id AND 'validation'!='".
				$this->sv->get_druid_string($validate).");");
			$this->add_query("UPDATE `stats` SET `nbErrArbitrage_druide` = `nbErrArbitrage_druide`+1, `score_druide` = IF(`score_druide`+".$this->sv->get_druid_verification_error_score()." WHERE `stats`.`langue` = @lang AND `stats`.`userid` IN (SELECT `idDruide` FROM `arbitrage` WHERE `enregistrementID`='$recordingID' AND `arbitrageID` < @arbitrage_id AND 'validation'='".
				$this->sv->get_druid_string($validate)."');");
		}
		//the notifications
		$this->add_query($this->notif->addNotif(
			"@oracle_id",
			"CONCAT('".$this->user->username.$this->messages['Oracle_verif'][$validate]."',
				' (', @recording_score_diff,' pts)')",
			$this->user->id,
			$this->messages['notif']['Oracle_verif'][$validate],
			false
		));
		$this->add_query($this->notif->addNotif(
			$this->user->id,
			"'".$this->messages['Rec_verified']."'",
			"@oracle_id",
			$this->messages['notif']['Rec_verified'],
			false
		));
		//TODO, il faudrait des notifications de réhabilitation / révocation, mais de toute façon il y a pas vraiment la fonctionnalité…
		/**/require_once("debug.php");
		/**/myLog("<h2>Druid action</h2><pre>".print_r($this->queries, true)."</pre>");
		if(!$this->db->transaction($this->queries)){
			$res = false;
			throw new Exception("“".print_r($this->queries,true)."” could not be performed.\n".$this->db->get_error());
		}
		else{
			$res = true;
		}
		return $res;
	}

	//so that if the augur reloads to give up without loss there's a penalty
	//and not for oracle
	public function augur_start_play(){

	}

	//don't find (end timer, either by giving up or not)
	//augur & oracle
	public function augur_loss(){

	}

	//augur (and oracle) win
	public function augur_win(){

	}
}?>

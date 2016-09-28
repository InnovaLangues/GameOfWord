<?php
require_once('./sys/db.class.php');
require_once('./models/user.class.php');
require_once('./models/recording.class.php');
require_once('./sys/load_iso.php');
require_once('./models/userlvl.class.php');

class ScoreHandler{
	private $db;
	private $oracle = false;
	private $druid = false;
	private $augur = false;
	private $recording;
	private $lang;
	private $lang_iso;
	private $gh;//game handler
	const ORACLE = 1;
	const DRUID = 2;
	const AUGUR = 3;

	/*Except for the druid card creation, the score handler basically requires two users and a recording, to be able to process the scores, the constructor allows to set the appropriate variables*/
	//if $user1 is true we are considering the logged in user
	//otherwise we expect a user id.
	//For druid card creation we only need the language concerned (otherwise we get it through the recording)
	public function __construct($user1_or_id = true, $user1_role_or_lang = self::AUGUR, $recording_or_id = false){
		$this->db = db::getInstance();
		$this->lang_iso = new IsoLang();
		$this->gh = new GameHandler();
		if($user1_or_id === true){
			$user = user::getInstance();
		}
		else{
			$user = new user($user1_or_id);
		}
		if($recording_or_id === false){//we have ourselves a card creator druid
			$this->druid = $user;
			try{
				$this->lang = $this->lang_iso->any_to_iso($user1_role_or_lang);
			}catch(Exception $E){
				throw new Exception("$user1_role_or_lang ne semble pas être une langue…");
			}
		}
		else{
			if(is_int($recording_or_id)){
				$this->recording = new Recording($recording_or_id);
			}
			else{//if it is not an id it is a recording (or so we decide :S)
				$this->recording = $recording_or_id;
			}
			$this->oracle = new user($this->recording->get_oracle_id());
			$this->lang = $this->lang_iso->any_to_iso($this->recording->get_lang());
			if($user1_role_or_lang === self::DRUID){
				$this->druid = $user;
			}
			else if($user1_role_or_lang === self::AUGUR){
				$this->augur = $user;
			}
			else{
				throw new Exception("On aurait dû avoir un devin (".self::AUGUR.") ou un druide (".self::DRUID."), au lieu de ça on a eu : '$user1_role_or_lang'.");
			}
		}
	}

	private function store_in_db($user_id, $lang_code, $role, $diff){
		switch($role){
			case self::ORACLE :
				$role = "scoreOracle";
				break;
			case self::DRUID :
				$role = "scoreDruide";
				break;
			case self::AUGUR :
				$role = "scoreDevin";
				break;
		}
		$sql = "UPDATE `score` SET `$role` = `$role` + ($diff), `scoreGlobal`=`scoreGlobal`+ ($diff) WHERE `score`.`userid` = '$user_id' AND `langue`='".$this->lang_iso->french_for($lang_code)."'";
		$this->db->query($sql);
		if($this->db->affected_rows() != 1){
			throw new Exception("store score in db : mais que se passe-t-il ? ($sql)");
		}
	}

	//The context allows to pick the appropriate score computation, we only need to know if it was a success (=the druid found the recording appropriate or the augur found the word.)
	public function update_scores($success=true,$notify=true){
		if($notify===true){
			require_once("./controllers/notificationMessage.php");
			$notif = new Notification();
			if(!isset($lang)){
				include("./languages/language.php");
			}
		}
		else{
			$notify = false;
		}
		//druid
		$score=0;//useless initialization
		if($this->druid !== false){
			if($this->oracle === false){//card creator
				$score = $this->gh->get_druid_create_card_score();
				$this->store_in_db(
					$this->druid->id,
					$this->lang,
					self::DRUID,
					$score
				);
				if($notify){
					$notif->addNotifGAME($this->druid->id, $lang['Card_created'].$score." pts",$lang['img_druid']);
				}
			}
			else{//card verification
				$score = $this->gh->get_druid_verification_score();
				$this->store_in_db(
					$this->druid->id,
					$this->lang,
					self::DRUID,
					$score
				);
				if($notify){
					$notif->addNotifGAME($this->druid->id, $lang['Rec_verified'].$this->oracle->username." ($score pts)",$lang['img_druid']);
				}
				$score = $this->gh->get_oracle_verification_score(
					$this->recording->get_level(),
					$this->recording->get_card_level(),
					$this->oracle->get_lang_lvl($this->lang),
					$success
				);
				$this->store_in_db(
					$this->oracle->id,
					$this->lang,
					self::ORACLE,
					$score
				);
				if($notify){
					$notif->addNotif($this->oracle->id, $this->druid->username. $lang['Oracle_verif'][$success]." ($score pts)",$this->druid->id);
				}
			}
		}
		else if($this->augur !== false){
			if($this->oracle === false){
				throw new Exception("Il devrait y avoir un oracle avec le devin");
			}
			else{
				$score = $this->gh->get_oracle_divination_score(
					$this->recording->get_level(),
					$this->recording->get_card_level(),
					$this->oracle->get_lang_lvl($this->lang),
					$success
				);
				$this->store_in_db(
					$this->oracle->id,
					$this->lang,
					self::ORACLE,
					$score
				);
				if($notify){
					$notif->addNotif($this->oracle->id, $this->augur->username. $lang['Oracle_devin'][$success]." ($score pts)",$this->augur->id);
				}
				$score = $this->gh->get_augur_divination_score(
					$this->augur->userlvl,
					$this->recording->get_card_level(),
					$this->augur->get_lang_lvl($this->lang),
					$success
				);
				$this->store_in_db(
					$this->augur->id,
					$this->lang,
					self::AUGUR,
					$score
				);
				if($notify){
					$notif->addNotifGAME($this->augur->id, $lang['Devin_played'].$this->oracle->username." ". $lang['Devin_oracle'][$success]." ($score pts)",$lang['img_augur']);
				}
			}
		}
		else{
			throw new Exception("Ni devin, ni druide, c'est pas normal…");
		}
/* //To debug scores:
		require_once("debug.php");
		$tmpString = $success ? "Gagné ":"Perdu ";
		logScores(user::getInstance(), $this->lang, $tmpString.$this);*/
	}

	public function __toString(){
		//for debugging purposes
		$res = "";
		if(isset($this->recording)){
			$res .= "Langue: $this->lang (".
				$this->recording->get_card_level().")\n";
		}
		if($this->oracle!==false){
			$res .= "Oracle: ".$this->oracle->username." (".
				$this->oracle->get_lang_lvl($this->lang)." / ".$this->recording->get_level().")\n";
		}
		if($this->druid!==false){
			$res .= "Druide: ".$this->druid->username." (".
				$this->druid->get_lang_lvl($this->lang).")\n";
		}
		if($this->augur!==false){
			$res .= "Devin: ".$this->augur->username." (".
				$this->augur->get_lang_lvl($this->lang)." / ".$this->augur->userlvl.")\n";
		}
		return $res;
	}
}?>

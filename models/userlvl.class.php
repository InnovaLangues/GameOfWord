<?php
//A class to handle the main rules
class ScoreValues{
	const LVL_EASY = 0;
	const LVL_MEDIUM = 1;
	const LVL_HARD = 2;
	const LVL_HARDEST = 3;
	const ACT_CREATECARD = 5;
	const ACT_VERIFYRECORD = 6;
	//key in array is card difficulty - user level → the lower the index, the easier the game
	private static $MULTIPLIERS_WIN = array(2 => 2, 1 => 1.5, 0 => 1, -1 => 0.75, -2 => 0.5, -3 => 0.33);
	private static $MULTIPLIERS_LOSE = array(2 => 0.5, 1 => 0.75, 0 => 1, -1 => 1.25, -2 => 1.5, -3 => 2);
	//below hardest is USELESS, but it made me feel safer.
	private static $FORBID_COUNT   = array(self::LVL_EASY => 1,
														self::LVL_MEDIUM => 3,
														self::LVL_HARD => 6/*,
													self::LVL_HARDEST => 6*/);
	private static $ORACLE_TIME    = array(self::LVL_EASY => 90,
														self::LVL_MEDIUM => 60,
														self::LVL_HARD => 30/*,
													self::LVL_HARDEST => 30*/);
	private static $AUGUR_MIN_TIME = array(self::LVL_EASY => 40,
														self::LVL_MEDIUM => 20,
														self::LVL_HARD => 0,
														self::LVL_HARDEST => 0);
	private static $AUGUR_MULT_TIME= array(self::LVL_EASY => 2,
														self::LVL_MEDIUM => 1.5,
														self::LVL_HARD => 1,
														self::LVL_HARDEST => 1);
	private static $AUGUR_PLUS_TIME= array(self::LVL_EASY => 0,
														self::LVL_MEDIUM => 0,
														self::LVL_HARD => 8,
														self::LVL_HARDEST => 8);
	private static $STAKES = array(self::LVL_EASY => 10,
											 self::LVL_MEDIUM => 20,
											 self::LVL_HARD => 30/*,
										 self::LVL_HARDEST => 30*/);
	const DRUID_VERIF = 25;
	const DRUID_VERIF_ERROR = 100;
	const DRUID_CREATE_CARD = 40;
	const RECORDING_SCORE_FORMULA =
		"IF(`validation`='given up',
			-ROUND(`miseD`/3),
			IF(`validation`='invalid',
				-ROUND(4*`miseD`/3),
				IF(`validation`='valid',
					ROUND(`miseV`*(0.5+(`nbSucces`*1.2)/GREATEST(1,`nbTentatives`))),
					0
				)
			)
		)";
	const AUGUR_SCORE_FORMULA = "ROUND(`nbMotsTrouves_devin`/`nbEnregistrements_devin`*`sommeMises_devin`)";
	private static $DRUID_STRINGS = array(0 => 'invalid',
												1 => 'valid');

	//utilities
	//To unify the way levels are defined throughout the game
	public function unify_Lvl($level){
		switch($level){
			case 'easy':
			case 'A1':
			case 'A2':
			case 'Débutant':
				$res = self::LVL_EASY;
				break;
			case 'medium':
			case 'B1':
			case 'B2':
			case 'Intermédiaire':
				$res = self::LVL_MEDIUM;
				break;
			case 'hard':
			case 'C1':
			case 'Avancé':
				$res = self::LVL_HARD;
				break;
			case 'Natif':
			case 'C2':
				$res = self::LVL_HARDEST;
				break;
			default:
				$res = false;
		}
		return $res;
	}

	//Game rules
	private function get_mastery_mult($card_lvl, $user_lvl, $win=true){
		if(!is_int($card_lvl)){
			$card_lvl = $this->unify_Lvl($card_lvl);
		}
		if(!is_int($user_lvl)){
			$user_lvl = $this->unify_Lvl($user_lvl);
		}
		if($win){
			return self::$MULTIPLIERS_WIN[$card_lvl - $user_lvl];
		}
		else{
			return self::$MULTIPLIERS_LOSE[$card_lvl - $user_lvl];
		}
	}

	public function get_forbidden_count($game_lvl){
		if(!is_int($game_lvl)){
			$game_lvl = $this->unify_Lvl($game_lvl);
		}
		return self::$FORBID_COUNT[$game_lvl];
	}

	public function get_oracle_time($game_lvl){
		if(!is_int($game_lvl)){
			$game_lvl = $this->unify_Lvl($game_lvl);
		}
		return self::$ORACLE_TIME[$game_lvl];
	}

	public function get_augur_time($game_lvl, $recording_duration){
		if(!is_int($game_lvl)){
			$game_lvl = $this->unify_Lvl($game_lvl);
		}
		$res = self::$AUGUR_MULT_TIME[$game_lvl] * $recording_duration
		 		 + self::$AUGUR_PLUS_TIME[$game_lvl];
		if($res < self::$AUGUR_MIN_TIME[$game_lvl]){
			$res = self::$AUGUR_MIN_TIME[$game_lvl];
		}
		return $res;
	}

	//Scoring
	public function get_druid_verification_score(){
		return self::DRUID_VERIF;
	}

	public function get_druid_verification_error_score(){
		return self::DRUID_VERIF_ERROR;
	}

	public function get_druid_create_card_score(){
		return self::DRUID_CREATE_CARD;
	}

	public function get_druid_string($valid){
		return self::$DRUID_STRINGS[$valid];
	}

	public function get_recording_score_sql_formula(){
		return self::RECORDING_SCORE_FORMULA;
	}

	public function get_augur_score_formula(){
		return self::AUGUR_SCORE_FORMULA;
	}

	public function get_stake($game_lvl, $card_lvl, $user_lvl, $won=true){
		if(!is_int($game_lvl)){
			$game_lvl = $this->unify_Lvl($game_lvl);
		}
		if(!is_int($card_lvl)){
			$card_lvl = $this->unify_Lvl($card_lvl);
		}
		if(!is_int($user_lvl)){
			$user_lvl = $this->unify_Lvl($user_lvl);
		}
		return round($this->get_mastery_mult($card_lvl, $user_lvl, $won) * self::$STAKES[$game_lvl]);
	}

	public function get_oracle_verification_score($game_lvl, $card_lvl, $user_lvl, $won=true){
		$res = $this->get_stake($game_lvl, $card_lvl, $user_lvl, $won);
		if(!$won){
			$res = -$res/2;
		}
		return round($res);
	}

	public function get_oracle_divination_score($game_lvl, $card_lvl, $user_lvl, $won=true){//necessary function, in case we change scoring
		return $this->get_oracle_verification_score($game_lvl, $card_lvl, $user_lvl, $won);
	}

	public function get_augur_divination_score($game_lvl, $card_lvl, $user_lvl, $won=true){
		$res = $this->get_stake($game_lvl, $card_lvl, $user_lvl, $won);
		if($won){
			$res = 2*$res;
		}
		else{
			$res = -$res;
		}
		return round($res);
	}

	public function get_augur_divination_stake($game_lvl, $card_lvl, $user_lvl){
		return $this->get_stake($game_lvl, $card_lvl, $user_lvl, true);
	}

//quick and dirty rules consistance
	public function forbid_count_to_string($lang){
		return self::const_table_to_string($lang, self::$FORBID_COUNT);
	}

	public function oracle_time_to_string($lang){
		return self::const_table_to_string($lang, self::$ORACLE_TIME);
	}

	public function stakes_to_string($lang){
		return self::const_table_to_string($lang, self::$STAKES);
	}

	private function const_table_to_string($lang, $static_array){
		$res="";
		if($lang == "en"){
			$or = "or";
		}
		else if($lang == "fr"){
			$or = "ou";
		}
		else{
			throw new Exception("$lang is not handled by const_table");
		}
		foreach($static_array as $key => $value){
			if($res!=""){
				$res.=", ";
			}
			$res.=$value;
		}
		$pos = strrpos ($res, ",");
		if($pos !== false){
			$res = substr_replace($res, " ".$or, $pos, 1);
		}
		return $res;
	}
}
?>

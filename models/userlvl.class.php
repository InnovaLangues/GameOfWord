<?php

class userlvl
{
    private $lvl = "";
    private $points = '';
    private $temps = '';
    private $user = '';

    private static $_instance = null;
        /**
     * Empêche la création externe d'instances.
     */
    private function __construct () {
        $this->read();
    }

    /**
     * Empêche la copie externe de l'instance.
     */
    private function __clone () {}

    /**
     * Renvoi de l'instance et initialisation si nécessaire.
     */
    public static function getInstance () {
        if ( !self::$_instance )
        {
            $class = __CLASS__;
            self::$_instance = new $class();
        }
        return self::$_instance;
    }



    public function read()
    {
        $this->user = user::getInstance();
        $this->lvl = $this->user->userlvl;

        $this->define($this->lvl);
    }

    private function define($lvl){
        $db = db::getInstance();

        $sql = 'SELECT *
                    FROM game_lvl
                    WHERE userlvl = "' . $lvl.'"';
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $result->free();
        if ( $row )
        {
            $this->points = (int) $row['points'];
            $this->temps = (int) $row['time'];
        }
    }
    public function get_points(){
        return $this->points;
    }
    public function get_time(){
        return $this->temps;
    }


}
















//A class to handle the main rules
require_once('./sys/constants.php');
require_once('./models/user.class.php');
require_once('./sys/db.class.php');//useless (called by Card)

class GameHandler{
	const LVL_EASY = 0;
	const LVL_MEDIUM = 1;
	const LVL_HARD = 2;
	const LVL_HARDEST = 3;
	const ACT_CREATECARD = 5;
	const ACT_VERIFYRECORD = 6;
	private static $MULTIPLIERS_WIN = array(2 => 2, 1 => 1.5, 0 => 1, -1 => 0.75, -2 => 0.5, -3 => 0.33);
	private static $MULTIPLIERS_LOSE = array(2 => 0.5, 1 => 0.75, 0 => 1, -1 => 1.25, -2 => 1.5, -3 => 2);
	//below hardest is USELESS, but it made me feel safer.
	private static $FORBID_COUNT   = array(self::LVL_EASY => 1,
														self::LVL_MEDIUM => 3,
														self::LVL_HARD => 6,
														self::LVL_HARDEST => 6);
	private static $ORACLE_TIME    = array(self::LVL_EASY => 90,
														self::LVL_MEDIUM => 60,
														self::LVL_HARD => 30,
														self::LVL_HARDEST => 30);
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
											 self::LVL_HARD => 30,
											 self::LVL_HARDEST => 30);
	const DRUID_VERIF = 25;
	const DRUID_CREATE_CARD = 40;
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

	private function get_mastery_mult($card_lvl, $user_lvl, $win=true){
		if(!is_int($card_lvl)){
			$card_lvl = $this->unify_Lvl($card_lvl);
		}
		if(!is_int($user_lvl)){
			$user_lvl = $this->unify_Lvl($user_lvl);
		}
		/**/echo "<p>".($card_lvl - $user_lvl)." → ".self::$MULTIPLIERS[$card_lvl - $user_lvl]."</p>";
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
		/**/echo "<p>".self::$AUGUR_MULT_TIME[$game_lvl]."×".$recording_duration."+".self::$AUGUR_PLUS_TIME[$game_lvl]."</p>";
		return $res;
	}

	public function get_druid_verification_score(){
		return self::DRUID_VERIF;
	}
	public function get_druid_create_card_score(){
		return self::DRUID_CREATE_CARD;
	}

	private function get_stake($game_lvl, $card_lvl, $user_lvl, $won=true){
		if(!is_int($game_lvl)){
			$game_lvl = $this->unify_Lvl($game_lvl);
		}
		if(!is_int($card_lvl)){
			$card_lvl = $this->unify_Lvl($card_lvl);
		}
		if(!is_int($user_lvl)){
			$user_lvl = $this->unify_Lvl($user_lvl);
		}
		return get_mastery_mult($card_lvl, $user_lvl, $won) * self::$STAKES[$game_lvl];
	}
	public function get_oracle_verification_score($game_lvl, $card_lvl, $user_lvl, $won=true){
		$res = $this->get_stake($game_lvl, $card_lvl, $user_lvl, $won);
		if(!$won){
			$res = -$res/2;
		}
		return $res;
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
		return $res;
	}
}

?>

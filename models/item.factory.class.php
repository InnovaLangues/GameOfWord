<?php
require_once('./sys/constants.php');
require_once('./models/card.class.php');
require_once('./sys/db.class.php');//useless (called by Card)
class ItemFactory //a quick and dirty class…
{	//…to encapsulate some "complicated" frequent queries
	const ANY = 1;
	const ALL_CARDS = 6;
	const ALL_UG_CARDS = 7;//User generated means not created by admin user…
	const CARD_NOT_ME = 2;
	const CARD_FROM_LEXICON = 5;
	const VALID_RECORDING_NOT_ME = 3;
	const LIMBO_RECORDING_ME_IF_POSSIBLE = 4;
	private $db;
	private $user_id;//(int)for now the items are selected based on the user
	private $lang = self::ANY;//(string iso639-1) language played in
	private $themes = array();//(array(int))the themes sought after
	private $query;
	//we don't use the level and that's a shame…

	/**
	 * Constructors setters and that kind of stuff
	 */
	public function __construct($userId, $langue, $theme = false){
		$this->db = db::getInstance();
		$this->user_id = $userId;
		if(is_string($langue)){
			$this->lang = $langue;
		}
		else{
			throw new Exception($langue." is not a string.");
		}
		if(is_array($theme)){
			$this->themes = $theme;
		}
	}

	private function generate_query($queryType, $parameter = NULL, $forOne = true){
		$res = true;
		if($forOne){
			$forOne = "LIMIT 1";
		}
		else{
			$forOne="";
		}
		switch ($queryType) {
			case self::CARD_NOT_ME:
				$this->query = "SELECT `cartes`.`idCarte` as `zeId` FROM `cartes`
						WHERE `cartes`.`idEraser` IS NULL
						AND   `cartes`.`langue`='$this->lang'
						AND   `cartes`.`idDruide` != '$this->user_id'
						AND   `cartes`.`idCarte` NOT IN (
							SELECT `enregistrement`.`carteID` as`cardId` FROM `enregistrement` WHERE `enregistrement`.`idOracle`='$this->user_id'
							UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `arbitrage`.`idDruide`='$this->user_id'
							UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `parties`,`enregistrement` WHERE `parties`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `parties`.`idDevin`='$this->user_id'
						) ORDER BY RAND() $forOne;";
				break;
			case self::LIMBO_RECORDING_ME_IF_POSSIBLE:
				$this->query = "SELECT `enregistrement`.*, IF(
						`cartes`.`idDruide` = '$this->user_id'
						OR `enregistrement`.`idOracle`= '$this->user_id'
						OR `cartes`.`idCarte` IN (
							SELECT `enregistrement`.`carteID` FROM `enregistrement`,`arbitrage` WHERE `enregistrement`.`enregistrementID`=`arbitrage`.`enregistrementID` AND `arbitrage`.`idDruide` = '$this->user_id'
							UNION SELECT `enregistrement`.`carteID` FROM `enregistrement`,`parties` WHERE `enregistrement`.`enregistrementID`=`parties`.`enregistrementID` AND `parties`.`idDevin` = '$this->user_id'
					),1,0) as `poids`
					FROM `enregistrement`,`cartes`
						WHERE `cartes`.`idEraser` IS NULL
						AND `enregistrement`.`idOracle` != '$this->user_id'
						AND `enregistrement`.`validation` = 'limbo'
						AND `enregistrement`.`carteID` = `cartes`.`idCarte`
						AND `cartes`.`langue` = '$this->lang'
					ORDER BY `poids` DESC, RAND() $forOne;";

				break;
			case self::VALID_RECORDING_NOT_ME:
				//timestamp used because the recording is by default in limbo
				//lets the user 2 minutes to give up before someone listens to it
				//TODO error handling if session is longer than 120s and druid before cancel
				$this->query = "SELECT `enregistrement`.* FROM `enregistrement`,`cartes`
						WHERE `cartes`.`idEraser` IS NULL
						AND   `cartes`.`langue`='$this->lang'
						AND   `cartes`.`idCarte` = `enregistrement`.`carteID`
						AND   `cartes`.`idDruide` != '$this->user_id'
						AND   `enregistrement`.`validation` = 'valid'
						AND   `enregistrement`.`idOracle` != '$this->user_id'
						AND   UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(`enregistrement`.`tpsEnregistrement`) > 120
						AND   `enregistrement`.`carteID` NOT IN (
							SELECT `enregistrement`.`carteID` as`cardId` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `arbitrage`.`idDruide`='$this->user_id'
							UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `parties`,`enregistrement` WHERE `parties`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `parties`.`idDevin`='$this->user_id'
						) ORDER BY RAND() $forOne;";
				break;
			case self::CARD_FROM_LEXICON:
				if(isset($parameter)){
					$this->query = "SELECT `cartes`.`idCarte` as `zeId` FROM `cartes`
						WHERE `cartes`.`idEraser` IS NULL
						AND   `cartes`.`langue`='$this->lang'
						AND   `cartes`.`idDruide` != '$this->user_id'
						AND   `cartes`.`idCarte` NOT IN (
							SELECT `enregistrement`.`carteID` as`cardId` FROM `enregistrement` WHERE `enregistrement`.`idOracle`='$this->user_id'
							UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `arbitrage`,`enregistrement` WHERE `arbitrage`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `arbitrage`.`idDruide`='$this->user_id'
							UNION SELECT `enregistrement`.`carteID` as`cardId` FROM `parties`,`enregistrement` WHERE `parties`.`enregistrementID` = `enregistrement`.`enregistrementID` AND `parties`.`idDevin`='$this->user_id'
						)
						AND (  `cartes`.`mot` IN $parameter
							OR `cartes`.`idCarte` IN (SELECT `idCarte` FROM `mots_interdits` WHERE `mots_interdits`.`mot` IN $parameter)
						) ORDER BY RAND() $forOne;";
				}
				else{
					$res = false;
				}
				break;
			case self::ALL_CARDS:
				$this->query = "SELECT `idCarte` as `zeId` FROM `cartes` WHERE `cartes`.`idEraser` IS NULL ORDER BY `langue`, `idCarte` $forOne";//le forOne servira pas, pour la cohérence
				break;
			case self::ALL_UG_CARDS:
				$this->query = "SELECT `idCarte` as `zeId` FROM `cartes` WHERE `cartes`.`idDruide`!='1' AND `cartes`.`idEraser` IS NULL ORDER BY `langue`, `idCarte` $forOne";//le forOne servira pas, pour la cohérence
					break;
			default:
				$res = false;
				break;
		}
		return true;
	}

	//returns a card object if the query yields 1 result and the number
	//of result otherwise, this should only be 0
	public function get_card($queryType, $parameter = NULL){
		if($this->generate_query($queryType, $parameter)){
			$this->db->query($this->query);
			$res = $this->db->affected_rows();
			if($res == 1){
				$res = new Card($this->db->fetch_object()->zeId);
			}
		}
		else{
			throw new Exception("Not a proper query type '$queryType'.");
		}
		return $res;
	}

	public function get_card_id($queryType, $parameter = NULL){
		if($this->generate_query($queryType, $parameter)){
			$this->db->query($this->query);
			if($this->db->affected_rows() == 1){
				$res = $this->db->fetch_object()->zeId;
			}
			else{
				$res = -1;
			}
		}
		else{
			throw new Exception("Not a proper query type '$queryType'.");
		}
		return $res;
	}

	public function get_card_ids($queryType, $parameter = NULL){
		if($this->generate_query($queryType, $parameter, false)){
			$this->db->query($this->query);
			$res = $this->db->affected_rows();
			if($res >= 1){
				$res = array();
				while($id = $this->db->fetch_object()){
					array_push($res, $id->zeId);
				}
			}
		}
		else{
			throw new Exception("Not a proper query type '$queryType'.");
		}
		return $res;
	}

	public function get_recording($queryType, $parameter = NULL){
	//also checks if the recording exists
		if($this->generate_query($queryType,$parameter)){
			$this->db->query($this->query);
			$res = $this->db->affected_rows();
			if($res == 1){
				$res = $this->db->fetch_object();
				//checks whether the file exists makes it invalid otherwise
				if(!file_exists("enregistrements/".$res->cheminEnregistrement)){
					//set recording to invalid
					$tmp_query = "UPDATE `enregistrement` SET `validation` = 'invalid', `tpsEnregistrement`=CURRENT_TIMESTAMP WHERE `enregistrement`.`enregistrementID` = ".$res->enregistrementID;
					$res = $this->db->query($tmp_query);
					//recursion
					if($res){
						return $this->get_recording($queryType, $parameter);
					}
					else{
						throw new Exception("Error chain in : ".$this->query." (recording not found) <br />Update error in: ".$tmp_query);
					}
				}
			}
		}
		else{
			throw new Exception("Not a proper query type '$queryType'.");
		}
		return $res;
	}
}
?>

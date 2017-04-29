<?php
require_once('./sys/db.class.php');
//created to make update_score_coeff more readable, lacking many many many functionnalities
/*see for instance
			require_once("./sys/mp3.utils.php");
			$mp3_file = new MP3File("./enregistrements/$fileName");
			$duration = $mp3_file->getDuration();
*/
class Recording
{
	private $id = NULL ;
	private $lang ;
	private $level ;
	private $card_id;
	private $card_level ;
	private $oracle_id;
	private $db;

	public function __construct($rec_id){
		$this->db = db::getInstance();
		$this->id = $rec_id;
		$this->db->query("SELECT `idOracle`,`oracleLang`,`nivpartie`,`nivcarte`,`carteID` FROM `enregistrement` WHERE `enregistrementID`='$this->id';");
		if($this->db->affected_rows() == 1){
			$tmpObj = $this->db->fetch_object();
			$this->lang = $tmpObj->oracleLang;
			$this->level = $tmpObj->nivpartie;
			$this->card_level = $tmpObj->nivcarte;
			$this->oracle_id = $tmpObj->idOracle;
			$this->card_id = $tmpObj->carteID;
		}
		else{
			throw new Exception("L'enregistrement “".print_r($rec_id, true)."” n'a pu être récupéré");
		}
	}

	public function get_id(){
		return $this->id;
	}

	public function get_level(){
		return $this->level;
	}

	public function get_card_id(){
		return $this->card_id;
	}

	public function get_card_level(){
		return $this->card_level;
	}

	public function get_lang(){
		return $this->lang;
	}

	public function get_oracle_id(){
		return $this->oracle_id;
	}
}
?>

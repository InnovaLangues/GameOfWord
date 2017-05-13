<?php
require_once('./sys/db.class.php');
require_once('./sys/constants.php');
class Card
{
	private $id = NULL ;
	private $lang ; //this should be an aobject
	private $langPrecisions=false ; //this should complete the language class
	private $level ; // should be an object
	private $cat ; //should be an object
	private $author ; //should be an object
	private $themes = array() ;
	private $cdate ;
	private $db;
	private $view = './views/card.table.display.php';
	private $guessWord ;
	private $forbiddenWords = array();

	/**
	 * The constructor can either create the object from scratch, or retrieve it in the database.
	 * The one parameter constructor takes the card id and picks it up in db, whereas the regular one requires one value per attribute
	 */
	public function __construct($idOrLang, $extLangueOrView=NULL, $niveau=NULL, $categorie=NULL, $idDruide=NULL, $mot=NULL, $forb=NULL, $themes=NULL,$aview=NULL){
		$this->db = db::getInstance();
		if(!isset($mot)){//db fetch
			if(is_int($idOrLang) || ($idOrLang === (string)(int)$idOrLang)){
				$this->get_from_id($idOrLang);
				$this->set_view($extLangueOrView);
			}
			else{
				throw new Exception("$idOrLang (Card ID) is not an integer…");
			}
		}
		else{//plein de paramètres
			//testing data types would not be entirely superfluous…
			$this->id = false;
			$this->lang = $idOrLang;
			$this->langPrecisions = $extLangueOrView;
			$this->level = $niveau;
			$this->cat = $categorie;
			$this->author = $idDruide;
			$this->guessWord = $mot;
		/*getting unique values in a simple array (good to prevent duplicate key errors later on)*/
			$this->themes = array_keys(array_flip($themes));
			$this->forbiddenWords = array_keys(array_flip($forb));
			$this->set_view($aview);
		}
	}

	public function get_id(){
		return $this->id;
	}

	public function get_level(){
		return $this->level;
	}

	public function get_lang(){
		return $this->lang;
	}

	public function get_cat(){
		return $this->cat;
	}

	public function get_themes(){
		return $this->themes;
	}

	public function get_forbidden_words(){
		return $this->forbiddenWords;
	}

	public function get_author(){
		return $this->author;
	}

	public function get_time(){
		return $this->cdate;
	}

	public function get_word(){
		return $this->guessWord;
	}

	public function set_view($aView){
		if(isset($aView) && is_string($aView)){//more error testing such as existance of file ?
			$this->view = $aView;
		}
	}
	//removes the last forbidden words until there are at most $nbWords
	//returns the actual number of forbidden words…
	public function set_forbidden_count($nbWords){
		while(count($this->forbiddenWords) > $nbWords){
			array_pop($this->forbiddenWords);
		}
		return count($this->forbiddenWords);
	}

	/*
	 * So that the current card's data is replaced by that of card $cardId
	 */
	public function get_from_id($cardId){
		$this->id = $cardId;
		if($this->id){
			$this->db->query("SELECT * FROM `cartes` WHERE `idCarte`='$this->id';");
			if($this->db->affected_rows() == 1){
				$tmpObj = $this->db->fetch_object();
				$this->lang = $tmpObj->langue;
				if(isset($tmpObj->extLangue)){
					$this->langPrecisions = $tmpObj->extLangue;
				}
				$this->level = $tmpObj->niveau;
				$this->cat = $tmpObj->categorie;
				$this->author = $tmpObj->idDruide;
				$this->guessWord = $tmpObj->mot;
				$this->cdate = $tmpObj->dateCreation;

				$this->db->query("SELECT `themeFR`
					FROM `themes` , `themes_cartes`
					WHERE `themes_cartes`.`idCarte` = '$this->id'
					AND `themes_cartes`.`idTheme` = `themes`.`idTheme` ");
				while($tmpObj = $this->db->fetch_object()){
					array_push($this->themes, $tmpObj->themeFR);
				}

				$this->db->query("SELECT `mot` FROM `mots_interdits` WHERE `idCarte`='$this->id' ORDER BY `ordre`;");
				while($tmpObj = $this->db->fetch_object()){
					array_push($this->forbiddenWords, $tmpObj->mot);
				}
			}
			else{
				throw new Exception("La carte $cardId n'a pu être récupérée");
			}
		}
		else{
			throw new Exception("La carte $cardId n'a pu être récupérée");
		}
	}

	/**
	 * this is used to store the object inside the database.
	 * $prompt : whether the function should put on hold the insertion
	 *		   if a similar card (same guessword)  should be inserted
	 * if the card already has an id, that means it exists (otherwise
	 * the auto_increment should do the numbering), thus we replace the
	 * existing card with this.
	 */
	public function store($prompt=false){
		if($prompt){
	   		$this->db->query("SELECT `idCarte` FROM `cartes` WHERE `mot`='".$this->guessWord."' AND `langue`='".$this->lang."';");
	   		$nb = $this->db->num_rows() ;
	   		if($nb > 0){
	   			//TODO : better compare with actual objects
	   			//and check the forbidden words and all…
	   			$msg="(";
	   			while($row = $this->db->fetch_object()){
	   				if($msg!="("){
	   					$msg .= ", " ;
	   				}
	   				$msg .= $row->idCarte;
	   			}
	   			$msg.=")";
	   			throw new Exception("Il y a déjà $nb cartes associées à “".$this->guessWord."” $msg.");
	   		}
		}
		if($this->id){
			throw new Exception("Le programme ne sait pas encore mettre à jour les cartes…");//TODO
		}
		else{//Normal situation
	//Handling the card base
			//So as not to escape NULL…
			if(!isset($this->langPrecisions)){
				$theLangPrecisions = "NULL";
			}
			else{
				$theLangPrecisions = $this->db->escape((string) $this->langPrecisions);
			}
			$this->db->query("INSERT INTO `cartes` (`langue`, `extLangue`, `niveau`,  `categorie`, `idDruide`, `mot`, `dateCreation`) VALUES (".$this->db->escape((string) $this->lang).", ".$theLangPrecisions.
					", ".$this->db->escape((string) $this->level).", ".$this->db->escape((string) $this->cat).
					", ".$this->db->escape((string) $this->author).", ".$this->db->escape((string) $this->guessWord).
					", CURRENT_TIMESTAMP);");
			$this->id=$this->db->insert_id();
			if($this->id > 0){
		//handling the themes
				for($i=0;$i<count($this->themes);$i++){
					$curTheme = $this->db->escape((string) $this->themes[$i]);
					$this->db->query("SELECT `idTheme` FROM `themes` WHERE `themeFR` = ".$curTheme.";");
					$idT = $this->db->fetch_object();
					if(!$idT){
						$this->db->query("INSERT INTO `themes` (`themeFR`) VALUES (".$curTheme.");");
						$idT = $this->db->insert_id() ;
					}
					else{
						$idT = $idT->idTheme;
					}
					if($idT > 0){
						$this->db->query("INSERT INTO `themes_cartes` (`idCarte`, `idTheme`) VALUES ('".$this->id."', '$idT');");
					}
					if(!$this->db->has_result() || ($idT <= 0)){
						throw new Exception("Il y a un os dans les thèmes (".$this->themes[$i].").<br /><pre>INSERT INTO `themes_cartes` (`idCarte`, `idTheme`) VALUES ('".$this->id."', '$idT');</pre>");
					}
				}
		//handling the forbidden words
				$query = "INSERT INTO `mots_interdits` (`idCarte`, `mot`, `ordre`) VALUES";
				$nbW = count($this->forbiddenWords);
				for($i=0;$i<$nbW;$i++){
					$query .= " ('".$this->id."', ".$this->db->escape((string) $this->forbiddenWords[$i]).", '".($i+1)."')";
					if($i==($nbW-1)){
						$query .= ";";
					}
					else{
						$query .= ",";
					}
				}
				$this->db->query($query);
				if(!$this->db->has_result()){
					throw new Exception("Il y a un os dans les mots interdits.");
				}
			}
			else{
				throw new Exception("La carte n'a pu être stockée.<br />".serialize($this));
			}
		}
	}

	public function __toString(){
		$card = $this;
		include($this->view);
		return $res;
	}

	public function dirtify(){
		$res = array();
		$res['carteID'] = $this->id;
		if(isset($this->themes[0])){
			$res['theme']   = $this->themes[0];
		}
		$res['idDruide']= $this->author;
		$res['temps']   = $this->cdate;
		$res['niveau']  = $this->level;
		$res['langue']  = $this->lang;
		$res['mot']     = $this->guessWord;
		$i = 1;
		foreach($this->forbiddenWords as $word){
			$res["tabou".$i] = $word;
			$i++;
		}
		return $res;
	}
}
?>

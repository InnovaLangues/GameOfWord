<?php
require_once('./sys/db.class.php');
class Card
{
	private $id = NULL ;
	private $lang ; //this should be an aobject
	private $langPrecisions=false ; //this should complete the language class
	private $level ; // should be an object
	private $cat ; //should be an object
	private $author ; //should be an object
	private $guessWord ;
	private $forbiddenWords = array();
	private $themes = array() ;
	private $cdate ;
	private $db;

	/**
	 * The constructor can either create the object from scratch, or retrieve it in the database.
	 * The one parameter constructor takes the card id and picks it up in db, whereas the regular one requires one value per attribute
	 */
	public function __construct($idCarteOrLangueOrCarte, $extLangue=NULL, $niveau=NULL, $categorie=NULL, $idDruide=NULL, $mot=NULL, $forb=NULL, $themes=NULL){
		$this->db = db::getInstance();
		if(!isset($mot)){//db fetch
			$this->id = $idCarteOrLangueOrCarte;
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
					throw new Exception("La carte $idCarteOrLangueOrCarte n'a pu être récupérée");
				}
			}
			else{
				throw new Exception("La carte $idCarteOrLangueOrCarte n'a pu être récupérée");
			}
		}
		else{//plein de paramètres
			//testing data types would not be entirely superfluous…
			$this->id = false;
			$this->lang = $idCarteOrLangueOrCarte;
			$this->langPrecisions = $extLangue;
			$this->level = $niveau;
			$this->cat = $categorie;
			$this->author = $idDruide;
			$this->guessWord = $mot;
			$this->themes = $themes;
			$this->forbiddenWords = $forb;
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
	   		$this->db->query("SELECT `idCarte` FROM `cartes` WHERE `mot`='".$this->guessWord."';");
	   		$nb = $this->db->affected_rows ;
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
			if(!isset($this->langPrecisions)){
				$this->langPrecisions = "NULL";
			}
			$this->db->query("INSERT INTO `cartes` (`langue`, `extLangue`, `niveau`,  `categorie`, `idDruide`, `mot`, `dateCreation`) VALUES ('".$this->lang."', ".$this->langPrecisions.
					", '".$this->level."', '".$this->cat.
					"', '".$this->author."', '".$this->guessWord.
					"', CURRENT_TIMESTAMP);");
			$this->id=$this->db->insert_id();
			if($this->id > 0){
		//handling the themes
				for($i=0;$i<count($this->themes);$i++){
					$this->db->query("SELECT `idTheme` FROM `themes` WHERE `themeFR` = '".$this->themes[$i]."'");
					$idT = $this->db->fetch_object();
					if(!$idT){
						$this->db->query("INSERT INTO `themes` (`themeFR`) VALUES ('".$this->themes[$i]."');");
						$idT = $this->db->insert_id() ;
					}
					else{
						$idT = $idT->idTheme;
					}
					if($idT > 0){
						$this->db->query("INSERT INTO `themes_cartes` (`idCarte`, `idTheme`) VALUES ('".$this->id."', '$idT');");
					}
					if(!$this->db->has_results() || ($idT <= 0)){
						throw new Exception("Il y a un os dans les thèmes (".$this->themes[$i].").<br /><pre>$query</pre>");
					}
				}
		//handling the forbidden words
				$query = "INSERT INTO `mots_interdits` (`idCarte`, `mot`, `ordre`) VALUES";
				$nbW = count($this->forbiddenWords);
				for($i=0;$i<$nbW;$i++){
					$query .= " ('".$this->id."', '".$this->forbiddenWords[$i]."', '".($i+1)."')";
					if($i==($nbW-1)){
						$query .= ";";
					}
					else{
						$query .= ",";
					}
				}
				$this->db->query($query);
				if(!$this->db->has_results()){
					throw new Exception("Il y a un os dans les mots interdits.");
				}
			}
			else{
				throw new Exception("La carte n'a pu être stockée.<br />".serialize($this));
			}
		}
	}

	public function __toString(){
		//TODO
		$res = "<div  class='carte'><div>
			<div class='niveauMin'>$this->level</div>
			<div class='langue'>$this->id ($this->lang / $this->langPrecisions)</div>
			<div class='categorie'>$this->cat</div>
			<div class='themes'>\n";
			foreach($this->themes as $theme){
				$res .= "<div>$theme</div>";
			}
			$res.="</div>			
		</div>
		<div>
			<div class='motCle motATrouver'>$this->guessWord</div>
			<div class='tabous'>";
			foreach($this->forbiddenWords as $word){
				$res .= "<div class='motCle'>$word</div>\n";
			}
			$res .= "</div>
		</div>
		<div>
			<div class='auteur'>$this->author ($this->cdate)</div>
		</div>
		</div></div>";
		return $res;
	}

	public function dirtify(){
		$res = array();
		$res['carteID'] = $this->id;
		$res['theme']   = $this->themes[0];
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
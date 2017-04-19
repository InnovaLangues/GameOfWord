<?php
require_once("./models/item.factory.class.php");
require_once("./models/card.class.php");

class druid_arbitrage
{
	private $errors = array();
	private $enregist = array();
	private $nivcarte = '';
	private $userlang = '';
	private $user= '';
	private $druid= '';
	public $partie = false;

	private $raisin ='';
	private $res2 = '';
	private $card ;
	private $result= '';
	private $result1= '';
	private $mode = '';
	private $adresse = '';
	private $oracle = '';
	private $enregistrement='';

	private $valid = 'valid';
	private $invalid = 'invalid';

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
		{
			$this->selectpartie();
			return $this->display_et_scores();
		}
		return false;
	}

	private function init()
	{
		//récupération des informations de bases : userid, langue et la date
		$this->user = user::getInstance();
		$this->druid = $this->user->id;
		$this->userlang = $this->user->langGame;

		return true;
	}
//Revoir dans le cas où toutes les cartes ont été supprimés du serveur.
	private function selectpartie(){
		try{
			//connexion à la BD
			$db = db::getInstance();
		//Dans le cas où le joueur souhaite arbitrer la carte après une partie en tant que devin
			if(isset($_SESSION["idCard"]) && isset($_SESSION["idEnregistrement"])){
				$idCarte = $_SESSION["idCard"];
				$this->enregistrement = $_SESSION["idEnregistrement"];

				$sql = "SELECT * FROM `enregistrement` WHERE `enregistrementID` =".$this->enregistrement;
				$db->query($sql);
				if($db->num_rows()>0){
					$this->raisin = $db->fetch_object();
//why why why ça et adresse ?
					$this->enregistrement = "enregistrements/".$this->raisin->cheminEnregistrement;
					// récupération du pseudo du joueur arbitré
					$sql = 'SELECT
						username
						FROM user WHERE userid ="'.$this->raisin->idOracle.'"';
					$this->result=$db->query($sql);
					$this->res2= mysqli_fetch_assoc($this->result);

					$this->card = new Card($_SESSION["idCard"], './views/card.inline.display.php');

					//construction de l'adresse de l'enregistrement à partir du nom du fichier
					$this->adresse = "enregistrements/".$this->raisin->cheminEnregistrement;
					$this->partie=true;

					unset($_SESSION["idCard"]);
					unset($_SESSION["idEnregistrement"]);

					return true; //two returns
				}
				else{
					array_push($this->errors, 'noEnregistrement');
				}
			}
		//Cas par défaut (clique sur arbitrer)
			else{
				$this->partie=false;
				$recordingFactory = new ItemFactory($this->druid,$this->user->langGame);
				$this->raisin = $recordingFactory->get_recording(ItemFactory::LIMBO_RECORDING_ME_IF_POSSIBLE);
				if(is_object($this->raisin) && file_exists("enregistrements/".$this->raisin->cheminEnregistrement)){
					//construction de l'adresse de l'enregistrement à partir du nom du fichier
					$this->adresse = "enregistrements/".$this->raisin->cheminEnregistrement ;
					$this->enregistrement = $this->raisin->enregistrementID;
					// récupération du pseudo du joueur arbitré
					 $sql = 'SELECT username FROM user WHERE userid ="'.$this->raisin->idOracle.'"';
					 $this->result=$db->query($sql);
					 $this->res2= mysqli_fetch_assoc($this->result);

					//récupération de la carte jouée
					$this->card = new Card($this->raisin->carteID,'./views/card.inline.display.php');
					$this->partie=true;
					require_once("./models/userlvl.class.php");
					$gh = new GameHandler();
					$this->card->set_forbidden_count($gh->get_forbidden_count($this->raisin->nivpartie));
					unset($gh);
	   			}
				else{//$this->partie reste faux
					array_push($this->errors, 'noEnregistrement');
				}
			}
		}
		catch(Exception $e){//$this->partie reste faux
			array_push($this->errors, 'noEnregistrement');
		}
		return $this->partie;
	}

	public function getParty(){
		return $this->partie;
	}

	private function display_et_scores()
	{
		require_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();
		require_once('./controllers/update_score_coeff.php');
		//connexion à la BD
		$db = db::getInstance();

		if(isset($_POST["enregistrement1"])  &&  isset($_POST["oracle"])){
			$this->enregistrement = $_POST["enregistrement1"];
			$this->oracle = $_POST['oracle'];
		}
		if(isset($this->enregistrement) && ($this->enregistrement!="") ){
			$sh = new ScoreHandler($this->druid, ScoreHandler::DRUID,(int) $this->enregistrement);
		}
		// après avoir cliqué sur "au bûcher" = description vide ou fautive
		if(isset($_POST['invalidate']))
		{
			// Requête d'insertion des info dans la table 'arbitrage'
			$sql = 'INSERT INTO arbitrage
			(enregistrementID,idDruide,validation)
				VALUES(' .
					$db->escape((string) $this->enregistrement ) . ', ' .
					$db->escape((string) $this->druid) . ', ' .
					$db->escape((string) $this->invalid ) . ')' ;

			$db->query($sql);
		//	mettre à jour le champs "validation" de la table enregistrement pour que cet enregistrement devienne jouable
			$sql = 'UPDATE enregistrement
				SET validation =  ' .$db->escape((string) $this->invalid ) . '
				WHERE enregistrementID="'.$this->enregistrement .'" ' ;
				$db->query($sql);

			// Requête de modification du score de l'Oracle dont la description est jetée en pâture aux flammes du bûcher purificateur
			//Requête de modification du score du Druide après l'accomplissement de son fastidieux travail d'inquisition
			$sh->update_scores(false);

			//for dynamic notification don't want to take the time to understand them…
			$_SESSION["notif"]["notification_done"]["Druide"] = 'pointsDruide';
			header('Location: index.php?page.home.html');

			// après avoir cliqué sur "valider" = description correcte et jouable
		}elseif (isset($_POST['validate'])){
			// insertion des informations dans la table arbitrage
			$sql = 'INSERT INTO arbitrage
			(enregistrementID,idDruide,validation)
				VALUES(' .
					$db->escape((string) $this->enregistrement ) . ', ' .
					$db->escape((string) $this->druid) . ', ' .
					$db->escape((string) $this->valid ) . ') ' ;
				$db->query($sql);

			//	mettre à jour le champs "validation" de la table enregistrement pour que cet enregistrement devienne jouable
			$sql = 'UPDATE enregistrement
			SET validation =  ' .$db->escape((string) $this->valid ) . '
			WHERE enregistrementID="'.$this->enregistrement .'" ' ;
			$db->query($sql);

			// Requête de modification du score de l'Oracle dont la description est élevée au rang de prediction divine
			//Requête de modification du score du Druide l'accomplissement de son fastidieux travail d'inquisition
			$sh->update_scores(true);

			//for dynamic notification don't want to take the time to understand them…
			$_SESSION["notif"]["notification_done"]["Druide"] = 'pointsDruide';
			header('Location: index.php?page.home.html');
			// sinon, c'est le premier passage dans la page, il n'y a pas encore eu d'arbitrage donc on affiche la page d'arbitrage
		}

		else{
			include('./views/druid.arbitrage.html');
		}
		return true;
	}

}

?>

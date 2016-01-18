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
	
	private $previousSGO = 0;
	private $previousSO = 0;
	
	private $previousSGDr = 0;
	private $previousSDr = 0;
	private $pointsDr = 10;
	
	private $et_c_est_le_temps_qui_court ='d/m/Y H:i';
	
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

		//récupération des points en fonction du niveau de jeu
		$this->userlvl = userlvl::getInstance();
		$this->points= $this->userlvl->get_points();		
		
		$this->et_c_est_le_temps_qui_court = date("d/m/Y H:i");
		
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
				$idEnregistrement = $_SESSION["idEnregistrement"];

				$sql = "SELECT * FROM `enregistrement` WHERE `enregistrementID` = $idEnregistrement";
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
		require('./sys/load_iso.php');
		require_once('./controllers/update_score_coeff.php');
		
		if(isset($_POST["enregistrement1"])  &&  isset($_POST["oracle"])){
			$this->enregistrement = $_POST["enregistrement1"];
			$this->oracle = $_POST['oracle'];
		}
		// après avoir cliqué sur "au bûcher" = description vide ou fautive
		if(isset($_POST['invalidate']))
		{

			//connexion à la BD
			$db = db::getInstance(); 

			// Requête d'insertion des info dans la table 'arbitrage'
			$sql = 'INSERT INTO arbitrage
			(enregistrementID,idDruide,tpsArbitrage,validation)
				VALUES(' .
					$db->escape((string) $this->enregistrement ) . ', ' .
					$db->escape((string) $this->druid) . ', ' .
					$db->escape((string) $this->et_c_est_le_temps_qui_court) . ', ' .
					$db->escape((string) $this->invalid ) . ')' ;
					
				$db->query($sql);
			//	mettre à jour le champs "validation" de la table enregistrement pour que cet enregistrement devienne jouable
				$sql = 'UPDATE enregistrement 
				SET validation =  ' .$db->escape((string) $this->invalid ) . ' 
				WHERE enregistrementID="'.$this->enregistrement .'" ' ;
				$db->query($sql);
			
		// Requête de modification du score de l'Oracle dont la description est jetée en pâture aux flammes du bûcher purificateur
			
			updateScoreOracleDruideRefuse($this->oracle,$iso[$this->userlang],$this->enregistrement);
			
		//Requête de modification du score du Druide après l'accomplissement de son fastidieux travail d'inquisition
			//récupération du score précédent;

			updateScoreDruideArbitrage($this->druid,$iso[$this->userlang],$this->pointsDr);
			
			$_SESSION["notif"]["notification_done"]["Druide"] = 'pointsDruide';
			header('Location: index.php?page.home.html');
			
			// après avoir cliqué sur "valider" = description correcte et jouable
		}elseif (isset($_POST['validate'])){

				//connexion à la BD
				$db = db::getInstance();
				// insertion des informations dans la table arbitrage
				$sql = 'INSERT INTO arbitrage
				(enregistrementID,idDruide,tpsArbitrage,validation)
					VALUES(' .
						$db->escape((string) $this->enregistrement ) . ', ' .
						$db->escape((string) $this->druid) . ', ' .
						$db->escape((string) $this->et_c_est_le_temps_qui_court) . ', ' .
						$db->escape((string) $this->valid ) . ') ' ;	
					$db->query($sql);
				
				//	mettre à jour le champs "validation" de la table enregistrement pour que cet enregistrement devienne jouable
				$sql = 'UPDATE enregistrement 
				SET validation =  ' .$db->escape((string) $this->valid ) . ' 
				WHERE enregistrementID="'.$this->enregistrement .'" ' ;
				$db->query($sql);
				
			// Requête de modification du score de l'Oracle dont la description est élevée au rang de prediction divine
			
				updateScoreOracleDruideAccepte($this->oracle,$iso[$this->userlang],$this->enregistrement);
		//Requête de modification du score du Druide l'accomplissement de son fastidieux travail d'inquisition
			//récupération du score précédent;
			updateScoreDruideArbitrage($this->druid,$iso[$this->userlang],$this->pointsDr);

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

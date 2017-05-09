<?php
require_once("./models/item.factory.class.php");
require_once("./models/card.class.php");
require_once("./models/userlvl.class.php");
require_once("./controllers/traces.handler.class.php");

class diviner_game
{
	private $mode = '';

	private $errors = array();
	private $lang = array();
	private $nivcarte = '';
	private $th ;


	private $userlang = '';
	private $user= '';
	private $diviner= '';

	private $raisin ='';
	private $res2 = '';
	private $card = '';
	private $res4 = '';
	private $result= '';
	private $sanction ='';
	private $score='';


	private $adresse = '';
	private $reussie = 'en cours';
	private $mess='';


	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
        {
			if($this->selectpartie()){
				$this->update();
			}
			 return $this->display();
        }

        return false;
	}

	private function init()
	{
		//récupération des informations de base : id user et sa langue
		$this->user = user::getInstance();
		$this->diviner = $this->user->id;

		if(isset($_SESSION["langDevin"])){
			$this->userlang = $_SESSION["langDevin"];
		}
		else{
			$this->userlang = $this->user->langGame;
		}
		$this->th = new TracesHandler();
		return true;
	}

	private function selectpartie(){
		$res = false;
		try{
			$recordingFactory = new ItemFactory($this->diviner,$this->user->langGame);
			$this->raisin = $recordingFactory->get_recording(ItemFactory::VALID_RECORDING_NOT_ME);
			if(is_object($this->raisin)){
				// construction de l'adresse de l'enregistrement à  partir du nom du fichier son
				$this->adresse = "enregistrements/".$this->raisin->cheminEnregistrement;

				//On vérifie ici que l'enregistrement est bien sur le serveur
				if (file_exists($this->adresse)){
					//récupération du contenu de la carte
					$this->card = new Card($this->raisin->carteID);
					//Gestion du temps
					$sv = new ScoreValues();
					$this->time = $sv->get_augur_time($this->user->userlvl, $this->raisin->duration);

					// récupération du pseudo de l'oracle pour savoir qui on écoute
					require_once("./sys/db.class.php");
					$db = db::getInstance();
					$sql = 'SELECT username
							FROM user WHERE userid ="'.$this->raisin->idOracle.'"';
					if($db->query($sql) && ($db->num_rows() > 0)){
						$this->res2= $db->fetch_assoc();
						//récupération du pseudo du créateur de la carte
						$sql = 'SELECT username
							FROM user WHERE userid ="'.$this->card->get_author().'"';
						if($db->query($sql) && ($db->num_rows() > 0)){
							$this->res4= $db->fetch_assoc();
							$res=true;
						}
						else{
							throw new Exception("Pas pu récupérer le nom de l'auteur '".print_r($this->raisin,true)."'");
						}
					}
					else{
						throw new Exception("Pas pu récupérer le nom de l'oracle '".$this->card->get_author()."'");
					}
				}
			}
			else{
				array_push($this->errors,'NoGame');
			}
		}
		catch(Exception $e){
			array_push($this->errors,'NoGame');
		}
		return $res;
	}

	private function update(){
		//update dans ce contexte, crée uniquement une nouvelle partie
		//la vue appellera diviner timeout/result selon la situation
		$this->th->augur_start_play($this->raisin->enregistrementID,
											 $this->raisin->OracleLang,
											 $this->raisin->nivcarte);
	}

	private function display()
	{
		include('./views/diviner.game.html');

        return true;
	}
}

?>

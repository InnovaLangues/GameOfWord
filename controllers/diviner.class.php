<?php
require_once("./models/item.factory.class.php");
require_once("./models/card.class.php");

class diviner_game
{
	private $mode = '';

	private $errors = array();
	private $lang = array();
	private $motadeviner='';
	private $nivcarte = '';


	private $userlang = '';
	private $user= '';
	private $diviner= '';
	private $pointsSanction ='';

	private $raisin ='';
	private $res2 = '';
	private $card = '';
	private $res4 = '';
	private $result= '';
	private $sanction ='';
	private $score='';


	private $carteValide = false;

	private $adresse = '';
	private $reussie = 'en cours';
	private $temps='';
	private $mess='';


	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
        {
			$this->sanctionLastPartie();
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
		$this->userlvl = userlvl::getInstance();
		$this->time = $this->userlvl->get_time();

		//récupération des points de sanction
		$this->pointsSanction = loosePointsDevin;


		if(isset($_SESSION["langDevin"])){
			$this->userlang = $_SESSION["langDevin"];
		}
		else{
			$this->userlang = $this->user->langGame;
		}
		$this->temps = date("d/m/Y H:i:s");
		unset($_SESSION["motDeviner"]); //permet de supprimer la sécurité qui empêche le joueur de s'ajouter des points à l'infini
		unset($_SESSION["timeOutOracle"]); //permet de supprimer la sécurité qui empêche le joueur d'enlever des points à l'oracle à l'infini

		return true;
	}

	private function sanctionLastPartie()
	{ // fonction qui permet de vérifier l'état de la dernière partie et de sanctionner le joueur de 5 pts s'il a quitté la partitatut = "en cours")
		include_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();
	    $db =  db::getInstance();
		$sql =  "SELECT *
			FROM parties WHERE idDevin = \"".$this->diviner."\"
			ORDER BY parties.tpsDevin DESC
			LIMIT 1";
			$res=$db->query($sql);
			$this->sanction = mysqli_fetch_assoc($res);

		if($this->sanction['reussie'] == "en cours"){

		   $sql = "SELECT *
            	FROM sanctionCarte
                WHERE idDevin ='".$this->diviner."' AND enregistrementID='".$this->sanction['enregistrementID']."'";
                $res = $db->query($sql);
                $this->existSanction = mysqli_num_rows($res);
				if($this->existSanction == 0){
					//Ajout de la carte sanctionné dans la BDD
					$sql = "INSERT INTO sanctionCarte
					(idDevin,enregistrementID)
					VALUES (".$this->diviner.",".$this->sanction['enregistrementID'].")";

                   	if( $res = $db->query($sql)){
						$sql = "SELECT scoreDevin, scoreGlobal
						FROM score
						WHERE userid ='".$this->diviner."' AND langue='" . $lang_iso->french_for($this->userlang) . "'";
						$res = $db->query($sql);
						$this->score = mysqli_fetch_assoc($res);

						if ($this->score['scoreDevin'] >= $this->pointsSanction) { #à modifier avec un fichier config


							$this->score['scoreDevin']-=$this->pointsSanction;
							$this->score['scoreGlobal']-=$this->pointsSanction;

							$sql='UPDATE score
							SET scoreDevin="'.$this->score['scoreDevin'].'", scoreGlobal ="'.$this->score['scoreGlobal'].'"
							WHERE userid="'.$this->diviner.'" AND langue="' . $lang_iso->french_for($this->userlang) . '"';
							$res=$db->query($sql);
							array_push($this->lang,"sanction");
						}
						else
						{
					 		array_push($this->lang,"sanction_without_points");

						}
		      	    }
				}
		}
	}

	private function selectpartie(){
		$res = false;
		try{
			$recordingFactory = new ItemFactory($this->diviner,$this->userlang);
			//$this->user->langGame); langGame n'a pas l'air d'être ce qu'on pense
			$this->raisin = $recordingFactory->get_recording(ItemFactory::VALID_RECORDING_NOT_ME);
			if(is_object($this->raisin)){
				// construction de l'adresse de l'enregistrement à  partir du nom du fichier son
				$this->adresse = "enregistrements/".$this->raisin->cheminEnregistrement;

				//On vérifie ici que l'enregistrement est bien sur le serveur
				if (file_exists($this->adresse)){
					//récupération du contenu de la carte
					$carte = new Card($this->raisin->carteID);
					$this->card = $carte;

					// récupération du pseudo de l'oracle pour savoir qui on écoute
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
							throw new Exception("Pas pu récupérer le nom de l'auteur '$this->raisin->idOracle'");
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
		$this->setcarteValide($res);
		return $res;
	}

	private function update()
	{
		//Insertion des informations dans la table parties
		//connexion à  la bd
			$db = db::getInstance();

			 $sql = 'INSERT INTO parties
				(enregistrementID,idDevin,tpsDevin,reussie)
					VALUES(' .
						$db->escape((string) $this->raisin->enregistrementID).','.
						$db->escape((string) $this->diviner) . ','.
						$db->escape((string) $this->temps) . ','.
						$db->escape((string) $this->reussie).')';
				$db->query($sql);
			return false;
	}

	private function display()
	{
		include('./views/diviner.game.html');

        return true;
	}

    public function setcarteValide ($state){
		$this->carteValide=$state;
	}
}

?>

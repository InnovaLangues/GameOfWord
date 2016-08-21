<?php
require_once("./models/card.class.php");

class diviner_timeout
{
	private $mode = '';

	private $user = '';
	private $diviner = '';
	private $devinName='';
	private $oracle = '';

	private $carte = '';
	private $reussie ='non';


	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
		{
			$this->carte_et_scoreOracle();
			$this->updateparties();
			return $this->display();
		}
		return false;
	}

	private function init()
	{
		//récupération des informations de base : userid
		$this->user = user::getInstance();
		$this->diviner = $this->user->id;
		$this->devinName = $this->user->username;
		return true;
	}

	private function carte_et_scoreOracle()
	{
		require_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();

		if(!isset($_SESSION["timeOutOracle"])){
			// récupération d'enregistrementID pour récupérer l'id de l'Oracle et l'id de la carte
			//connexion à la BD
			$db = db::getInstance();

			//Récupération de enregistrementID
			$sql = 'SELECT `enregistrementID` FROM `parties` WHERE `idDevin`="'.$this->diviner.'" ORDER BY `tpsDevin` DESC LIMIT 1 ';
			$db->query($sql);
			require_once("./models/recording.class.php");
			$this->rec= new Recording($db->fetch_assoc()['enregistrementID']);

			//mise à jour des scores
			require_once('./controllers/update_score_coeff.php');
			$sh = new ScoreHandler($this->diviner, ScoreHandler::AUGUR, $this->rec);
			$sh->update_scores(false); //because they lost…

			// récupération du contenu de la carte avec carteID
			$this->carte = new Card($this->rec->get_card_id());
			$_SESSION["timeOutOracle"]=true;

			return false;
		}
		else{
			header('Location: index.php?page.home.html');
			return false;
		}
	}

	private function updateparties()
	{
		// Requête de mise à jour de la table partie
			$db = db::getInstance();
			$sql = 'UPDATE parties
					SET  reussie='.$db->escape((string) $this->reussie).'
					WHERE idDevin='.$this->diviner.' ORDER BY tpsDevin DESC LIMIT 1 ';
			$db->query($sql);
			//for dynamic notification don't want to take the time to understand them…
			$_SESSION["notif"]["notification_error"]["Devin"] = 'diviner_timeout';
		return false;
	}

	private function display()
	{
		include('./views/diviner.timeout.html');
		  return true;
	}
}

?>

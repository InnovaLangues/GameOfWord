<?php
require_once('./models/recording.class.php');
class diviner_result
{
	private $mode = '';
	private $user = '';
	private $devin = '';
	private $oracle = '';
	private $lang = '';
	private $devinName ='';

	private $reussie = 'oui';

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() ){
			$this->score();
			return $this->display();
		}
		return false;
	}

	public function __construct(){
		require_once('./sys/load_iso.php');
	}

	private function init()
	{
		$lang_iso = new IsoLang();
		$db = db::getInstance();
		//Récupération des informaions de base: userid
		$this->user = user::getInstance();
		$this->devin = $this->user->id;
		$this->devinName = $this->user->username;
		$this->lang = $_SESSION["langDevin"];
		return true;
	}

	private function score()
	{
		if(!isset($_SESSION["motDeviner"]))
		{
			$lang_iso = new IsoLang();
			//Récupération des infos nécessaires
			$db = db::getInstance();
			$query = "SET @id_enr := null;
				UPDATE `parties` SET  `reussie`=".$db->escape((string) $this->reussie).", `enregistrementID` = (SELECT @id_enr := `enregistrementID`) WHERE idDevin='".$this->devin."' ORDER BY tpsDevin DESC LIMIT 1;
				SELECT @id_enr;";
			$db->multi_query_last_result($query);
			$this->record_id = $db->fetch_assoc()["@id_enr"];
			$tmpRecording  = new Recording($this->record_id);
			$this->oracle  = $tmpRecording->get_oracle_id();

			//mise à jour des scores
			require_once('./controllers/update_score_coeff.php');
			$sh = new ScoreHandler($this->devin, ScoreHandler::AUGUR, $tmpRecording);
			$sh->update_scores(true); //because they won…

			// récupération du contenu de la carte avec carteID
			require_once("./models/card.class.php");
			$this->carte = new Card($tmpRecording->get_card_id());
			$_SESSION["motDeviner"] = true;//pour éviter de s'ajouter des points à l'infini avec des refresh
			return true;
		}
		else{
			header('Location: index.php?page.home.html');
			return false;
		}
	}

	private function display()
	{
		//for dynamic notification don't want to take the time to understand them…
		$_SESSION["notif"]["notification_done"]["Devin"] = 'points';
		include('./views/diviner.result.html');
		return true;
	}
}

?>

<?php
require_once("./models/card.class.php");

class diviner_timeout
{
	private $mode = '';

	private $partieID;

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

	public function process(){
		try{
			if ( $this->init() ){
				$this->init_card_and_recording();
				$this->update_score();
				return $this->display();
			}
		}
		catch(Exception $e){
			header('Location: index.php');
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

	private function init_card_and_recording(){
		$res = false;
		require_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();
		// récupération d'enregistrementID pour récupérer l'id de l'Oracle et l'id de la carte
		//connexion à la BD
		$db = db::getInstance();
		//Récupération de enregistrementID
		$sql = "SELECT `enregistrementID`, `partieID` FROM `parties` WHERE `idDevin`='$this->diviner' AND `reussite`='en cours' ORDER BY `tpsDevin` DESC LIMIT 1 ";
		if($db->query($sql)){
			require_once("./models/recording.class.php");
			$tmpTable = $db->fetch_assoc();
			$this->partieID = $tmpTable['partieID'];
			$this->rec= new Recording($tmpTable['enregistrementID']);

			// récupération du contenu de la carte avec carteID
			$this->carte = new Card($this->rec->get_card_id());
			$res = true;
		}
		else{
			throw new Exception($query."yielded no result, there's a problem in the consistance of the db, maybe the user toyed with the url…");
		}
		return $res;
	}

	private function update_score(){
		require_once("./controllers/traces.handler.class.php");
		$th = new TracesHandler();
		if(isset($_GET['duration'])){
			$duration = $_GET['duration'];
		}
		else{
			$duration = false;
		}
		$th->augur_loss($this->partieID, $this->rec->get_id(), $duration);
		//for dynamic notification don't want to take the time to understand them…
		$_SESSION["notif"]["notification_error"]["devin"] = 'diviner_timeout';
		return false;
	}

	private function display()
	{
		include('./views/diviner.timeout.html');
		  return true;
	}
}

?>

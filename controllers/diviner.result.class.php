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
		if(isset($_GET['recording_id'])){
			$this->recording_id=$_GET['recording_id'];
			if(isset($_GET['duration'])){
				$this->duration=$_GET['duration'];
				return true;
			}
		}
		return false;
	}

	private function score(){
		require_once("./controllers/traces.handler.class.php");
		$th = new TracesHandler();
		$th->augur_win($this->recording_id, $this->duration);
		//used in view
		$tmpRecording  = new Recording($this->recording_id);
		$this->oracle  = $tmpRecording->get_oracle_id();
		$this->rec_level = $tmpRecording->get_level();

		// récupération du contenu de la carte avec carteID
		require_once("./models/card.class.php");
		$this->carte = new Card($tmpRecording->get_card_id());
		return true;
	}

	private function display()
	{
		//for dynamic notification don't want to take the time to understand them…
		$_SESSION["notif"]["notification_done"]["devin"] = 'points';
		include('./views/diviner.result.html');
		return true;
	}
}

?>

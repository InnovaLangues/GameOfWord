<?php
require_once("./models/item.factory.class.php");
require_once("./models/card.class.php");
require_once('./sys/db.class.php');
require_once("./controllers/traces.handler.class.php");


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
	private $revoke=false;

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	private function readyToApplyDecision(){
		return isset($this->invalidate);
	}

	private function invalidate(){
		$res = "not set";
		if(isset($this->invalidate)){
			$res = $this->invalidate;
		}
		return $res;
	}

	public function process()
	{
		if ( $this->init() )
		{
			if(!$this->readyToApplyDecision()){
				//to be able to use it even without using the druid interface
				$this->selectpartie();
			}
			return $this->display_et_scores();
		}
		return false;
	}

	private function init()
	{
		$res = true;
		//récupération des informations de bases : userid, langue et la date
		$this->user = user::getInstance();
		$this->druid = $this->user->id;
		$this->userlang = $this->user->langGame;
		//cas où on effectue un traitement
		if(isset($_POST["enregistrement1"])){
			$this->enregistrement = $_POST["enregistrement1"];
		}
		if(isset($_POST["oracle"])){
			$this->oracle = $_POST['oracle'];
		}
		if(isset($_POST['invalidate'])){
			$this->invalidate = true;
		}
		if(isset($_POST['validate'])){
			$this->invalidate = false;
		}
		if(isset($_POST['revoke'])){
			$this->revoke = true;//revoking a decision by another druid
		}
		if( (($this->enregistrement!='') && ($this->oracle!='') && isset($this->invalidate))
		  !=(($this->enregistrement!='') || ($this->oracle!='') || isset($this->invalidate)) ){
			$res = false;
			array_push($this->errors, 'druidNotReady');
		}
		return $res;
	}
//Revoir dans le cas où toutes les cartes ont été supprimés du serveur.
	private function selectpartie(){
		try{
			//connexion à la BD
			$this->partie=false;
			$recordingFactory = new ItemFactory($this->druid,$this->user->langGame);
			$this->raisin = $recordingFactory->get_recording(ItemFactory::LIMBO_RECORDING_ME_IF_POSSIBLE);
			if(is_object($this->raisin) && file_exists("enregistrements/".$this->raisin->cheminEnregistrement)){
				//construction de l'adresse de l'enregistrement à partir du nom du fichier
				$this->adresse = "enregistrements/".$this->raisin->cheminEnregistrement ;
				$this->enregistrement = $this->raisin->enregistrementID;
				// récupération du pseudo du joueur arbitré
				$db = db::getInstance();
				 $sql = 'SELECT username FROM user WHERE userid ="'.$this->raisin->idOracle.'"';
				 $this->result=$db->query($sql);
				 $this->res2= mysqli_fetch_assoc($this->result);

				//récupération de la carte jouée
				$this->card = new Card($this->raisin->carteID,'./views/card.inline.display.php');
				$this->partie=true;
				require_once("./models/userlvl.class.php");
				$sv = new ScoreValues();
				$this->card->set_forbidden_count($sv->get_forbidden_count($this->raisin->nivpartie));
				unset($sv);
   			}
			else{//$this->partie reste faux
				array_push($this->errors, 'noEnregistrement');
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

	private function display_et_scores(){
		require_once('./sys/load_iso.php');
		$lang_iso = new IsoLang();
		if( ($this->invalidate()!==true)
		 && ($this->invalidate()!==false) ){
			 include('./views/druid.arbitrage.html');
		 }
		else{
			$th = new TracesHandler();
			//could change $this->invalidate to validate…
			$th->druid_validate($this->enregistrement, !$this->invalidate(), $this->revoke);
			//for dynamic notification don't want to take the time to understand them…
			$_SESSION["notif"]["notification_done"]["Druide"] = 'pointsDruide';
			header('Location: index.php?page.home.html');
		}
		return true;
	}

}

?>

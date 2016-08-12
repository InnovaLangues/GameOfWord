<?php
require_once("./models/card.class.php");

class diviner_timeout
{
	private $mode = '';

	private $user = '';
	private $diviner = '';
	private $devinName='';
	private $oracle = '';

	private $previousSGO = 0;
	private $previousSO = 0;


	private $res = '';
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
		$this->userlvl = userlvl::getInstance();
		$this->points= $this->userlvl->get_points();


		return true;
	}

	private function carte_et_scoreOracle()
	{
		require_once('./sys/load_iso.php');
		require_once('./controllers/update_score_coeff.php');

		if(!isset($_SESSION["timeOutOracle"])){
			// récupération d'enregistrementID pour récupérer l'id de l'Oracle et l'id de la carte
			//connexion à la BD
			$db = db::getInstance();

			//Récupération de enregistrementID
			$sql = 'SELECT enregistrementID FROM parties WHERE idDevin="'.$this->diviner.'" ORDER BY tpsDevin DESC LIMIT 1 ';
	        $res1=$db->query($sql);
	        $this->res2= mysqli_fetch_assoc($res1);

	       // récupération de l'id de l'oracle et de la carte grâce à enregistrementID
			$sql = 'SELECT idOracle,carteID,OracleLang
	                    FROM enregistrement WHERE enregistrementID='.$this->res2['enregistrementID'].'';
	        $res1=$db->query($sql);
	        $res3= mysqli_fetch_assoc($res1);

	        $this->oracle = $res3['idOracle'];

			// récupération du contenu de la carte avec carteID
			$carte = new Card($res3['carteID']);
	        $this->res= $carte->dirtify();

		// Requête de modification des scores de l'Oracle qui a fait une description non trouvée par le devin
		  $lang_iso = new IsoLang();
			updateScoreOracleDevinEchec($this->oracle,$lang_iso->french_for($res3["OracleLang"]),$this->res2['enregistrementID']);
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

<?php
require_once("./models/card.class.php");
require_once('./models/card.class.php');

class oracle_card_byid
{

	private $errors = array();
	private $userlang ;
	private $user ;
	private $oracle ;
	public  $carteId ;

	private $card;
	private $mode;
	private $time;


	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
        {
            $this->selectcarte();
            return $this->display();
        }
        return false;
	}

	private function init()
	{
		// récupération de l'id de l'utilisateur et de sa langue à étudier
		$this->user = user::getInstance();
		$this->userlang = $this->user->userlang;
		$this->oracle = $this->user->id;


		//rÃ©cupÃ©ration du l'id de la carte dans la zone de texte
		$this->submit = isset($_POST['submit_form']);
		if ( $this->submit )
		{

		    $this->carteId = isset($_POST['carteId']) ? trim($_POST['carteId']) : '';
		}
		else if( isset($_SESSION["idCard"])){

			$this->carteId = isset($_SESSION['idCard']) ? trim($_SESSION['idCard']) : '';
		}
		return true;
	}

	public function selectcarte(){
		$res = true;
		if ( ( !$this->submit || $this->errors ) && !(isset($_SESSION["idCard"])) ){
			$res = false;
		}
		else{
			// Sélection "dans ce cas là on laisse les gens jouer avec n'importe quelle carte (pour le moment) à terme on pourrait utiliser la item factory pour vérifier que le joueur a droit…"
			$this->card = new Card((int) $this->carteId);
			$sv = new ScoreValues();
			$this->time = $sv->get_oracle_time($this->user->userlvl);
			$this->card->set_forbidden_count($sv->get_forbidden_count($this->user->userlvl));
		}
		return $res;
	}

	private function display(){
		if(isset($_POST['submit_form']) || isset($_SESSION["idCard"])){
			if(is_object($this->card)){
				if (isset($_POST['carteId']) && (int) $_POST['carteId'] !== (int) $this->card->get_id()){
					array_push($this->errors, 'no_card');
					include('./views/oracle.card.byid.html');
				}
				else{
					include('./views/oracle.card.display.html');
				}
			}
			else{
				array_push($this->errors,'unavailable_card');
					include('./views/oracle.card.byid.html');
			}
		}else{
			include('./views/oracle.card.byid.html');
		}
        return true;
	}
}

?>

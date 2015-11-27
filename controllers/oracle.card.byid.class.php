<?php
require_once("./models/card.class.php");

class oracle_card_byid
{
	
	private $errors = array();
	private $nivcarte = '';
	private $userlang = '';
	private $user= '';
	private $oracle= '';
	public $carteId='';
	private $boobool= true;
	
	private $res='';
	private $result= '';
	private $mode = '';


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
	
	 public function selectcarte()
	{
	// modifications 08/02/2015
	$db = db::getInstance();
	//if ( !$this->submit)
	if ( ( !$this->submit || $this->errors ) && !(isset($_SESSION["idCard"])) )
	{
		return false;
	}
	// Sélection "dans ce cas là on laisse les gens jouer avec n'importe quelle carte (pour le moment) à terme le rôle pourrait être fixé selon les besoins"

	$carte = new Card($this->carteId);
    $this->res = $carte->dirtify();


	// ligne pour permettre la récupération du niveau de la carte dans la table enregistrement
	$this->res['nivcarte'] = $this->res['niveau'];
	}

	private function display()
	{
		if(isset($_POST['submit_form']) || isset($_SESSION["idCard"]))
		{	
			if(isset($this->res['carteID'])){

			
				if (isset($_POST['carteId']) && $_POST['carteId'] != $this->res['carteID'])
				{
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

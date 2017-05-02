<?php
require_once("./models/item.factory.class.php");
require_once("./models/userlvl.class.php");

class oracle_alea_exist
{

	private $errors = array();
	private $nivcarte = '';
	private $userlang = '';
	private $user= '';
	private $oracle= '';
	public $card;

	private $res='';
	private $result= '';
	private $mode = '';
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
		// récupération de userid
		$this->user = user::getInstance();
		$this->oracle = $this->user->id;
		$this->userlang = $this->user->langGame;

		return true;
	}

	private function selectcarte(){
		// récupération de plusieurs cartes possibles
		$cardFactory = new ItemFactory($this->oracle,$this->user->langGame);
		$card = $cardFactory->get_card(ItemFactory::CARD_NOT_ME);
    	if(is_object($card) && get_class($card)=="Card"){
    		$this->card=$card;

			//Gestion des règles
			$sv = new ScoreValues();
			$this->time = $sv->get_oracle_time($this->user->userlvl);
			$this->card->set_forbidden_count($sv->get_forbidden_count($this->user->userlvl));

    		$res = true;
    	}
		else{
			array_push($this->errors,'noCardBD');
			//for dynamic notification don't want to take the time to understand them…
			$_SESSION["notif"]["notification_error"]["Oracle"] = 'noCardBD';
			$res = false;
		}
		return $res;
	}



	private function display()
	{
		include('./views/oracle.card.display.html');
        return true;
	}
}

?>

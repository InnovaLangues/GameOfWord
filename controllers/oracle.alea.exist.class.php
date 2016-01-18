<?php
require_once("./models/item.factory.class.php");

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

		$this->userlvl = userlvl::getInstance();
		$this->time = $this->userlvl->get_time();
		
		// Ici il faudra récupérer le niveau de l'utilisateur pour n'afficher sur tel ou tel nb de mots tabous.
		// récupérer scoreID dans user, puis scoreglobal dans score. si score = tant, $niveau = facile, moyen ou difficile
		// En fonction, ne récupérer que le mot, les deux mots tabous ou les 5 mots tabous. Sinon on peut vider $res de ses mots tabous.
		
		return true;
	}

	private function selectcarte(){
		// récupération de plusieurs cartes possibles
		$cardFactory = new ItemFactory($this->oracle,$this->user->langGame);
		$card = $cardFactory->get_card(ItemFactory::CARD_NOT_ME);
    	if(is_object($card) && get_class($card)=="Card"){
    		//echo "<script>window.alert('C bon ça');</script>";
    		$this->card=$card;
    		$res = true;
    	}
		else{
			array_push($this->errors,'noCardBD');
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

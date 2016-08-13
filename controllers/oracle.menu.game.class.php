<?php
require_once("./controllers/lexInnova.class.php");
unset($_SESSION["idCard"]);

class oracle_menu_game
{
	private $userlang = '';
	private $user= '';
	private $oracle ='';
	private $mode = '';

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
		{
				return $this->display();
		}
		return false;
	}

	private function init()
	{
		$this->user = user::getInstance();
		/*useless*/
		$this->oracle = $this->user->id;
		$this->userlang = $this->user->userlang;
		/*Lexicon*/
		$this->lexicon = new LexInnovaLink($this->user);
		if($this->lexicon->count_entries() > 0){
			require_once("./models/item.factory.class.php");
			$cardFactory = new ItemFactory($this->oracle,$this->user->langGame);
			$card_id = $cardFactory->get_card_id(ItemFactory::CARD_FROM_LEXICON,
							$this->lexicon->list_entries());
			if($card_id != -1){
				$_SESSION["idCard"] = $card_id;
			}
		}
		return true;
	}


	private function display()
	{
		include('./views/oracle.menu.html');
		  return true;
	}
}

?>

<?php
require_once("lexInnova.class.php");

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
		$this->oracle = $this->user->id;
		$this->userlang = $this->user->userlang;
		$this->lexicon = new LexInnovaLink($this->user);
		return true; 
	}


	private function display()
	{
		include('./views/oracle.menu.html');
        return true;
	}
}

?>

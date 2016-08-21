<?php

class oracle_timeout_card
{
	private $mode = '';
	private $notif = array();

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
        {
            $this->check();
            $this->validate();
            return $this->display();
        }
        return false;
	}

	private function init()
	{
		//for dynamic notification don't want to take the time to understand them…
		$_SESSION["notif"]["notification_error"]["oracle"] = 'oracle_card_timeout';
		return true;
	}

	private function check()
	{
		return false;
	}

	private function validate()
	{
		return false;
	}

	private function display()
	{
		include('./views/page.home.html');
        return true;
	}
}

?>

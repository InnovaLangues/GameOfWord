<?php

class oracle_result
{
	//ceci est la classe result
	private $submit = false;
	private $file = '';
	private $userlang = '';
	private $user= '';
	private $oracle= '';
	private $mode = '';
	private $errors = '';
	private $result = '';
	private $pointsPerdus = false;

	public function set_mode($mode)
	{
		$this->mode = $mode;
	}

	public function process()
	{
		if ( $this->init() )
		  {
				$this->score();
				return $this->display();
		  }
		  return false;
	}

	private function init()
	{
		// récupération de l'id de l'utilisateur et de sa langue étudiée
		$this->user = user::getInstance();
		$this->userlang = $this->user->userlang;
		$this->oracle = $this->user->id;
		return true;
	}

	private function score()
	{


		if(!isset($_SESSION["Record"]))
		{
			if ( isset($_POST['submit_form']) )
			{  //for dynamic notification don't want to take the time to understand them…
				$_SESSION["notif"]["notification_done"]["oracle"] = 'pointsOracle';
			}
			else{
					//for dynamic notification don't want to take the time to understand them…
					$_SESSION["notif"]["notification_error"]["oracle"] = 'giveUpOracle';
			}
			return true;

		}
		else{
			unset($_SESSION["Record"]);
			header('Location: index.php?page.home.html');
		}

	}

	private function display()
	{
		include('./views/page.home.html');
		  return true;
	}
}

?>

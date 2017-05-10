<?php
require_once("./models/score.class.php");
require_once("./models/user.class.php");
class Score{
	private $user;
	private $languages = array();
	private $global = true;
	private $view = './views/score.html';
	private $mode = '';

	public function __construct(){
		$this->user = user::getInstance();
		require_once('./sys/db.class.php');
		$this->db = db::getInstance();
		include_once('./sys/load_iso.php');
		$this->lang_iso = new IsoLang();

		$sql = "SELECT DISTINCT `langue` FROM `stats` WHERE `stats`.`userid` = '".$this->user->id."'";
		$this->db->query($sql);
		if($this->db->has_result()){
			while($tempObj = $this->db->fetch_object()){
				$this->languages[$tempObj->langue] = $tempObj->langue;
				//new ScoreTable creates other database queries, so we separate both processes
			}
			foreach($this->languages as $lang){
				$this->languages[$lang] = new ScoreTable($this->user, $lang);
				$this->languages[$lang]->get_score_table(true);
			}
			$this->languages[GlobalScoreTable::ALL_LANG] =
				new GlobalScoreTable($this->user);
			$this->languages[GlobalScoreTable::ALL_LANG]->get_score_table(true);
		}
	}

	public function set_mode($mode){
		$this->mode = $mode;
	}

	public function process(){
		$theScore = $this;
		include($this->view);
		return true;
	}
}

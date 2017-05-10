<?php
class EmptyScoreLine{
	protected $highlight;
	protected $is_global_score;
	protected $view = './views/empty_score_line.table.display.php';

	public function  __construct($highlight=false, $view=false, $is_global=false){
		if($view !== false){
			$this->view = $view;
		}
		$this->highlight = $highlight;
		$this->is_global_score = $is_global;
	}

	public function __toString(){
		$scoreLine = $this;
		include($this->view);
		return $res;
	}

	public function isEmpty(){
		return true;
	}

	public function isGlobal(){
		return $this->is_global_score;
	}
}

class ScoreLine extends EmptyScoreLine {
	protected $user_name;
	protected $oracle;
	protected $druid;
	protected $augur;
	protected $global;
	protected $position;
	protected $o_wl;
	protected $o_en;
	protected $o_er;
	protected $d_a;
	protected $d_er;
	protected $d_c;
	protected $a_en;
	protected $a_wl;
	protected $o_a;

	protected $view = './views/score_line.table.display.php';

	//see create_scoring_table() for $compositeObject structure
	public function __construct($compositeObject, $p, $h=false, $v=false, $is_global = false){
		parent::__construct($h, $v, $is_global);
		$this->user_name = $compositeObject->name;
		$this->oracle = $compositeObject->o;
		$this->druid = $compositeObject->d;
		$this->augur = $compositeObject->a;
		$this->global = $compositeObject->g;
		$this->position = $p;
		//oracle stats
		$this->o_wl = $compositeObject->o_wl;
		$this->o_er = $compositeObject->o_er;
		$this->o_en = $compositeObject->o_en;
		$this->d_a = $compositeObject->d_a;
		$this->d_er = $compositeObject->d_er;
		$this->d_c = $compositeObject->d_c;
		$this->a_en = $compositeObject->a_en;
		$this->a_wl = $compositeObject->a_wl;
		$this->o_a = $compositeObject->o_a;
	}

	public function get_oracle_recordings(){
		return $this->o_en;
	}
	public function get_oracle_errors(){
		return $this->o_er;
	}
	public function get_oracle_perc(){
		return $this->o_wl;
	}
	public function get_oracle_forfeits(){
		return $this->o_a;
	}
	public function get_druid_arbitration(){
		return $this->d_a;
	}
	public function get_druid_arbitration_err(){
		return $this->d_er;
	}
	public function get_druid_cards(){
		return $this->d_c;
	}
	public function get_augur_recordings(){
		return $this->a_en;
	}
	public function get_augur_perc(){
		return $this->a_wl;
	}

//stats

//position
	public function get_position(){
		return $this->position;
	}

//scores
	public function get_user_name(){
		return $this->user_name;
	}

	public function get_oracle_score(){
		return $this->oracle;
	}

	public function get_druid_score(){
		return $this->druid;
	}

	public function get_augur_score(){
		return $this->augur;
	}

	public function get_global_score(){
		return $this->global;
	}

	public function isEmpty(){
		return false;
	}
}

class GlobalScoreLine extends ScoreLine{
	protected $language_list;
	protected $view = "./views/global_score_line.table.display.php";
	//string containing the list of languages used…
	public function __construct($compositeObject, $p, $h=false, $v=false){
		parent::__construct($compositeObject, $p, $h, $v, true);
		$this->language_list = $compositeObject->l;
	}

	public function get_language_list(){
		return $this->language_list;
	}
}

class ScoreTable{
	const LANGUAGE_ERROR = 123456;
	protected $user ;
	private $language ;
	protected $scores = array();
	protected $incomplete_scores = array();
	protected $the_scores;
	protected $db;
	protected $lang_iso;
	protected $nb_incomplete = 5;//number of lines, centered around the player to be displayed
	protected $view = './views/score_table.table.display.php';
	protected $line_view = './views/score_line.table.display.php';


	public function __construct($user=false, $language, $nb_incomplete = false,$view=false,$lineview=false){
		//views could use setters, but I don't need them…
		if($view!==false){
			$this->view = $view;
		}
		if($lineview!==false){
			$this->line_view = $lineview;
		}
		if($user !== false){
			$this->user = $user;
		}
		else{
			$this->user = user::getInstance();
		}
		if($nb_incomplete !== false){
			$this->nb_incomplete = $nb_incomplete;
		}
		require_once('./sys/db.class.php');
		$this->db = db::getInstance();
		include_once('./sys/load_iso.php');
		$this->lang_iso = new IsoLang();
		$this->set_language($language);
	}

	public function set_user($user_or_id){
		if(is_int($user_or_id)){
			require_once('./models/user.class.php');
			$this->user = new user($user_or_id);
		}
		else{
			$this->user = $user_or_id;
		}
	}

	public function get_user_name(){
		return $this->user->username;
	}

	public function set_language($l){
		try{
			$lang = $this->lang_iso->any_to_french($l);
		}
		catch(Exception $e){
			throw new Exception("set_language: '$l' is not a known language.", self::LANGUAGE_ERROR);
		}
		if($lang){
			$this->language = $lang;
			if(isset($sthis->scores)){
				unset($this->scores) ;
			}
		}
	}

	public function get_language(){
		return $this->language;
	}

	public function get_iso_language(){
		return $this->lang_iso->any_to_iso($this->language);
	}

	public function is_built(){
		return (count($this->scores)>0);
	}

	public function is_global(){
		return false;
	}

	private function create_scoring_table(){
		if(!$this->is_built()){
			//we have not yet run that method
			$query = "SELECT
					`stats`.`nbAbandons_oracle` as `o_a`,
					`stats`.`nbErreurs_oracle` as `o_er`,
					`stats`.`nbEnregistrements_oracle` as `o_en`,
					ROUND(`stats`.`nbSucces_oracle`*100/GREATEST(1,`stats`.`nbLectures_oracle`)) as `o_wl`,
					`stats`.`score_oracle` as `o`,
					`stats`.`nbArbitrages_druide` as `d_a`,
					`stats`.`nbErrArbitrage_druide` as `d_er`,
					`stats`.`nbCartes_druide` as `d_c`,
					`stats`.`score_druide` as `d`,
					`stats`.`nbEnregistrements_devin` as `a_en`,
					ROUND(`stats`.`nbMotsTrouves_devin`*100/GREATEST(1,`stats`.`nbEnregistrements_devin`)) as `a_wl`,
					`stats`.`score_devin` as `a`,
					(`stats`.`score_devin`+`stats`.`score_druide`+`stats`.`score_oracle`) as `g`,
					`user`.`username` as `name`
				FROM  `stats`,`user`
				WHERE `stats`.`langue`='".$this->get_iso_language()."'".
				" AND `user`.`userid`=`stats`.`userid`
				ORDER BY `g` DESC";
			$this->db->query($query);
			if($this->db->affected_rows() > 0){
				$this->scores=array();
				$pos = 1;
				while($tmpObj = $this->db->fetch_object()){
					$highlight = false;
					if($tmpObj->name == $this->get_user_name()){
						$highlight = true;
						$this->usr_pos = $pos;
					}
					array_push($this->scores,
						new ScoreLine($tmpObj, $pos, $highlight, $this->line_view));
					$pos++;
				}
				$this->make_incomplete();
			}
		}
		return $this->scores;
	}

	protected function make_incomplete(){
		if($this->is_built()){
			//remove scores too far behind the user
			//user nth, index = n-1, splice from n
			$this->incomplete_scores = $this->scores;
			if($this->usr_pos > $this->nb_incomplete/2){
				array_splice($this->incomplete_scores,
					$this->usr_pos+floor($this->nb_incomplete/2)
				);
			}
			if(count($this->scores) != count($this->incomplete_scores)){
				array_push($this->incomplete_scores, new EmptyScoreLine(false,false,$this->is_global()));
			}
			//remove scores between nb_incompleteth and too far before the user
			if($this->nb_incomplete+1 <
				$this->usr_pos - floor($this->nb_incomplete/2)){
					array_splice($this->incomplete_scores,
						$this->nb_incomplete,
						($this->usr_pos - floor($this->nb_incomplete/2))-$this->nb_incomplete,
						array(new EmptyScoreLine(false,false,$this->is_global()))
					);
			}
		}
	}

	public function get_score_table($complete=true){
		if(!$this->is_built()){
			$this->create_scoring_table();
		}
		if($complete){
			$this->the_scores = &$this->scores;
		}
		else{
			$this->the_scores = &$this->incomplete_scores;
		}
		return $this->the_scores;
	}

	public function __toString(){
		$scoreTable = $this;
		include($this->view);
		return $res;
	}
}

class GlobalScoreTable extends ScoreTable{
	const ALL_LANG = "global";
	protected $line_view = './views/global_score_line.table.display.php';
	protected $view = './views/global_score_table.table.display.php';

	public function is_global(){
		return true;
	}

	public function __construct($user=false, $nb_incomplete = false, $view=false, $lineview=false){
		try{
			parent::__construct($user=false, false, $nb_incomplete = false,$view=false,$lineview=false);
		}
		catch(Exception $e){
			if($e->getCode() != ScoreTable::LANGUAGE_ERROR){
				throw new Exception($e->getCode().": Une erreur envoyée par le constructeur de ScoreTable depuis GlobalScoreTable : ".$e->getMessage());
			}
		}
	}

	protected function create_scoring_table(){
		if(!$this->is_built()){
			//we have not yet run that method
			$query =
				"SELECT
					`stats`.`nbAbandons_oracle` as `o_a`,
					`stats`.`nbErreurs_oracle` as `o_er`,
					`stats`.`nbEnregistrements_oracle` as `o_en`,
					ROUND(`stats`.`nbSucces_oracle`*100/GREATEST(1,`stats`.`nbLectures_oracle`)) as `o_wl`,
					`stats`.`score_oracle` as `o`,
					`stats`.`nbArbitrages_druide` as `d_a`,
					`stats`.`nbErrArbitrage_druide` as `d_er`,
					`stats`.`nbCartes_druide` as `d_c`,
					`stats`.`score_druide` as `d`,
					`stats`.`nbEnregistrements_devin` as `a_en`,
					ROUND(`stats`.`nbMotsTrouves_devin`*100/GREATEST(1,`stats`.`nbEnregistrements_devin`)) as `a_wl`,
					`stats`.`score_devin` as `a`,
					(`stats`.`score_devin`+`stats`.`score_druide`+`stats`.`score_oracle`) as `g`,
					`user`.`username` as `name`,
					GROUP_CONCAT(`stats`.`langue` SEPARATOR '+') as `l`
				FROM `stats`,`user`
				WHERE `user`.`userid`=`stats`.`userid`
				GROUP BY `name`
				ORDER BY `g` DESC" ;

			$this->db->query($query);
			if($this->db->has_result()){
				$this->scores=array();
				$pos = 1;
				while($tmpObj = $this->db->fetch_object()){
					$highlight = false;
					if($tmpObj->name == $this->get_user_name()){
						$highlight = true;
						$this->usr_pos = $pos;
					}
					array_push($this->scores,
						new GlobalScoreLine($tmpObj, $pos,
							$highlight, $this->line_view));
					$pos++;
				}
				$this->make_incomplete();
			}
		}
		return $this->scores;
	}

	public function get_score_table($complete=true){
		if(!$this->is_built()){
			$this->create_scoring_table();
		}
		if($complete){
			$this->the_scores = &$this->scores;
		}
		else{
			$this->the_scores = &$this->incomplete_scores;
		}
		return $this->the_scores;
	}
}

<?php

class LexInnovaLink{
	private $user= '';
	//$this->oracle = $this->user->id;
	//$this->userlang = $this->user->userlang;
	private $lexiconUrl = "http://totoro.imag.fr/lexinnova/api/Lexinnova@user@/@lang@/cdm-headword/*/cdm-headword/?strategy=NOT_EQUAL" ;
	private $content ;


	public function __construct(){
		/**/
/*		$this->user = user::getInstance();
		if ($this->user->userlang == 'es'){
			$this->lang = "esp";
		}
		else{
			$this->lang = "fra";//pour qu'il y ait une alternative
		}
		
		$this->lexiconUrl = str_replace(array("@user@", "@lang"), array($this->user->id, $this->lang), $this->lexiconUrl);*/
		//mais là on va dire que c'est Titi et qu'il apprend l'espagnol
		$this->lexiconUrl = str_replace(array("@user@", "@lang@"), array("lzbk", "esp"), $this->lexiconUrl);
		echo $this->lexiconUrl;
		$this->content = simplexml_load_file($this->lexiconUrl);
	}

	public function has_content(){
		return ($this->content !== false);
	}

	public function count_entries(){
		$result = 0;
		if($this->has_content()){
			$result = count($this->content->xpath('//d:entry'));
		}
		return $result;
	}

	public function __toString(){
		$result = "";
		foreach ($this->content->xpath('d:entry/d:key') as $element){
			if($result != ""){
				$result .= ", ";
			}
			$result .= "'".$element."'";
		}
		return $this->count_entries()." entrées → ($result)";
	}
}
/**/
echo "youhouu";
$test = new LexInnovaLink();
echo "<!doctype html><html><head> <meta charset='utf-8'></head><body>$test</body></html>";

?>

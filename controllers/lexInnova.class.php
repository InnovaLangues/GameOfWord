<?php

class LexInnovaLink{
	private $user ;
	private $lexiconUrl = "http://totoro.imag.fr/lexinnova/api/Lexinnova@user@/@lang@/cdm-headword/*/cdm-headword/?strategy=NOT_EQUAL" ;
	private $content ;
	private $lang_codes = array("es"=>"esp",
								"fr"=>"fre");


	public function __construct($user){
		$this->user = $user ;
		$this->lexiconUrl = str_replace(array("@user@", "@lang@"),
			array($this->user->username,
				  $this->lang_codes[$this->user->langGame]),
			$this->lexiconUrl);
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
?>

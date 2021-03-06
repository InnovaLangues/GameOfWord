<?php
require_once('./sys/db.class.php');
class user
{
	const SESSION_DURATION = 600; // 600 secondes = 10 minutes
	const DEFAULT_LANG = 'en';
	public $id = false;
	public $username = '';
	public $useremail = '';
	public $useronline = false;
	public $userlang = '';
	public $userlvl = '';
	public $langGame = '';
	public $spoken_lang = '';

	public $lang = 'en';

	private static $_instance = null;
    public static function getInstance()
    {
        if ( !self::$_instance )
        {
            $class = __CLASS__;
            self::$_instance = new $class();
        }
        return self::$_instance;
    }
    public function __construct($id=false)
    {
		 if($id===false){
			 $this->login();
		 }
		 else{
			 $this->read($id);
		 }
    }
    private function __clone() {}

	// appelé par le constructeur : lire l'utilisateur si il est logué
	private function login()
	{
		$this->id = false;
		if ( isset($_SESSION['userid']) && intval($_SESSION['userid']) && $this->read(intval($_SESSION['userid'])) )
		{
			// mettre à jour le moment de dernier hit sur le site par le user
			$this->update_online_status();
		}

		// lire la langue sur la session ou utiliser celle par defaut
		$this->lang = $this->guess_lang();



		// stocker la langue sur la session
		$this->set_lang();
	}

	// user log out
	public function set_logout()
	{
		// mettre le dernier hit du user en outtime
		$this->update_online_status(true);

		// supprimer le user id de la session
        unset($_SESSION['userid']);

		$this->user = false;
	}

	// validation du formulaire de login: stocker l'id
	public function set_login($id)
	{
		$this->id = intval($id);
		$_SESSION['userid'] = intval($this->id);
	}

	// lire la langue à partir de la session
	public function guess_lang()
	{
		//return isset($_SESSION['lang']) ? $_SESSION['lang'] : self::DEFAULT_LANG;

		if(!isset($_SESSION["langDevin"] )){
			$_SESSION["langDevin"]= $this->langGame;

		}

		return $this->userlang;
	}

	// mettre à jour la session avec la langue
	public function set_lang($lang=false)
	{
		$_SESSION['lang'] = $lang === false ? $this->lang : $lang;
		return $this->update_lang($lang);
	}

	public function get_lang()
	{
		return $this->lang;
	}

	public function get_lang_lvl($lang){
		return isset($this->languages[$lang]) ? $this->languages[$lang] : false ;
	}

	private function update_lang($lang=false) {
		$db = db::getInstance();
		if ($lang === false) { return false;}
		$sql = 'UPDATE user
					SET userlang = ' . $lang . '
					WHERE userid = ' . intval($this->id);
		return $db->query($sql);
	}

	public function update_online_status($off=false)
	{
		$db = db::getInstance();
		if ( $this->id === false )
		{
			return false;
		}
		$now = $off ? time() - self::SESSION_DURATION - 1 : time();
		$sql = 'UPDATE user
					SET useronline = ' . intval($now) . '
					WHERE userid = ' . intval($this->id);
		$db->query($sql);
		$this->useronline = intval($now);
	}

	public function logged_in()
	{
		return $this->id !== false;
	}

	public function read($id)
	{
		$db = db::getInstance();

		$this->id = false;
		$this->username = '';
		$this->useremail = '';
		//$this->useronline = false;

		$sql = 'SELECT *
					FROM user
					WHERE userid = ' . intval($id);
		$result = $db->query($sql);
		$row = $result->fetch_assoc();
		$result->free();
		if ( $row )
		{
			$this->id = (int) $row['userid'];
			$this->username = (string) $row['username'];
			$this->useremail = (string) $row['useremail'];
			$this->langGame = (string) $row['userlang_game'];
			$this->userlang = (string) $row['userlang'];
			$this->userlvl = (string) $row['userlvl'];
		}

		$sql = 'SELECT * FROM user_niveau WHERE userid = ' . intval($id);
		$result = $db->query($sql);
		$row = $result->fetch_assoc();
		$result->free();
		if ($row) {
			$this->spoken_lang = (string) $row['spoken_lang'];
			//now we go deeper…
			$tmp_langs = explode(';',$row['spoken_lang']);
			$tmp_lvls = explode(';',$row['niveau']);
			$nb_langs = count($tmp_langs);
			if($nb_langs==count($tmp_lvls)){
				for($i=0;$i<$nb_langs;$i++){
					require_once("./sys/load_iso.php");
					$lang_iso = new IsoLang();
					if($tmp_langs[$i]!=""){
						$this->languages[$lang_iso->language_code_for($tmp_langs[$i])]=$tmp_lvls[$i];
					}
				}
			}
			//end add on for level for languages… multiplicity of info beurk
			return true;
		}
		return false;
	}
}

?>

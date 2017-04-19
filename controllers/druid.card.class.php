<?php
//require_once('./sys/db.class.php'); not required is caled by card.class
require_once('./models/card.class.php');
class druid_card
{
	private $submit = false;
	private $mot = '';
	private $tabou1 = '';
	private $tabou2 = '';
	private $tabou3 = '';
	private $tabou4 = '';
	private $tabou5 = '';
	private $tabou6 = '';
	private $nivcarte = '';
	private $userlang = '';
	private $user= '';
	private $createur= '';
	private $theme='';
	private $theme_carte='';
	private $errors = array();

	private $res = '';
	private $card;
	private $res2 = '';

	private $mode = '';

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
       	}
       	return $this->display_et_scores();

        return false;
	}

	private function init()
	{
		// récupération de l'id de l'utilisateur et de sa langue étudiée
		$this->user = user::getInstance();
		$this->userlang = $this->user->langGame;
		$this->createur = $this->user->id;

		// récupération du formulaire de création de carte
		 $this->submit = isset($_POST['submit_form']);
		if ( $this->submit )
		{

		    $this->res['mot'] = isset($_POST['mot']) ? trim($_POST['mot']) : '';
		    $this->res['theme_carte'] = isset($_POST['theme_carte']) ? trim($_POST['theme_carte']) : '';
		    $this->res['nivcarte'] = isset($_POST['nivcarte']) ? trim($_POST['nivcarte']) : '';
		    $this->res['tabou1'] = isset($_POST['tabou1']) ? trim($_POST['tabou1']) : '';
		    $this->res['tabou2'] = isset($_POST['tabou2']) ? trim($_POST['tabou2']) : '';
		    $this->res['tabou3'] = isset($_POST['tabou3']) ? trim($_POST['tabou3']) : '';
		    $this->res['tabou4'] = isset($_POST['tabou4']) ? trim($_POST['tabou4']) : '';
		    $this->res['tabou5'] = isset($_POST['tabou5']) ? trim($_POST['tabou5']) : '';
		    $this->res['tabou6'] = isset($_POST['tabou6']) ? trim($_POST['tabou6']) : '';
		}

		$db = db::getInstance();
		//A theme object would not have been bad… notimenow
		$sql = 'SELECT DISTINCT `themeFR` FROM `themes` ORDER BY `themes`.`themeFR` ASC';
	    $db->query($sql);
		$this->theme_carte = array();
		while($theme = $db->fetch_object()){
			array_push($this->theme_carte, $theme->themeFR);
		}
	    return true;
	}

	private function check()
	{
        if ( !$this->submit )
		{
			return false;
		}

        // Vérification de l'unicité, le mot à trouver ne doit pas être parmi les mots tabous
		if ( $this->res['mot'] == $this->res['tabou1'] || $this->res['mot'] == $this->res['tabou2'] || $this->res['mot'] == $this->res['tabou3'] || $this->res['mot'] == $this->res['tabou4'] || $this->res['mot'] == $this->res['tabou5'] || $this->res['mot'] == $this->res['tabou6']){
				array_push($this->errors, "tabooWords");
				include('./views/druid.card.html');
				exit;
		}
       return true;
    }
	private function validate()
	{
        if ( $this->submit)
        {	//Categorie always "nom" TODO…
    		try{
	    		$forbidden = array();
	    		for($i=1;isset($this->res['tabou'.$i]);$i++){
	    			array_push($forbidden,$this->res['tabou'.$i]);
	    		}
				$carte = new Card($this->userlang,
								  NULL,
								  $this->res['nivcarte'],
								  "nom",
								  $this->createur,
								  $this->res['mot'],
								  $forbidden,
								  array($this->res['theme_carte']),
								  './views/card.inline.display.php');
				$carte->store();
				$_SESSION["LastStoredId"] = $carte->get_id();
				$this->card = $carte ;
				$this->res = array('carteID' => $_SESSION["LastStoredId"]);
			}
			catch(Exception $e){
				echo $e;
				return false;
			}
			return true;
		}else{
			return false;
		}
    }

	private function display_et_scores()
	{

        //si une carte a été soumise
       if ($this->submit && !isset($_SESSION["CreateCard"]))
        {
			require_once('./sys/load_iso.php');
			$lang_iso = new IsoLang();
			require_once('./controllers/update_score_coeff.php');

			//Requête de modification du score du Druide l'accomplissement de son fastidieux travail de création de carte
			$sh = new ScoreHandler($this->createur, $this->userlang);
			$sh->update_scores();
			$_SESSION["CreateCard"]=true;

			//affichage de l'aperçu de la carte avec son identifiant
			include('./views/druid.card.display.html');
		}else{
			if(isset($_SESSION["CreateCard"])){
				header('Location: index.php?page.home.html');
			}
			// sinon, pas encore de carte soumise donc affichage du formulaire de création de carte.
			include('./views/druid.card.html');
		}
        return true;
	}
}

?>

<?php
require_once('./sys/config.php');
require_once('./sys/db.class.php');
$fileName = '';
$ext = ".ogg";
$db=db::getInstance();

foreach(array('audio') as $type) {
	if (isset($_FILES["audio-blob"])) {
		echo 'enregistrements/';
		$fileName = $_POST["audio-filename"];
		$_SESSION['filename']=$fileName;
		$uploadDirectory = 'enregistrements/'.$fileName;
		if (!move_uploaded_file($_FILES["audio-blob"]["tmp_name"], $uploadDirectory)) {
			throw new Exception('problem moving uploaded file');
		}
	}
	

	//echo ($fileName);
	$temps= date("d/m/Y H:i");		
	
	// récupère dans un tableau de hachage le nom du fichier sans l'extension, l'extension et le chemin
	$file = pathinfo('./enregistrements/'.$fileName.''); 
	
			
	// convertit en mp3
			if($conversion!==false){
				//#format
				if($conversion=="mp3"){
					$conversion = "avconv -i %source% -acodec libmp3lame -q:a 2 -ac 1 %target%";
				}
				$commande = audio_convert($conversion, $file['filename']);
				exec($commande); 
				// Supression du fichier.wav du serveur.	
				exec("rm ./enregistrements/".$file['filename'].".ogg"); 
				$ext = ".mp3";	
				echo $file['filename'].$ext;
				echo $commande;

			}
			else{
				echo $file['filename'].$ext;
			}
	
	

	
	// ajout 15/02
	//$_SESSION['userid']=$userid;
	
	//enregistrement dans la BD de la partie de l'oracle
	if($fileName!='')
	//$userid = $_POST["filename"];
	//$_SESSION['userid']=$userid;
	{
		//renomme le nom du fichier à rentrer dans la BD
		
		$fileName=$file['filename'].$ext;
		$sql = 'INSERT INTO enregistrement
                (cheminEnregistrement,idOracle,OracleLang,tpsEnregistrement,carteID,nivcarte) 
				 VALUES('.$db->escape($fileName).','.
					$_GET["userid"]. ','.
					$_GET["userlang"]. ','.$db->escape($temps).','.
					$_GET["cardid"]. ','.
					$_GET["levelcard"].')';
		$db->query($sql);
	}
}

function audio_convert($file, $filename){
	$commandeConv = str_replace(array("%source%", "%target%"), array("./enregistrements/".$filename.".ogg","./enregistrements/".$filename.".mp3"), $file); 
	return $commandeConv;
}

?>

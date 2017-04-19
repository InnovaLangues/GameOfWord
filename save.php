<?php
require_once('./sys/config.php');
require_once('./sys/db.class.php');
$fileName = '';
$ext = ".ogg";
$db=db::getInstance();

foreach(array('audio') as $type) {
	if (isset($_FILES["audio-blob"])) {
		$fileName = $_POST["audio-filename"];
		$_SESSION['filename']=$fileName;
		$uploadDirectory = './enregistrements/'.$fileName;
		if (!move_uploaded_file($_FILES["audio-blob"]["tmp_name"], $uploadDirectory)) {
			throw new Exception('problem moving uploaded file');
		}
	}

	// récupère dans un tableau de hachage le nom du fichier sans l'extension, l'extension et le chemin
	$file = pathinfo('./enregistrements/'.$fileName);


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
		$fileName=$file['filename'].$ext;
		//renomme le nom du fichier à rentrer dans la BD
		$duration = 1;//durée minimale…
		if($ext == ".mp3"){
			//would better use ffmpeg, but if we do that, might as well replace avconv
			//see http://stackoverflow.com/questions/12053125/php-function-to-get-mp3-duration#answer-15237692
			//short term solution
			require_once("./sys/mp3.utils.php");
			$mp3_file = new MP3File("./enregistrements/$fileName");
			$duration = $mp3_file->getDuration();
		}
		$sql = 'INSERT INTO enregistrement
                (`cheminEnregistrement`,`idOracle`,`OracleLang`,`carteID`,`nivcarte`,`duration`,`nivpartie`)
				 VALUES('.$db->escape($fileName).','.
					$_GET["userid"]. ','.
					$_GET["gamelang"]. ','.
					$_GET["cardid"]. ','.
					$_GET["levelcard"].','.
					$duration.','.
					$_GET['gamelevel'].')';
		$db->query($sql);
	}
}

function audio_convert($file, $filename){
	$commandeConv = str_replace(array("%source%", "%target%"), array("./enregistrements/".$filename.".ogg","./enregistrements/".$filename.".mp3"), $file);
	return $commandeConv;
}

?>

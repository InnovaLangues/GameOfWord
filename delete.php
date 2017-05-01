<?php
require('./sys/config.php');
require_once("./controllers/score.handler.class.php");
$ext = ".mp3";

//script pour supprimer le fichier sur le serveur
if (isset($_POST['delete-file'])) {
	$fileName = 'enregistrements/'.$_POST['delete-file'];
	if($conversion!==false){
		//#format
		if(!unlink($fileName.'.mp3')) {
			echo $fileName;
			echo(' problem deleting files.');
		}
		else {
			$full_name = $_POST['delete-file'].".mp3";
			echo(' both wav/webm files deleted successfully.');
		}
	}
	else{
		$ext = ".ogg";
		if(!unlink($fileName.'.ogg')) {
			echo $fileName;
			echo(' problem deleting files.');
		}
		else {
			$full_name = $_POST['delete-file'].".ogg";
			echo(' both wav/webm files deleted successfully.');
		}
	}
	$sh = new ScoreHandler2();
	$sh->abort_record($full_name);
}

?>

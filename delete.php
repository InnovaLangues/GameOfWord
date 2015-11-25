<?php
require('./sys/config.php');
require_once('./sys/db.class.php');

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
				echo(' both wav/webm files deleted successfully.');
			}		
		}

		
		
		//connexion à la BD et suppression de la ligne de l'enregistrement
		$db=db::getInstance();
		$sql = 'DELETE FROM enregistrement
				WHERE (cheminEnregistrement=\''.$_POST['delete-file'].$ext.'\')';
		$db->query($sql);
				
		//suppression du fichier dans la serveur
		

	}
	
?>

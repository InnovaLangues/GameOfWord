<?php
class Notification
{
	private $user = '';
	public $messNotif = array();
	private $db;
	private $time = "";

	function __construct()
	{
		//Récupération des informaions de base: userid
		$this->user = user::getInstance();
		//connexion à la BD
		$this->db = db::getInstance();

		$this->time = date('Y-m-d H:i:s');
	}

	function readNotif()
	{
		//récupération des messages de notification;
		$sql = 'SELECT * FROM `notif` WHERE `userid`="'.$this->user->id.'" ORDER BY `time` DESC';
		$result=$this->db->query($sql);
        // comptage du nombre de résultats
        $nb_result=$result->num_rows;

        //pour chaque enregistrement:
        if ($nb_result > 0)
        { 		while($res = mysqli_fetch_assoc($result)){

        			if($res['emetteur'] != 0){
						$sql = "SELECT `photo` FROM `user` WHERE `userid`=".$res['emetteur'];
						$resultat=$this->db->query($sql);
						$res2 = mysqli_fetch_assoc($resultat);
						$emetteur = $res2["photo"];
					}
					else{
						$emetteur = $res["game"];
					}

					$this->messNotif[$res['id']][$res['state']][$emetteur][$res["time"]] = $res['message'];
				}
		}
	}

	function addNotif($userid,$notification,$emetteur){
		$sql = "INSERT INTO `notif`(`userid`, `message`, `emetteur` , `time`) VALUES ($userid,".$this->db->escape($notification).",$emetteur,'".$this->time."')";
		$result=$this->db->query($sql);
	}

	function addNotifGAME($userid,$notification,$role){
		//add an image to your notification…
		$sql = "INSERT INTO `notif` (`userid`, `message`, `emetteur` ,`game` , `time`) VALUES ($userid,".$this->db->escape($notification).",0,'".$role."','".$this->time."')";
		$result=$this->db->query($sql);
	}


	function notifRead($id) {
		$sql= 'UPDATE `notif` SET `state`=1 WHERE id= $id';
		$result=$this->db->query($sql);
	}

}

?>

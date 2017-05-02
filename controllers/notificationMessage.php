<?php
class Notification
{
	private $user = '';
	public $messNotif = array();
	private $db=false;
	private $time = "";

	function __construct($perform_queries=true,$db=false)
	{
		//Récupération des informaions de base: userid
		$this->user = user::getInstance();
		//connexion à la BD
		if(!$db){
			$this->db = db::getInstance();
		}else{
			$this->db = $db;
		}
		$this->perform_queries = $perform_queries;
	}

	function is_query_performer(){
		return $this->perform_queries;
	}

	function readNotif(){
		if($this->is_query_performer()){
			//récupération des messages de notification;
			$sql = 'SELECT * FROM `notif` WHERE `userid`="'.$this->user->id.'" ORDER BY `time` DESC';
			$result=$this->db->query($sql);
			// comptage du nombre de résultats
			$nb_result=$result->num_rows;

			//pour chaque enregistrement:
			if ($nb_result > 0){
				while($res = mysqli_fetch_assoc($result)){
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
	}

	function addNotif($userid,$notification,$emetteur,$type="NULL"){
		$sql = "INSERT INTO `notif`(`userid`, `type`, `message`, `emetteur`) VALUES ($userid,$type,".$this->db->escape($notification).",'$emetteur');";
		if($this->is_query_performer()){
			$result=$this->db->query($sql);
		}
		else{
			$result=$sql;
		}
		return $result;
	}

	function addNotifGAME($userid,$notification,$role,$type="NULL"){
		//add an image to your notification…
		$sql = "INSERT INTO `notif` (`userid`, `type`, `message`, `emetteur` ,`game`) VALUES ($userid,$type,".$this->db->escape($notification).",0,'".$role."');";
		if($this->is_query_performer()){
			$result=$this->db->query($sql);
		}
		else{
			$result=$sql;
		}
		return $result;
	}


	function notifRead($id) {
		$sql= 'UPDATE `notif` SET `state`=1 WHERE id= $id;';
		if($this->is_query_performer()){
			$result=$this->db->query($sql);
		}
		else{
			$result=$sql;
		}
		return $result;
	}

	function cancelNotif($id=false) {
		if($id===false){
			$id = "@notif_id";
		}
		$sql= 'DELETE FROM `notif` WHERE `notif`.`id` = $id;';
		if($this->is_query_performer()){
			$result=$this->db->query($sql);
		}
		else{
			$result=$sql;
		}
		return $result;
	}

	function cancelLastNotifOfType($user_id,$type){
		$sql= "DELETE FROM `notif` WHERE `notif`.`userid`='$user_id' AND `notif`.`type` = '$type' ORDER BY `notif`.`time` DESC LIMIT 1;";
		if($this->is_query_performer()){
			$result=$this->db->query($sql);
		}
		else{
			$result=$sql;
		}
		return $result;
	}
}

?>

<?php
require_once("./sys/db.config.php"); //dirty hack to correct some dirtiness…
/*Il faut enlever les commantaires à cette variable si sur le serveur 
  une version de libav-tools >= 9 est installée*/
//$conversion = "avconv -i %source% -acodec libmp3lame -q:a 2 -ac 1 %target%";
?>

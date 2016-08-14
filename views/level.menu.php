<script type="text/javascript">
	//make sure changeParameters is loaded
	if(typeof changeParameters != "function"){
		var script= document.createElement('script');
		script.type= 'text/javascript';
		script.src= './controllersJS/menu_lang.js';
		document.getElementsByTagName('head')[0].appendChild(script);
		/* peut nécessiter un setTimeout(function(){alert('loaded ' + typeof changeParameters);}, 10);*/
	}
</script>
<?php
	function print_level_choice($role,$lang,$user){
		echo $lang['level'];
 		if(!isset($user)){
			throw new Exception("Pas d'utilisateur", 1);
		}
		echo "<select name=\"chooseLevel\" size=\"1\" id=\"chooseLevel\" onchange=\"changeParameters('$role','level')\">";
		$levels = array("easy", "medium", "hard");
		foreach ($levels as $level) {
			echo "<option value=" . $level ;
				if (strcmp($user->userlvl,$level) == 0) { echo " selected"; }
				echo ">" . $lang['level_'.$level] . "</option>";
		}
		echo "</select>\n".
			  '<span class="info" id="info">'.$lang[$role."_".$user->userlvl].'</span>';
	}
?>

<?php
	function get_messages($llang){
		include 'languages/lang.' . $llang . '.php';
		include 'languages/errors.php';
		include 'languages/notifs_codes.php';
		return $lang;
	}
?>

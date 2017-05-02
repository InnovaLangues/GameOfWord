<?php
	if(!isset($user)){
		$user = user::getInstance();
	}
	$userlang = $user->get_lang();

	if ( $userlang ){
		include 'languages/lang.' . $userlang . '.php';
	}
	include 'languages/errors.php';
	include 'languages/notifs_codes.php';
?>

<?php
/*
------------------
Language: English
------------------
*/

$lang = array();

///Utilisateurs : register.class.php, login.class.php, edit.form.html, login.form.html, register.form.html
$lang['user_username_blank'] = 'You must enter a user name';
$lang['user_useremail_blank'] ='You must enter an email address';
$lang['user_password_blank'] = 'You must enter a password';
$lang['user_passwordconfirm_blank'] = 'Please confirm your password';
$lang['user_email_notvalid'] = 'Your email is not valid';
$lang['user_passwordconfirm_false'] = 'Your passwords do not match';
$lang['user_exists_already'] = 'This username already exists';
$lang['user_pass_user_nonmatch'] = 'The user name or the password is not a match';
$lang['user_username'] ='User name';
$lang['users_email'] = 'Email';
$lang['users_passwd'] = 'Password';
$lang['users_confirm_passwd'] = 'Re-enter password';
$lang['password_confirm'] = 'confirm your password';
$lang['photoProfil'] = 'Profile\'s photo';
$lang['user_userlang'] ='User language';
$lang['user_userlang_interface']='Interface language';
$lang['login'] = 'Log in';
$lang['logout'] = 'Log out';
$lang['user_userlang_maternelle']='native language';
$lang['langue_apprentissage']='Choose a/the languages that you are studying:';
$lang['ajout_langue']='Add a studied language';
$lang['retirer_langue'] = "Delete the last language in the list";
$lang['choose_lvl'] = "Choose default game level";


//Menu et boutons de formulaires
$lang['cmd_submit'] = 'Submit';
$lang['cmd_cancel'] = 'Cancel';
$lang['register'] = 'Register';
$lang['rules'] = 'Rules and information';
$lang['about'] = 'About';
$lang['niouzes'] = 'News and disclaimer';

//Page d'accueil: menu, home, page.header.html
$lang['menu_profile'] = 'Profile';
$lang['menu_logout'] = 'Logout';
$lang['homepage'] = 'Home';
$lang['home_welcome'] = 'Welcome';

$lang['oracle'] = 'Oracle';
$lang['druide'] = 'Druid';
$lang['devin'] = 'Augur';
$lang['learning'] = 'You\'re playing in ';
$lang['userName'] = 'Player';

//Sélection des modes de jeux
$lang['select_mode'] = 'Select a mode';
$lang['select_role']='Select a role';
$lang['card_create'] = 'Card creation';
$lang['card_description']='Card description';
$lang['game_arbitrage']='Referee a game';

// Description d'une carte par un Oracle oracle.card.display.html
$lang['warning'] = 'Warning';
$lang['cut_sound'] = 'You have not authorised your microphone. Therefore you will return back to the principal menu of the game.';
$lang['start_describe'] = 'Start your description:';
$lang['record'] = 'Play';
$lang['send_description'] = 'Send your description !';
$lang['erase'] = 'Erase and restart';
//$lang['giveUp']=' You did not send your record, Therefore you will loose 10 points as Oracle but you will get 5 points as Druide';
//$lang['giveUpWithoutPoints'] = 'You did not send your record. But you have not enought points to loose. So you don\'t loose points for this time... Be carefull the next time ;)';

$lang['pointsOracle'] = ' The record  has been sent. you will gain or lose points according the Druid/Oracle points.';
$lang['giveUpOracle'] = ' The record has not been sent to the server.';


// Affichage des cartes en création et lecture : Oracle | Druide | Devin
$lang['taboo_1'] = 'Word Taboo 1';
$lang['taboo_2'] = 'Word Taboo 2';
$lang['taboo_3'] = 'Word Taboo 3';
$lang['taboo_4'] = 'Word Taboo 4';
$lang['taboo_5'] = 'Word Taboo 5';
$lang['taboo_6'] = 'Word Taboo 6';
$lang['taboo']=' Taboo';
$lang['theme']='Subject';
$lang['word_to_find'] = 'Word to Find';
$lang["wordForbid"] = 'Forbidden words';
$lang['word_direction'] = 'CEFRL level associated to the word (not taking the forbidden words into account):';
$lang['level_easy'] = 'Easy';
$lang['level_medium'] = 'Medium';
$lang['level_hard'] = 'Hard';
$lang['level_beg'] = "Beginner";
$lang['level_int'] = "Intermediate";
$lang['level_adv'] = "Advanced";
$lang['level_nat'] = "Native";
$lang['validate'] = 'Validate';
$lang['reset'] = 'Reset';
$lang['random'] = 'Randomize';

$lang['card_creation'] = 'Card Creation';
$lang['more_taboo'] = 'More taboo';
$lang['less_taboo'] = 'Less taboo';
$lang['enter_id'] ='Entrer an Id';

// Menu Oracle
$lang['card_alea'] = 'Random Card (Create)';
$lang['card_exist'] = 'Random Card (Exist)';
$lang['card_by_id'] ='Card by it\'s Identification';
$lang['card_lexinno']='Linked to your lexicon';

// Description d'une carte : Oracle
$lang['card_descr'] = 'Card Description';
$lang['description'] = 'You let the others guess your description of the<span class="motatrouver"> first word</span> without using <span class="motTaboo">the other words</span>';
$lang['beware_time']=' You can restart your recording as much as you want but beware, you are limited in time. If you don\'t send your recording, you loose the points from Oracle, but you get some points from Druide.';
$lang['card_preview'] = 'Card Preview';
$lang['id_describe'] = 'Here is your Card ID, send it to your friend so they can play with it ! ';
$lang['unknown_id'] = 'Unreachable card: either the card does not exist in this language, or your are it\'s creator and therefor can\'t play with.';
$lang['abandonner']='Give up';


//Arbitrage d'une description : Druide
$lang['arbitrage'] = 'Description Arbitration';
$lang['listen'] = 'Listen carefully to the Oracle description. Did he used taboo words?';
$lang['invalidate'] = 'Burn at the stake!';

//Ecoute d'une description : Devin
$lang['listen_diviner'] = 'Listen carefully to the Oracle description.';
$lang['id_card'] = 'You are guessing the card:';
$lang['card_creator'] = 'This card was created by:';
$lang['card_oracle'] = 'You are listening to a description by';
$lang['card_level'] = 'Level of the card: ';
$lang['guess'] = 'Guess !';
$lang['which_word'] = 'What is the word described ?';
$lang["RecordCard"] ='If you want to record yourself click here: ';
$lang["RecordArbitre"] ='If you think this recording should have been declared invalid, take matters into your own hands: ';
$lang["restart"] ='Play again ? ';
$lang["start"] ='It\'s time to play the game !';
$lang['start_playback']="Play recording";
$lang['give_up'] = 'Give up';
$lang['devin-nope'] = 'Not the word…';
$lang["game"] ='Start the game';


//Résultats, Scores et Points
$lang['well_done'] = 'Congratulation!';
$lang['too_bad'] = 'Too bad !';
$lang['no_point'] = 'You haven\'t got points';
$lang['result'] = 'Score';
$lang['return']= 'Return';
$lang['score_role'] = 'Regarding the roles you played';
$lang['scores'] = 'Scores';
$lang['nbLangues'] = 'Languages count';
$lang['listLangues'] = 'Languages played';
$lang['classement'] = 'Rankings';
$lang['score_oracle']= 'Oracle Score';
$lang['score_druid']= 'Druid Score';
$lang['score_diviner']= 'Augur Score';
$lang['score_global']= 'Global score';
$lang['global_score'] = 'Global rankings';
$lang['scores_by_language'] = "Rankings by language";

// Timeout
$lang['diviner_timeout'] = 'You did not give an anwser in time';
$lang['oracle_timeout'] = 'You did not make a description in time';
$lang['oracle_card_timeout'] = 'You did not answer the card in time';


//Triche
$lang['sanction']='We are sory but it seems that you leave suddenly the plateforme! So, you loose 5 points!';
$lang['sanction_without_points']='We are sory but it seems that you leave suddenly the plateforme! You are lucky this time, you don\'t have points to loose! But be careful the next time...';

//Pas de partie jouable
$lang['NoGame']='You have already played all the cards.';
//
$lang['add_btn']='Add a taboo word';
$lang['remv_btn']='Remove a taboo word';
$lang['subj']='Select your subject or create a new one';

//Erreurs
$lang['unavailable_card'] = 'Access card denied. The card does\'nt exist';
$lang['without_card']= 'Sorry,  There is no card to play, Would you create one.';
$lang['no_card']= ' Access card denied: maybe the card does\'nt exist in this language or  you are the creator of the card and  so, you can not play this card.';
$lang['no_card_active']= ' Card production is not available  for the other languages; is just available for french.';
$lang['user_name']= 'Please enter a user name';
$lang['email']= 'Please enter an email address';
$lang['password']= 'Please enter a password';
$lang['choose_lang']= 'Choose a language';
$lang['invalid_email']= 'Invalid email!';
$lang['invalid_password']= 'The passwords do not match';
$lang['username_exist']= 'This username already exists';
$lang['enter_username']= 'You must enter a user name';
$lang['enter_password']= 'You must enter a password';
$lang['not_match']= 'The user name or the password are not match.';
$lang['enter_email']= 'You must enter an email';
$lang['enter_language']= 'You must choose a language';
$lang['enter_nativelang']='Please enter your native language';
$lang['tabooWords']= 'The word to find in the card must be diffrent from the taboo words';
$lang['noCardBD']= ' There is no card to play in the Data base';
$lang['noEnregistrement']= "There are no recordings to verify in this language, try another one or enjoy yourself and play another role…";
$lang['Becareful']= 'Attention!';
$lang['Word2find'] = "The word to find is:";
$lang['home_miss_lang_game'] = 'Please select a game language in your profile.';

$lang["languePlay"] = "Language: ";
$lang['level'] = 'Game level: ';
$lang['levelChange'] = 'Level of next game:';
$lang['Oracle_easy'] = "Only one forbidden word and 1′30″ recording time (stake: 10 points).";
$lang['Oracle_medium'] = "Three forbidden words and 1′ recording time (stake: 20 points).";
$lang['Oracle_hard'] = "Six forbidden words and 30″ recording time (stake: 30 points).";
$lang['Devin_easy'] = "Twice the duration of the recording, for a 10 points stake.";
$lang['Devin_medium'] = "1.5 times the duration of the recording, for a 20 points stake.";
$lang['Devin_hard'] = "Recording duration + 8″, for a 30 points stake.";
$lang['Card_created'] = "Thank you for creating a card: ";
$lang['Rec_verified'] = "Thank you for verifying this recording.";
$lang['Oracle_verif'][true] = " verified your recording and thinks you didn't use forbidden words";
$lang['Oracle_verif'][false] = " verified your recording and thinks you used forbidden words";
$lang['Oracle_started'] = "Oracle game started, if you give up the recording you'll lose ";
$lang['Oracle_posted'] = "You took the risk of posting your description, you, brave Oracle!";
$lang['Oracle_abort'] = "Recording deleted. Small penalty, but nothing like the one you would have got with forbidden words…";
$lang['Oracle_devin'][false] = " listened to your recording and didn't find the word…";
$lang['Oracle_devin'][true] = " listened to your recording and found the word!";
$lang['Devin_played'] = "You listened to a recording by ";
$lang['Devin_oracle'][true] = "And you found the word!";
$lang['Devin_oracle'][false] = "And you did not find the word…";

$lang['druidNotReady'] = "All the required variables for a Druid decision have not been set…";
$lang['page_errors'] = "Error in processing query";

//Erreur upload
$lang['file_unupload'] = 'Attention, the file was not fully uploaded.';
$lang['sizeOfUp'] = 'The uploaded file is too large.';
$lang['extUp'] = 'The uploaded file format is not supported (accepted formats are: png,gif,jpg,jpeg).';
$lang['uploadProb'] = 'The file has not been uploaded yet...';

//
$lang['same_lang'] = ' You have chosen twice the same language in langues parlées';
$lang['spok_lang'] = 'Spoken languages';

$lang['language']="Language";
$lang['proficiency'] = "Proficiency";
$lang['game_lang'] = "Game language";

//traces
$lang["AJAX_query_fail"] = "Could not properly perform action: ";
$lang["AJAX_fail"] = "A call to the database was supposed to happen, it did not. Chances are you're playing for nothing. Sorry…";
$lang["AJAX_noquery"] = "No query, actually…";


//Administration cartes
$lang['admin'] = "Administration: Press cross to delete card";
?>

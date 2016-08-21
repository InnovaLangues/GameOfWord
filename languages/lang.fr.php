<?php
/*
------------------
Language: Français
------------------
*/

$lang = array();

//Utilisateurs : register.class.php, login.class.php, edit.form.html, login.form.html, register.form.html
$lang['user_username_blank'] = 'Vous devez entrer un pseudo.';
$lang['user_useremail_blank'] = 'Vous devez entrer une adresse e-mail.';
$lang['user_password_blank'] = 'Vous devez entrer un mot de passe.';
$lang['user_passwordconfirm_blank'] = 'Veuillez confirmer votre mot de passe';
$lang['user_email_notvalid'] = 'Votre adresse e-mail n\'est pas valide.';
$lang['user_passwordconfirm_false'] = 'Vos mots de passe ne sont pas identiques.';
$lang['user_exists_already'] = 'Ce pseudo existe déjà.';
$lang['user_pass_user_nonmatch'] = 'Le pseudo et le mot de passe ne correspondent pas';
$lang['user_username'] = 'Pseudo';
$lang['users_email'] = 'E-mail';
$lang['users_passwd'] = 'Mot de passe';
$lang['users_confirm_passwd'] = 'Entrez à nouveau votre mot de passe';
$lang['password_confirm'] = 'confirmez votre mot de passe';
$lang['photoProfil'] = 'Photo de profil';
$lang['user_userlang'] = 'Langue de l\'utilisateur';
$lang['user_userlang_interface'] = 'Langue de l\'interface';
$lang['login'] = 'Connexion';
$lang['logout'] = 'Déconnexion';
$lang['langue_apprentissage'] = 'Indiquez la/les langue(s) que vous apprenez:';
$lang['ajout_langue'] = 'ajouter une langue parlée';
//Menu et boutons de formulaires
$lang['cmd_submit'] = 'Valider';
$lang['cmd_cancel'] = 'Annuler';
$lang['register'] = 'Enregistrer';
$lang['rules'] = 'Règles';
$lang['about'] = 'A propos';

//Page d'accueil: menu, home, page.header.html
$lang['menu_profile'] = 'Profil';
$lang['menu_logout'] = 'Déconnexion';
$lang['homepage'] = 'Accueil';
$lang['rules'] = 'Règles du Jeu';
$lang['home_welcome'] = 'Bienvenue';

$lang['oracle'] = 'Oracle';
$lang['druide'] = 'Druide';
$lang['devin'] = 'Devin';
$lang['learning'] = 'Vous jouez en ';

//Sélection des modes de jeux
$lang['select_mode'] = 'Selectionez un mode';
$lang['select_role'] = 'Selectionez un rôle';
$lang['card_create'] = 'Création d\'une carte';
$lang['card_description'] = 'Description d\'une carte';
$lang['game_arbitrage'] = 'Arbitrage d\'une partie';
$lang['card_lexinno']='Carte liée à votre lexique';

// Description d'une carte par un Oracle oracle.card.display.html
$lang['warning'] = 'Attention, votre micro n\'a pas été activé.';
$lang['cut_sound'] = 'vous serez par conséquant redirigé vers le menu principal. Veuillez paramétrer votre micro';
$lang['start_describe'] = 'Commence ta description&nbsp;';
$lang['record'] = 'Jouer';
$lang['send_description'] = 'Envoie ta description !';
$lang['erase'] = 'Efface et recommence';
$lang['pointsOracle'] = 'L\'enregistrement a bien été déposé sur le serveur. Vous serez gratifié ou sanctionné de points en fonction des résultats obtenus par les druides et devins.';
$lang['giveUpOracle'] = 'L\'enregistrement n\'a pas été déposé sur le serveur.';

// Affichage des cartes en création et lecture : Oracle | Druide | Devin
$lang['taboo_1'] = 'Mot Tabou 1';
$lang['taboo_2'] = 'Mot Tabou 2';
$lang['taboo_3'] = 'Mot Tabou 3';
$lang['taboo_4'] = 'Mot Tabou 4';
$lang['taboo_5'] = 'Mot Tabou 5';
$lang['taboo_6'] = 'Mot Tabou 6';
$lang['taboo'] = 'Tabou';
$lang['theme'] = 'Thème';
$lang['word_to_find'] = 'Mot à trouver';
$lang['wordForbid'] = 'Mots interdits';
$lang['word_direction'] = 'Niveau du CECR de ce mot (indépendamment des mots interdits)&nbsp;:';
$lang['level_easy'] = 'Facile';
$lang['level_medium'] = 'Moyen';
$lang['level_hard'] = 'Difficile';
$lang['validate'] = 'Valider';
$lang['reset'] = 'Réinitialiser';
$lang['random'] = 'Aléatoire';

$lang['card_creation'] = 'Création de carte';
$lang['more_taboo'] = '+ de mots  tabou';
$lang['less_taboo'] = '- de mots tabou';
$lang['enter_id'] = 'Entrez un identifiant';

// Menu Oracle
$lang['card_alea'] = 'Carte Aléatoire (Création)';
$lang['card_exist'] = 'Carte Aléatoire (Existante)';
$lang['card_by_id'] = 'Carte par son Identifiant';

// Description d'une carte : Oracle
$lang['card_descr'] = 'Description d\'une carte';
$lang['description'] = '<span class="motTaboo">Attention&nbsp;: Il semblerait que ce rôle nécessite l\'utilisation de firefox</span>.<br />Tu dois faire deviner aux autres joueurs  le<span class="motatrouver"> premier mot</span> sans dire les<span class="motTaboo"> autres mots.</span><br/>
Tu recevras 10 points à la fin de ta description. Si elle n\'est pas validée par le Druide, tu les perdras. Si elle est validée mais que le Devin ne trouve pas le mot décrit, tu perdras 5 points.';
$lang['abandonner'] = 'Abandonner';
$lang['beware_time'] = 'Tu peux recommencer ton enregistrement à infini, mais ton temps est limité. Si tu décides de ne pas envoyer tu perdras des points d\'Oracle mais tu gagneras un peu de Druide';
$lang['card_preview'] = 'Aperçu de votre carte';
$lang['id_describe'] = 'Voici l\' identifiant de votre carte (ID), transmettez-le à vos amis pour qu\'ils jouent à votre carte ! ';
$lang['unknown_id'] = 'Carte inaccessible: soit la carte n\'existe pas dans cette langue, soit vous en êtes le créateur et dans ce cas vous ne pouvez pas y jouer.';

//Arbitrage d'une description : Druide
$lang['arbitrage'] = 'Arbitrage d\'une description';
$lang['listen'] = 'Ecoute attentivement la description de l\'oracle. A-t-il utilisé des mots tabou?';
$lang['invalidate'] = 'Au bûcher!';

//Ecoute d'une description : Devin
$lang['listen_diviner'] = 'Ecoute attentivement la description de l\'oracle.';
$lang['id_card'] = 'Tu devines la carte numéro';
$lang['card_creator'] = 'Cette carte a été créée par';
$lang['card_oracle'] = 'Tu écoutes la description de';
$lang['card_level'] = 'Niveau de la carte décrite';
$lang['guess'] = 'Devine !';
$lang['which_word'] = 'Quel est le mot décrit ?';
$lang['NoGame'] = 'Il n\'y a plus de carte à jouer. Peut-être pourriez vous en ajouter quelques unes <a href=\'index.php?mode=druid.card\'>ici</a>…';
$lang['RecordCard'] = 'Si vous voulez proposer un enregistrement pour cette carte cliquez ici&nbsp; ';
$lang['RecordArbitre'] = 'Si vous voulez arbitrer cet enregistrement cliquez ici : ';
$lang['restart'] = 'Rejouer une partie ? ';
$lang['start'] = 'Il est temps de jouer !';
$lang['start_playback']="Commencer l'écoute";
$lang['give_up'] = 'Abandonner';
$lang['devin-nope'] = 'Pas ce mot…';
$lang['game'] = 'Jouer';

//Résultats, Scores et Points
$lang['well_done'] = 'Félicitation!';
$lang['too_bad'] = 'Dommage!';
$lang['no_point'] = 'Tu n\'as pas eu de points';
$lang['result'] = 'Score';
$lang['return'] = 'Retour';
$lang['score_role'] = 'En fonction des rôles que tu as joué.';
$lang['scores'] = 'Scores';
$lang['classement'] = 'Classement';
$lang['nbLangues'] = 'Nombre de langues';
$lang['listLangues'] = 'Langues jouées';
$lang['score_oracle'] = 'Oracle';
$lang['score_druid'] = 'Druide';
$lang['score_diviner'] = 'Devin';
$lang['score_global'] = 'Score global';
$lang['userName'] = 'Joueur';
$lang['global_score'] = 'Classement général';
$lang['scores_by_language'] = "Scores par langue";

// Timeout
$lang['diviner_timeout'] = 'Tu n\'as pas fourni de réponse dans le temps imparti';
$lang['oracle_timeout'] = 'Tu n\'as pas fourni de description dans le temps imparti';
$lang['oracle_card_timeout'] = 'Tu n\'as pas répondu à la carte dans le temps imparti';

//Triche
$lang['sanction'] = 'Il semblerait que vous avez subitement quitté la précédente partie. Par conséquent, vous serez sanctionné de 5 points...';
$lang['sanction_without_points'] = 'Il semblerait que vous ayez subitement quitté la partie précédente. Néamoins vous n\'avez pas de point pour le moment, vous ne serez donc pas sanctionné! Mais gare à vous la prochaine fois ;)';

//Pas de partie jouable
$lang['NoGame'] = 'aucune partie disponible pour l\'instant.';
//
$lang['add_btn'] = 'Ajouter un mot tabou';
$lang['remv_btn'] = 'Retirer un mot tabou';
$lang['subj'] = 'Choisissez votre thème ou créez-en un nouveau';

//Erreurs
$lang['unavailable_card'] = 'Carte inaccessible: la carte n\'existe pas.';
$lang['without_card'] = 'Désolé, il n\'y a pas de carte disponible à  jouer, vous pourriez en <a href=\'index.php?mode=druid.card\'>créer une</a>.';
$lang['no_card'] = 'Carte inaccessible: soit la carte n\'existe pas dans cette langue, soit vous en êtes le créateur et dans ce cas vous ne pouvez pas y jouer.';
$lang['no_card_active'] = 'La génération de carte n\'est pas active pour d\'autres langues que le français pour l\'instant';
$lang['user_name'] = 'Veuillez entrer un pseudo';
$lang['email'] = 'Veuillez entrer une adresse mail';
$lang['password'] = 'Veuillez entrer un mot de passe';
$lang['choose_lang'] = 'Veuillez choisir une langue';
$lang['invalid_email'] = 'L\'adresse mail est invalide';
$lang['invalid_password'] = 'Les password ne sont pas compatibles';
$lang['username_exist'] = 'Cet utilisateur existe déjà';
$lang['enter_username'] = 'Il faut entrer un pseudo';
$lang['enter_password'] = 'Il faut entrer un mot de passe';
$lang['not_match'] = 'Le pseudo ou le mot de passe est incorrect...';
$lang['enter_email'] = 'Il faut entrer une adresse mail';
$lang['enter_language'] = 'Il faut choisir une langue';
$lang['tabooWords'] = 'L\'intitulé de la carte doit être différent des mots taboo';
$lang['noCardBD'] = 'Il n\'y a pas de carte à jouer dans la base de données… Si vous en <a href=\'index.php?mode=druid.card\'>créez</a>, cette situation sera moins fréquente pour tout le monde ;)';
$lang['noEnregistrement'] = "Il n'y a plus d'enregistrement à arbitrer dans cette langue, essayez-en une autre, ou faites-vous plaisir et jouez un autre rôle…";
$lang['Becareful'] = 'ATTENTION!';
$lang['enter_nativelang'] = 'Veuillez entrer votre langue maternelle';
$lang['Word2find'] = 'Le mot à trouver était&nbsp;: ';
$lang['home_miss_lang_game'] = 'Veuillez sélectionner une langue de jeu dans votre profil.';' There is no available record';

$lang['languePlay'] = 'Vous jouez en ';
$lang['level'] = 'au niveau ';
$lang['levelChange'] = 'Niveau de la prochaine partie&nbsp;:';
$lang['Oracle_easy'] = "Un seul mot interdit et 1′30″ d'enregistrement (mise&nbsp;: 10 points).";
$lang['Oracle_medium'] = "Trois mots interdits et 1′ d'enregistrement (mise&nbsp;: 20 points).";
$lang['Oracle_hard'] = "Six mots interdits et 30″ d'enregistrement (mise&nbsp;: 30 points).";
$lang['Devin_easy'] = "2 fois la durée de l'enregistrement pour une mise de 10 points.";
$lang['Devin_medium'] = "1,5 fois la durée de l'enregistrement pour une mise de 20 points.";
$lang['Devin_hard'] = "Enregistrement + 8″ pour une mise de 30 points.";
$lang['Card_created'] = "Merci d'avoir créé une carte&nbsp;: ";
$lang['Rec_verified'] = "Merci d'avoir vérifié l'enregistrement de ";
$lang['Oracle_verif'][true] = " a vérifié votre enregistrement et pense que vous n'avez pas utilisé de mot interdit";
$lang['Oracle_verif'][false] = " a vérifié votre enregistrement et pense que vous n'avez pas utilisé de mot interdit";
$lang['Oracle_devin'][false] = " a écouté votre enregistrement et n'a pas trouvé le mot…";
$lang['Oracle_devin'][true] = " a écouté votre enregistrement et a trouvé&nbsp;!";
$lang['Devin_played'] = "Vous avez écouté un enregistrement de ";
$lang['Devin_oracle'][true] = "Et vous avez trouvé&nbsp;!";
$lang['Devin_oracle'][false] = "Mais vous n'avez pas trouvé…";


$lang['img_augur']  = "./profil/diviner.jpg";
$lang['img_druid']  = "./profil/druide.jpg";
$lang['img_oracle'] = "./profil/oracle.jpg";

//Erreur upload
$lang['file_unupload'] = 'Attention le fichier a mal été uploadé.';
$lang['sizeOfUp'] = 'La taille du fichier uploadé est trop grande.';
$lang['extUp'] = 'Le fichier uploadé n\'est pas dans un format acceptable (png,gif,jpg,jpeg).';
$lang['uploadProb'] = 'Le fichier n\'a pas été uploadé...';

//
$lang['same_lang'] = 'Vous avez choisi deux fois la même langue  dans langues parlées ';
//Administration cartes
$lang['admin'] = "Administration&nbsp;: Cliquer sur la croix pour supprimer la carte";

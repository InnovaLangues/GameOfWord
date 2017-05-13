 System Install
================

Requirements
-------------
* Apache Server with PHP 5.3+
* MySQL
* Tested on firefox (and chrome)
* Javascript activated
* libavtools >= 9

Installation
-------------

* Download from GitHub and unzip
* Create a "./enregistrements" folder (make sure your Apache server has writing rights in this directory)
* Make sure apache also has writing rights in "./profil" directory
* Edit ./sys/config.sample.php, enter identification to your MySQL or MariaDB server and save the file as ./sys/config.php
* If you want the recordings stored in mp3 format on the server, libavtools >=9 must be installed and the $conversion variable initialization adjusted in ./sys/config
* If you want some cards in various languages you can uncomment the "include_once('import.php');" at the end of initializeDB.php
* Run initializeDB.php
* You're good to go

Administration
---------------
./adminCards.php provides very unsafe administration and should not be kept on the server unless you are about to use it. It allows 2 types of actions :
* admin (GET variable admin is set e.g. http://localhost/adminCards.php?admin=true) : see all the cards, to decide which ones should not be submitted to players (default case as well, if no get variable is set).
* export (GET variable export is set e.g. http://localhost/adminCards.php?export=true) : generate code that can be used to import the said card in another system. NB : the authorship in the next instance will be lost and the cards associated to the admin account
* user generated cards only (GET variable ugc is set e.g. http://localhost/adminCards.php?ugc=true) : only export (or admin, or both) the cards that were created by users.

import
-------
import can automatically performed on install.
It imports the cards we provided and cards you might have exported (see administration). The parameter in the store method in all import script checks whether two cards can concern the same words. It is set to true by default, so that the system checks whether the word exists and refuses same word cards. If you want those cards, you should find occurences of “store(true)” and replace them by “store(false)” before running the import script for the 1st time. (“true” might be a word in your cards)

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
* Edit ./sys/config.sample.php, enter identification to your MySQL or MariaDB server and save the file as ./sys/config.php
* If you want the recordings stored in mp3 format on the server, libavtools >=9 must be installed and the $conversion variable initialization adjusted in ./sys/config
* If you want some cards in various languages you can uncomment the "include_once('provided_cards.php');" at the end of initializeDB.php
* Run initializeDB.php
* You're good to go

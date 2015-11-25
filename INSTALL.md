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
* Edit ./sys/db.config.sample.php, enter identification to your MySQL or MariaDB server and save the file as ./sys/db.config.php
* If you want the recordings stored in mp3 format on the server, libavtools >=9 must be installed and the $conversion variable initialization uncommented in ./sys/config
* Run initializeDB.php
* You're good to go

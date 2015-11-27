<?php header('Content-Type: text/html; charset=UTF-8'); ?>
	<head>
		<style>.carte{float:left; padding:3px; background-color:lightgrey; margin-right:3px;display: flex;flex-direction: column;justify-content:space-between;width:250px}
			   .carte div{margin:0;padding-bottom:4px; padding-top:4px; padding-left:1em; padding-right:1em;}
			   .carte>div{padding:0;}
			   .langue{font-weight:bold; background-color:grey;color:white;}
			   .niveauMin{text-align: right; color:white; float:right; background-color:black}
			   .categorie{background-color:white; margin-bottom:3px}
			   .motCle{text-align:center}
			   .motATrouver{font-weight:bold; background-color:#06AD45}
			   .tabous{overflow:auto;height:250px}
			   .themes{overflow:auto;height:30px}
			   .carte + *:not(.carte){clear:both}
			   .auteur{background-color:white; text-align:left;}</style>
	</head>
	<body>
<?php
error_reporting(E_ALL);//error_reporting(0); désactiver
ini_set('display_errors', '1');
require('models/card.class.php');
/*$test = new Card("fr", NULL, "A2", "nom", "1", "slip", Array("Culotte","Caleçon","Sous-vêtements","Maillot de bain","Nu","Cul"), Array("Objets","Vêtement"));
$test->store();
$test2 = new Card($test->get_id());
echo $test.$test2;*/
//$test = new Card("es", NULL, "A1", "nom", "2", "Actor", Array("Película","Cine","Interpretar","Jugar","Teatro","Protagonista"), Array("Profession","Art"));
//$test->store();
//$test2 = new Card(2);
$t1 = array("bla","bli","bli","blu","bla");
echo count($t1)."<br />" ;
$t2 = array_unique($t1);
echo count(array_unique($t1))."<pre>" ;
print_r(array_keys(array_flip($t1)));
echo "</pre>";

echo is_int("3")."=?".("3" === (string)(int) "3")."=?".("3a" === (string)(int) "3a");

?>
	</body>

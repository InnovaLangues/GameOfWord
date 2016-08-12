<?php

require_once 'sys/db.class.php';
require_once './sys/load_iso.php';
$lang_iso = new IsoLang();

$db = db::getInstance();

$sql = 'SELECT DISTINCT langue FROM `cartes`';
$db->query($sql);

$usedIsos = array();
while ($usedIso = $db->fetch_assoc()) {
    $code = $usedIso['langue'];
    $usedIsos[$code] = $lang_iso->french_for($code);
}

asort($usedIsos);

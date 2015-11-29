<?php

require_once 'sys/db.class.php';
include './sys/load_iso.php';

$db = db::getInstance();

$sql = 'SELECT DISTINCT langue FROM `cartes`';
$db->query($sql);

$usedIsos = array();
while ($usedIso = $db->fetch_assoc()) {
    $language = $usedIso['langue'];
    $usedIsos[$language] = $iso[$language];
}

sort($usedIsos);

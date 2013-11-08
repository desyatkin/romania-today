<?php
require_once __SMDIR__ . '/cParseKeys.php';
require_once __SMDIR__ . '/cConfigSm.php';

cConfigSm::loadLocalConfig();

$aFiles = array(__SMDIR__ . '/template.txt' => __SMDIR__ . '/../data/distr_1.php');

foreach ($aFiles as $sFileIn => $sFileOut) {
    if (file_exists($sFileOut) && filectime($sFileOut)) continue;
    $o = new cParseKeys($sFileIn);
    $o->vProcess();
    $o->vSaveFile($sFileOut);
}

?>
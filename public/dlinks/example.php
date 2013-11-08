<?php
set_time_limit(2);
require_once('slibs/cKeysDb.php');
$sUrl = $_SERVER['REQUEST_URI'] . '?d=fr&d=3&i3&f=' . $_GET['d'];
echo $sUrl;
echo cKeysDb::getBlock($sUrl) . "<br />";
echo cKeysDb::getBlock($sUrl, 2) . "<br />";
echo cKeysDb::getBlock($sUrl, 3) . "<br />";

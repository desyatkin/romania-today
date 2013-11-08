<?
include_once('dlinks/slibs/cKeysDb.php'); 
echo cKeysDb::getBlock($_SERVER['REQUEST_URI'],3);
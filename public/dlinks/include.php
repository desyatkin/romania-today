<?php
require_once('slibs/cKeysDb.php');
// Где $sUrl  урл страницы для которой отображается блок. Например $_SERVER['REQUEST_URI']
// В функции вызова вторым параметром указываем номер шаблона. 
$sUrl = $_SERVER['REQUEST_URI'];
echo cKeysDb::getBlock($sUrl);
?>
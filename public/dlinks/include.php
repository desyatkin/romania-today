<?php
require_once('slibs/cKeysDb.php');
// ��� $sUrl  ��� �������� ��� ������� ������������ ����. �������� $_SERVER['REQUEST_URI']
// � ������� ������ ������ ���������� ��������� ����� �������. 
$sUrl = $_SERVER['REQUEST_URI'];
echo cKeysDb::getBlock($sUrl);
?>
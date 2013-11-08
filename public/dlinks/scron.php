<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

if (!defined('__SMDIR__')) {
    define('__SMDIR__', dirname(__FILE__) . '/slibs');
}

define('LOCK_MAX_RETRIES', 10);
define('LOCK_FILE_GLOBAL', 'flock.lock');
define('LOCK_FILE_TIMEOUT', 50); // 50 минут

if (file_exists(LOCK_FILE_GLOBAL) && ((time() - filemtime(LOCK_FILE_GLOBAL)) < LOCK_FILE_TIMEOUT)) {
    exit;
}
//touch(LOCK_FILE_GLOBAL);
require_once('slibs/parsing.func.php');
cConfigSm::loadGlobalConfig();
cConfigSm::loadLocalConfig();
$bShowImg = (cConfigSm::instance('global_show_img') == 1);

$sKWRDSfileName = __SMDIR__ . '/../data/kwrds.txt';

// обновление файла
cRemoteFile::bActualizeFile($sKWRDSfileName, TRUE);

$sDbFilePrefix = cConfigSm::get('global_db2_prefix', 'data/bd2');
$oLog          = new cFastDbDistributed($sDbFilePrefix, cConfigSm::get('global_db2_files', 10), TRUE);
$sDbKeys       = __SMDIR__ . '/../' . cConfigSm::get('global_keys.db');
$oDbKeys       = new cKeysDbSort($sDbKeys);

$oDbKeys->vLoadDb();
$oLog->vResort();

$oDbKeysNew = new cKeysDb($sDbKeys . '.new2');
$oDbKeysNew->vForceImg($bShowImg);
$iTotalShownNew = 0;
$iTotalFreqNew  = 0;
$iTotalFreqRow  = 0;
if (!file_exists($sKWRDSfileName)) die('do not have keywords file');
$fh = fopen($sKWRDSfileName, 'r') or die('can\'t read file');
while ($line = fgets($fh)) {
    $aRow                                         = preg_split('/;/', $line);
    $aRow[cRemoteFile::REMOTE_FILE_POSITION_FREQ] = (int)$aRow[cRemoteFile::REMOTE_FILE_POSITION_FREQ];
    if (!$bShowImg) {
        unset($aRow[cRemoteFile::REMOTE_FILE_POSITION_IMG]);
    }
    $iStat  = $oDbKeys->iGetStat($aRow[cRemoteFile::REMOTE_FILE_POSITION_KWRD], $aRow[cRemoteFile::REMOTE_FILE_POSITION_URL]);
    $aRow[] = $iStat;

    if ($aRow[cRemoteFile::REMOTE_FILE_POSITION_FREQ] < 5 and empty($iStat)) {
        continue;
    }
    $iTotalShownNew += $iStat;
    $iTotalFreqRow++;
    $iTotalFreqNew += $aRow[cRemoteFile::REMOTE_FILE_POSITION_FREQ];
    $oDbKeysNew->vAddToDb(array(array_values($aRow)));
}
$oDbKeysNew->close();
$oDbKeys->close();

cConfigSm::instance('local_shown', $iTotalShownNew);
cConfigSm::instance('local_rows', $iTotalFreqRow);
cConfigSm::instance('local_totalfreq', $iTotalFreqNew);

force_rename($sDbKeys . '.new2', $sDbKeys);
// Удалим вновь потерянные строки из файла
$oDbKeys = new cKeysDbSort($sDbKeys);
$oDbKeys->vForceImg($bShowImg);
$oDbKeys->vLoadDb();
$oDbKeys->vClearStat();
$oLog->vRemoveMissedRows($oDbKeys);
$oDbKeys->vWriteNewStat();
$oLog->close();
$oDbKeys->close();
cConfigSm::instance('local_show_img', $bShowImg);
cConfigSm::saveLocalInfo();
@unlink(LOCK_FILE_GLOBAL);
?>
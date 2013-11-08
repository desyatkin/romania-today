<?php
if (!defined('__SMDIR__')) {
    define('__SMDIR__', dirname(__FILE__));
}

require_once(__SMDIR__ . '/cRemoteFile.php');
require_once(__SMDIR__ . '/cFastDb.php');
require_once(__SMDIR__ . '/cKeysDb.php');
require_once(__SMDIR__ . '/cConfigSm.php');
require_once(__SMDIR__ . '/parse.php');

interface ciCompare
{
    public function bVerifyDbKeyUrlPair($sKey, $sUrl = NULL);

    public function vAddStat($sKey);

    public function sGetImgUrl($sKey);
}

/**
 * Мы знаем что файлы sFrom и sTo рано или поздно освободятся.
 * Поэтому делаем несколько попыток записать данные.
 *
 * @param $sFrom
 * @param $sTo
 *
 * @throws Exception
 */
function force_rename($sFrom, $sTo)
{
    $status = FALSE;
    $tries  = 10;
    while ($tries-- >= 0 && !$status) {
        usleep(200);
        $status = @rename($sFrom, $sTo);
    }
    if (!$status) {
        throw new Exception("can't rename $sFrom to $sTo");
    }
}

class cKeysDbSort extends cKeysDb implements ciCompare
{
    private $aKeysInfo = array();

    public function __construct($sKeysDb)
    {
        parent::__construct($sKeysDb);
    }

    /**
     * Возвращает информацию о позиции поля в строке
     * @param $sKeyword названия поля
     *
     * @return array('start' => позиция начала поля в строке
     *               'len' => длина строки
     * @throws Exception
     */
    public function aFieldPos($sKeyword)
    {
        $iStart = 0;
        $iLen   = 0;
        /// $this->aFields полностью конфигурируются в родительском классе
        foreach ($this->aFields as $sKey => $iKeyLen) {
            if ($sKey == $sKeyword) {
                $iLen = $iKeyLen;
                break;
            }
            $iStart += $iKeyLen;
        }
        if ($iLen == 0) throw new Exception('Keyword not found');
        return array('start' => $iStart,
                     'len'   => $iLen);
    }

    /**
     * Загружает всю базу в память
     * @throws Exception
     */
    public function vLoadDb()
    {
        $ilRow = 1;
        while ($aRow = $this->aReadFields($ilRow)) {
            $this->aKeysInfo[$aRow['keyword']] = array(
                'row'  => $ilRow,
                'stat' => $aRow['shown'],
                'url'  => $aRow['url'],
                'img'  => isset($aRow['img']) ? $aRow['img'] : '');
            $ilRow++;
        }
    }

    /**
     * Возвращает число показов текущего ключевика
     * @param             $sKey ключевик
     * @param null|string $sUrl null проверка на соответствие новому урлу и старому не нужна, иначе сверяем старый и новый урл в фиде
     *
     * @return int 0 если не найдено число хитов если найдено
     */
    public function iGetStat($sKey, $sUrl = NULL)
    {
        /**
         * Такой keyword есть
         * и (
         *  или не нужна проверка урла $sUrl == null
         *  или урл проверили и он такой же как был)
         */
        if ($this->bVerifyDbKeyUrlPair($sKey, $sUrl)) {
            return $this->aKeysInfo[$sKey]['stat'];
        }
        return 0;
    }

    public function vClearStat()
    {
        foreach ($this->aKeysInfo as $sKey => $aVal) {
            $this->aKeysInfo[$sKey]['stat'] = 0;
        }
    }

    public function vAddStat($sKey)
    {
        // по факту тут должно быть
        $this->aKeysInfo[$sKey]['stat']++;
    }

    /**

     */
    public function vWriteNewStat()
    {
        foreach ($this->aKeysInfo as $aVal) {
            $this->bWriteField($aVal['row'], 'shown', $aVal['stat']);
        }
    }

    /**
     * Возвращает количество ключевиков
     * @return int количество ключевиков
     */
    public function iCountRows()
    {
        return count($this->aKeysInfo);
    }

    /**
     * Проверяет наличие в базе ключевика и урла,
     * необходима для проверки консистентности базы бд2
     * @param string      $sKey ключевик
     * @param string|null $sUrl url ссылки, если параметр не задан только проверка по ключевику
     *
     * @return bool true если есть в базе
     */
    public function bVerifyDbKeyUrlPair($sKey, $sUrl = NULL)
    {
        return (isset($this->aKeysInfo[$sKey])
            && (is_null($sUrl) || $sUrl == $this->aKeysInfo[$sKey]['url']));
    }

    public function sGetImgUrl($sKey)
    {
        return (isset($this->aKeysInfo[$sKey]) ? $this->aKeysInfo[$sKey]['img'] : '');
    }
}

?>
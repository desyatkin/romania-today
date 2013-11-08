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
 * �� ����� ��� ����� sFrom � sTo ���� ��� ������ �����������.
 * ������� ������ ��������� ������� �������� ������.
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
     * ���������� ���������� � ������� ���� � ������
     * @param $sKeyword �������� ����
     *
     * @return array('start' => ������� ������ ���� � ������
     *               'len' => ����� ������
     * @throws Exception
     */
    public function aFieldPos($sKeyword)
    {
        $iStart = 0;
        $iLen   = 0;
        /// $this->aFields ��������� ��������������� � ������������ ������
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
     * ��������� ��� ���� � ������
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
     * ���������� ����� ������� �������� ���������
     * @param             $sKey ��������
     * @param null|string $sUrl null �������� �� ������������ ������ ���� � ������� �� �����, ����� ������� ������ � ����� ��� � ����
     *
     * @return int 0 ���� �� ������� ����� ����� ���� �������
     */
    public function iGetStat($sKey, $sUrl = NULL)
    {
        /**
         * ����� keyword ����
         * � (
         *  ��� �� ����� �������� ���� $sUrl == null
         *  ��� ��� ��������� � �� ����� �� ��� ���)
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
        // �� ����� ��� ������ ����
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
     * ���������� ���������� ����������
     * @return int ���������� ����������
     */
    public function iCountRows()
    {
        return count($this->aKeysInfo);
    }

    /**
     * ��������� ������� � ���� ��������� � ����,
     * ���������� ��� �������� ��������������� ���� ��2
     * @param string      $sKey ��������
     * @param string|null $sUrl url ������, ���� �������� �� ����� ������ �������� �� ���������
     *
     * @return bool true ���� ���� � ����
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
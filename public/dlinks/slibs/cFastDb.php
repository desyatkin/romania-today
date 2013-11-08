<?php
require_once('cConfigSm.php');
abstract class cFastDb
{
    protected $aFields = array();
    protected $iPackSize = 0;
    protected $sMode = 'c+';
    protected $sFilename = '';
    protected $fh;

    /**
     * @param $sFile �������� �����
     */
    public function __construct($sFile)
    {
        $this->sFilename = $sFile;
        // ������� ������
        $iTotalLength = 2;
        foreach ($this->aFields as $iLength) {
            $iTotalLength += $iLength;
        }
        $this->iPackSize = $iTotalLength;
    }

    /**
     * @throws Exception cant open file
     */
    public function open()
    {
        if (is_resource($this->fh)) return;
        if (empty($this->sFilename)) {
            throw new Exception('empty filename');
        }
        if (!file_exists($this->sFilename) && ($this->sMode != 'w')) {
            @touch($this->sFilename);
        }
        $this->fh = @fopen($this->sFilename, $this->sMode);
        if (!is_resource($this->fh)) {
            throw new Exception('cant open file' . $this->sFilename);
        }
    }

    /**
     * ������� ����.
     * � $this->fh null
     */
    public function close()
    {
        if (!is_resource($this->fh)) return;
        fclose($this->fh);
        $this->fh = NULL;
    }

    /**
     * @param $iRow   ����� ������
     * @param $sField �������� ����
     *
     * @throws Exception
     */
    public function vSeekTo($iRow, $sField = '')
    {
        $this->open();
        $iPos = 0;
        $iRow--;
        if ($sField != '') {
            $sTmp  = '';
            $aInfo = array_keys($this->aFields);
            foreach ($aInfo as $sKey) {
                if ($sTmp === $sField) continue;
                $sTmp = $sKey;
                $iPos += $this->aFields[$sKey];
            }
            $iPos -= $this->aFields[$sField];
        }
        $iSeekPos = fseek($this->fh, $iRow * $this->iPackSize + $iPos);
        if ($iSeekPos === -1) {
            throw new Exception('Can\'t seek to row:' . $iRow);
        }
    }

    /**
     * ���������� ������ � ����������� iRow ������
     *
     * @param integer $iRow
     *
     * @return array|boolean
     */
    public function aReadFields($iRow)
    {
        $this->vSeekTo($iRow);
        $sLine = fread($this->fh, $this->iPackSize - 2);
        return $this->aParseLineToArr($sLine);
    }

    /**
     * ������ ������ �� ��, � ������������ � this->aFields
     *
     * @param string $sLine ������ �� ����� ��
     *
     * @return array
     */
    public function aParseLineToArr($sLine)
    {
        if (empty($sLine)) return FALSE;
        $iStart  = 0;
        $aResult = array();
        foreach ($this->aFields as $sName => $iCnt) {
            $aResult[$sName] = substr($sLine, $iStart, $iCnt);
            $iStart += $iCnt;
            switch ($sName) {
                case 'chance':
                case 'shown':
                    $aResult[$sName] = (int)$aResult[$sName];
                    break;
                default:
                    $aResult[$sName] = trim($aResult[$sName]);
            }
        }
        return $aResult;
    }

    /**
     * @param $iRow   ����� ������
     * @param $sField �������� ����
     *
     * @throws Exception - ���������� ���������� ����� �����
     * @return string ������ ������
     */
    public function sReadField($iRow, $sField)
    {
        $this->vSeekTo($iRow, $sField);
        $sRead = fread($this->fh, $this->aFields[$sField]);
        if (strlen($sRead) !== $this->aFields[$sField]) {
            throw new Exception('����� �����' . $iRow);
        }
        return $sRead;
    }

    /**
     * @param $iRow   - ����� ������
     * @param $sField - �������� ���������
     * @param $sValue - �������� ������� ����������
     *
     * @return boolean true ���� ��� ������
     */
    public function bWriteField($iRow, $sField, $sValue)
    {
        $this->vSeekTo($iRow, $sField);
        return (fwrite($this->fh, sprintf('%-' . $this->aFields[$sField] . 's', $sValue)) !== FALSE);
    }

    /**
     * @param $aArr ������ � ������� ������� ���� �������� � ����� �����
     */
    public function vAddToDb($aArr)
    {
        $this->open();
        fseek($this->fh, 0, SEEK_END);
        $this->bSaveToDb($aArr);
    }

    /**
     * @param $aArr
     *
     * @throws Exception
     */
    public function bSaveToDb($aArr)
    {
        $this->open();
        $sFormatStr = $this->sGetFormatStr();
        if (count($aArr[0]) != count($this->aFields)) {
            throw new Exception('blah');
        }
        foreach ($aArr as $aRow) {
            fwrite($this->fh, vsprintf($sFormatStr, array_values($aRow)));
        }
    }

    /**
     * @param $sFieldName ��������� ����
     */
    public function vUnsetField($sFieldName)
    {
        if (isset($this->aFields[$sFieldName])) {
            unset($this->aFields[$sFieldName]);
        }
        $iTotalLength = 2;
        foreach ($this->aFields as $iLength) {
            $iTotalLength += $iLength;
        }
        $this->iPackSize = $iTotalLength;
    }

    /**
     * @return string ������ ������ ������� ����� �������� � ����
     */
    public function sGetFormatStr()
    {
        $sResult = '';
        foreach ($this->aFields as $iCnt) {
            $sResult .= '%-' . $iCnt . '.' . $iCnt . 's';
        }
        $sResult .= "\r\n";
        return $sResult;
    }

    /***
     * ���������
     * @param        $iRow
     * @param string $sCntField
     */
    public function vIncrement($iRow, $sCntField = 'cnt')
    {
        $iCnt = $this->sReadField($iRow, $sCntField);
        $iCnt++;
        $this->bWriteField($iRow, $sCntField, $iCnt);
    }
}

class cFastDbWI extends cFastDb
{
    protected $bShowImg = FALSE;
    protected $aFieldsOrig = array();

    public function __construct($sFile)
    {
        $this->bShowImg    = (cConfigSm::instance('local_show_img') == 1);
        $this->aFieldsOrig = $this->aFields;
        parent::__construct($sFile);
    }

    /**

     */
    public function sGetFormatStrWI($bShowImg)
    {
        $sResult = '';
        $aFields = $this->aFieldsOrig;
        if (!$bShowImg && array_key_exists('img', $aFields)) {
            unset($aFields['img']);
        }
        foreach ($aFields as $iCnt) {
            $sResult .= '%-' . $iCnt . '.' . $iCnt . 's';
        }
        $sResult .= "\r\n";
        return $sResult;
    }

    /**
     * @see cFastDb::vAddRowToDb
     *
     * @param array $a
     */
    public function vAddToDb($a)
    {
        if (!$this->bShowImg && array_key_exists('img', $a[0])) {
            unset($a[0]['img']);
        }
        parent::vAddToDb($a);
    }

    public function open()
    {
        if (!$this->bShowImg) {
            $this->vUnsetField('img');
        }
        parent::open();
    }

    public function sGetFormatStr()
    {
        if (!$this->bShowImg) {
            $this->vUnsetField('img');
        }
        return parent::sGetFormatStr();
    }

    /**
     * @param boolean $bStat ��������� �� ���������� img
     */
    public function vForceImg($bStat)
    {
        $this->bShowImg = $bStat;
    }
}

class cFastDbDistributed extends cFastDbWI
{
    protected $iParts;
    protected $iLastPos = 0;
    protected $aFields = array(
        'url'  => 32,
        'prod' => 64,
        'img'  => 124,
        'key'  => 50,
        'tpl'  => 40,
        'sort' => 7
    );

    protected $sFileOrig = '';
    private $iFileOrderedTo = 0;

    public function __construct($sFileOrig, $iParts = 1)
    {
        $this->iParts    = $iParts;
        $this->sFileOrig = $sFileOrig;
        parent::__construct($sFileOrig);
    }

    protected function sGetFileName($sHash)
    {
        $sResult              = $this->sFileOrig . (($this->iParts == 1) ? '0' : (ord($sHash) % $this->iParts));
        $this->iFileOrderedTo = cConfigSm::instance('local_' . $sResult) ? cConfigSm::instance('local_' . $sResult) : 0;
        if (!file_exists($sResult)) {
            @touch($sResult);
        }
        return $sResult;
    }

    /**
     * @param string $sSearch    ��������� ������
     * @param string $sFieldName ���� � ������� ���� ����������
     *
     * @return bool|int false ���� �� �������, ����� ������ ���������
     * @internal param int $iOrderedTo ������ � ����� �� ������� ��� ������ ��������������
     */
    public function mSearchName($sSearch, $sFieldName = 'url')
    {
        $this->vSetFileName(md5($sSearch));
        $iFoundPos = 0;
        try {
            $iMin      = 0;
            $iMax      = $this->iFileOrderedTo;
            $sSearch   = vsprintf('%-' . $this->aFields[$sFieldName] . 's', $sSearch);
            $iOrderPos = 0;
            $bExit     = ($iMax == 0);
            while ($iFoundPos == 0 && !$bExit) {
                $iOrderPos = (int)round(($iMax - $iMin) / 2) + $iMin;
                $sRowName  = $this->sReadField($iOrderPos, $sFieldName);
                if ($sSearch == $sRowName) {
                    $iFoundPos = $iOrderPos;
                } else {
                    if ($sSearch < $sRowName) {
                        $bExit = ($iMax == $iOrderPos);
                        $iMax  = $iOrderPos;
                    } else {
                        $bExit = ($iMin == $iOrderPos);
                        $iMin  = $iOrderPos;
                    }
                }
            }
            if ($iFoundPos == 0) {
                // �� ����� � ��������������� �����.. ���� � �� ���������������
                $this->iLastPos = $iOrderPos;
                $iOrderedTo     = $this->iFileOrderedTo;
                while ($iFoundPos == 0) {
                    $iOrderedTo++;
                    try {
                        $sRowName = $this->sReadField($iOrderedTo, $sFieldName);
                        if ($sRowName == $sSearch) {
                            $iFoundPos = $iOrderedTo;
                        }
                    } catch (Exception $e) {
                        $iFoundPos = -1;
                    }
                }
            }
        } catch (Exception $e) {
            // ������ ����� �� �����
            $iFoundPos = 0;
        }
        return ($iFoundPos > 0) ? $iFoundPos : FALSE;
    }

    protected function vSetFileName($sHash)
    {
        $this->sFilename = $this->sGetFileName($sHash);
    }

    public function vAddToDb($aArr)
    {
        $this->vSetFileName(md5($aArr['url']));
        parent::vAddToDb(array($aArr));
    }

    public function vAddRowToDb($aArr)
    {
        $aArr['sort'] = $this->iLastPos;
        $this->vSetFileName(md5($aArr['url']));
        parent::vAddToDb(array($aArr));
    }

    /**
     * �������������� ���� ������.
     * @return void
     */
    public function vResort()
    {
        if (!$this->bShowImg && array_key_exists('img', $this->aFields)) {
            unset($this->aFields['img']);
        }
        for ($i = 0; $i < $this->iParts; $i++) {
            $sFile    = $this->sFileOrig . $i;
            $iMaxSort = cConfigSm::instance('local_' . $sFile);
            if (file_exists($sFile)) {
                $iMaxSort = $this->iResortDb($sFile, $iMaxSort);
            } else {
                $iMaxSort = 0;
            }
            cConfigSm::instance('local_' . $sFile, $iMaxSort);
        }
    }

    /**
     * ������� ��������� ���� �� ���������� � ����� ������
     * @param string $sFileName ����
     * @param int    $iMaxSort  ������� �� ������� � ����� ������������� ������
     *
     * @return int ��������� ����� ��������� ������ � �����
     * @throws Exception
     */
    public function iResortDb($sFileName, $iMaxSort)
    {
        if (filesize($sFileName) <= $iMaxSort * $this->iPackSize) {
            return (int)(filesize($sFileName) / $this->iPackSize);
        }
        $fh_read = fopen($sFileName, 'r');
        if (!is_resource($fh_read)) throw new Exception('sort:cant open for read');
        $sFormat       = $this->sGetFormatStr();
        $iUnsortedRows = 0;
        fseek($fh_read, $iMaxSort * $this->iPackSize);
        while (!feof($fh_read)) {
            $aRow = array();
            foreach ($this->aFields as $sFieldName => $iCharCount) {
                $aRow[$sFieldName] = fgets($fh_read, $iCharCount + 1);
                if (!$aRow[$sFieldName]) break;
            }
            fgets($fh_read, 3);
            if (count($aRow) > 1) {
                $iRow                           = (int)$aRow['sort'];
                $aRow['sort']                   = '';
                $aUnsorted[$iRow][$aRow['url']] = vsprintf($sFormat, $aRow);
                $iUnsortedRows++;
            }
        }
        if ($iUnsortedRows == 0) {
            // ��� �� ������������� �����
            fclose($fh_read);
            return $iMaxSort;
        }
        $fh_write = fopen($sFileName . '.sort', 'w');
        if (!is_resource($fh_write)) throw new Exception('sort:cant open for write');
        fseek($fh_read, 0);
        $iRow = 1;
        while ($iRow <= $iMaxSort) {
            $sStr = fgets($fh_read);
            if (isset($aUnsorted[$iRow])) {
                $sUrl                    = substr($sStr, 0, $this->aFields['url']);
                $aUnsorted[$iRow][$sUrl] = $sStr;
                $aKeys                   = array_keys($aUnsorted[$iRow]);
                sort($aKeys);
                foreach ($aKeys as $sKey) {
                    fputs($fh_write, $aUnsorted[$iRow][$sKey]);
                }
            } else {
                fputs($fh_write, $sStr);
            }
            $iRow++;
        }
        if (isset($aUnsorted[0])) {
            $aKeys = array_keys($aUnsorted[0]);
            sort($aKeys);
            foreach ($aKeys as $sKey) {
                fputs($fh_write, $aUnsorted[0][$sKey]);
            }
        }
        fclose($fh_read);
        fclose($fh_write);
        try {
            force_rename($sFileName . '.sort', $sFileName);
        } catch (Exception $e) {
            return $iUnsortedRows;
        }
        return $iUnsortedRows + $iRow - 1;
    }

    /**
     * !!! ����� ��� ��� ��������� ������� ���� ������ ���� ������������
     * @param ciCompare $oCompare
     */
    public
    function vRemoveMissedRows(ciCompare $oCompare)
    {
        $iTotalLines = 0;
        for ($i = 0; $i < $this->iParts; $i++) {
            $sFile = $this->sFileOrig . $i;
            if (file_exists($sFile)) {
                $iLines = $this->iRemoveMissedRowsFile($sFile, $oCompare);
                $iTotalLines += $iLines;
                cConfigSm::instance('local_' . $sFile, $iLines);
            } else {
                cConfigSm::instance('local_' . $sFile, FALSE);
            }
        }
        cConfigSm::instance('local_shown', $iTotalLines);
    }

    /**
     * @param string    $sFileName
     * @param ciCompare $oCompare
     *
     * @return int ���������� ����� � �����
     * @throws Exception
     */
    public
    function iRemoveMissedRowsFile($sFileName, ciCompare $oCompare)
    {
        $fh_read  = fopen($sFileName, 'r');
        $fh_write = fopen($sFileName . '.missed', 'w');
        if (!is_resource($fh_read)) throw new Exception('sort:cant open for read');
        fseek($fh_read, 0);
        $iLines            = 0;
        $bSaveImg          = (cConfigSm::get('global_show_img') == 1);
        $bImgStatusChanged = ($bSaveImg != $this->bShowImg);
        $aFields           = $this->aFieldsOrig;
        $sFormatString     = $this->sGetFormatStrWI($bSaveImg);
        if ($bImgStatusChanged) {
            if (!$bSaveImg) {
                unset($aFields['img']);
            }
        }
        while (!feof($fh_read)) {
            $sStr = fgets($fh_read);
            $aArr = $this->aParseLineToArr($sStr);
            if (!$oCompare->bVerifyDbKeyUrlPair($aArr['key'], $aArr['prod'])) {
                continue;
            }
            $oCompare->vAddStat($aArr['key']);
            $iLines++;
            $sImg = $oCompare->sGetImgUrl($aArr['key']);
            if ($bImgStatusChanged || $aArr['img'] != $sImg) {
                $aArr2 = array();
                /** @var $aFields array */
                foreach ($aFields as $sFieldName => $iLen) {
                    if ($sFieldName == 'img') {
                        $aArr2[] = $sImg;
                    }
                    $aArr2[] = $aArr[$sFieldName];
                }
                /** @var $sFormatString string */
                $sStr = vsprintf($sFormatString, $aArr2);
            }
            fputs($fh_write, $sStr);
        }
        fclose($fh_read);
        fclose($fh_write);
        force_rename($sFileName . '.missed', $sFileName);
        return $iLines;
    }
}

<?php
/**
 * ����� �������� �� ������������� ��������
 */
class cCustomDistribution
{
    /***
     * @var array
     *  array(
     *   '����' => array('hash', 'chance', 'keys'),
     *   '����2' => array('hash', 'chance', 'keys')
     * )
     *  hash ��� ��� ������������, ����� � ������ ���������� ������� ������ �� �������� ���������� �������������
     *  chance ������ ������������ ������, ��� ������ ����� ��� ������ ���� ��������� � ����
     *  keys - ������ � ��� ��� ������ ���� �� ������ "������"
     **/
    private $aDistribution = array();
    private $iAccuracy = 0;
    private $sFilePathTemplate = '/data/distr_%d.php';

    public function __construct($iAccuracy = 100)
    {
        $this->iAccuracy         = $iAccuracy;
        $this->sFilePathTemplate = __SMDIR__ . '/../' . $this->sFilePathTemplate;
    }

    /**
     * ������������� ������������� ������������
     * @param $aDistribution
     */
    public function setDistribution($aDistribution)
    {
        $this->aDistribution = $aDistribution;
    }

    /**
     * @param $iAcc ������������� ��������, � ����� ���� �� ��������� ������ �� �������������, �� ������� ��������� � ���������
     */
    public function setAccuracy($iAcc)
    {
        $this->iAccuracy = $iAcc;
    }

    /**
     * @param $sHash string
     *
     * @return boolean|string
     */
    public function mFindByHash($sHash)
    {
        foreach ($this->aDistribution as $sName => $aInfo) {
            if ($aInfo['hash'] == $sHash) {
                return $sName;
            }
        }
        return FALSE;
    }

    /**
     * �� ���� ���������� ������ �� �������� ����������� ������� ������������� ����������.
     * �� ������� � ��������� ��� � ���� ������ ������� � ���������� � ���������� � �������
     * ���������� ������ � �������������.
     * ������ 1.
     * input array('red', 'green', 'red');
     * output array('red', 'green'), array(66,34)
     * ������ 2.
     * input array('red', 'green', 'red', 'green');
     * output array('red', 'green'), array(50,50)
     *
     * @param array  $aInputArray
     * @param string $sName
     *
     * @return mixed
     */
    public function parseInput($aInputArray, &$sName = 'main')
    {
        $iTotal = count($aInputArray);
        $sHash  = md5(implode('', $aInputArray));
        if (($sOldName = $this->mFindByHash($sHash)) !== FALSE) {
            $sName = $sOldName;
            return;
        }
        $aTemporary = array();
        foreach ($aInputArray as $sKey) {
            $sKey = trim($sKey);
            if (empty($sKey)) continue;
            if (!isset($aTemporary[$sKey])) {
                $aTemporary[$sKey] = 0;
            }
            $aTemporary[$sKey]++;
        }
        $this->aDistribution[$sName] = array('keys'   => array(),
                                             'chance' => array(),
                                             'hash'   => $sHash);
        $i                           = 0;
        $iTotalChance                = 0;
        $iMax                        = 0;
        $iMaxPos                     = 0;
        $iRnd                        = 1;

        foreach ($aTemporary as $sKey => $iCnt) {
            while (preg_match_all('/{([^{^}]+)}/', $sKey, $aMatches)) {
                $aSearch = $aReplace = array();
                foreach ($aMatches[1] as $sMatch) {
                    $sNewKey = '%%rnd' . $iRnd . '%%';
                    $iRnd++;
                    $this->parseInput(preg_split('/\|/', $sMatch), $sNewKey);
                    $aSearch[]  = '{' . $sMatch . '}';
                    $aReplace[] = $sNewKey;
                }
                $sKey = str_replace($aSearch, $aReplace, $sKey);
            }
            $sKey                                      = str_replace('[...]', '{k}', $sKey);
            $this->aDistribution[$sName]['keys'][$i]   = $sKey;
            $iLocalAcc                                 = round($iCnt * $this->iAccuracy / $iTotal);
            $this->aDistribution[$sName]['chance'][$i] = $iLocalAcc;
            $iTotalChance += $iLocalAcc;
            if ($iCnt > $iMax) {
                $iMax    = $iCnt;
                $iMaxPos = $i;
            }
            $i++;
        }
        $this->aDistribution[$sName]['chance'][$iMaxPos] += $this->iAccuracy - $iTotalChance;
    }

    /**
     * ���������� ��������� �������� �� ������������� ��� ������ $sName, �������� �������� ������������
     * @param string $sName �������� �������������
     *
     * @return mixed item
     */
    protected function sGetRandomItem($sName = 'main')
    {
        $iRand = rand(0, $this->iAccuracy);
        $i     = $iAcc = 0;
        if (!isset($this->aDistribution[$sName])) die();
        reset($this->aDistribution[$sName]['chance']);
        while ($iAcc <= $iRand && isset($this->aDistribution[$sName]['chance'][$i])) {
            $iAcc += $this->aDistribution[$sName]['chance'][$i];
            $i++;
        }
        return $this->aDistribution[$sName]['keys'][$i - 1];
    }

    /**
     * ������� ��� callback, ���� ��� ������������ ����� ��������� ���������
     *
     * @param $aMatches
     *
     * @return mixed
     */
    public function sGetCallback($aMatches)
    {
        return $this->sGetRandom($aMatches[1]);
    }

    /**
     * sGetRandom ���������� ��������� ������.
     *
     * @param $sName
     *
     * @return mixed
     */
    public function sGetRandom($sName)
    {
        return preg_replace_callback('/(%%rnd\d+%%)/', array($this, 'sGetCallback'), $this->sGetRandomItem($sName));
    }

    public function sGetExport()
    {
        ob_start();
        var_export($this->aDistribution);
        $sContent = ob_get_contents();
        ob_end_clean();
        return $sContent;
    }

    public function vDistributionExport($sOutputFile)
    {
        //        if (!is_writable($sOutputFile)) throw new Exception('file not writable');
        $sContents = '<?php ';
        $sContents .= "\r\n \$iAccuracy = " . $this->iAccuracy . " ;\r\n";
        $sContents .= ' $aDistr= ';
        $sContents .= $this->sGetExport();
        $sContents .= "; \r\n ?>";
        file_put_contents($sOutputFile, $sContents);
    }

    /**
     * @param $sAnchor ��������
     */
    public function vDistributionImport($sAnchor)
    {
        $iFile   = 1;
        $sInFile = sprintf($this->sFilePathTemplate, $iFile);
        $this->_vDistributionImport($sInFile);
    }

    /**
     * @param $sInFile
     *
     * @throws Exception
     */
    public function _vDistributionImport($sInFile)
    {
        if (!file_exists($sInFile)) {
            throw new Exception('in file not exists :' . $sInFile);
        }
        $aDistr    = array();
        $iAccuracy = 100;
        include($sInFile);
        $this->setDistribution($aDistr);
        $this->setAccuracy($iAccuracy);
    }
}

?>
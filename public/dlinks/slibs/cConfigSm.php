<?php
class cConfigSm
{
    private static $instance;
    private $aObjects;
    const sLocalConfigFile  = '/../data/lconfig.php';
    const sGlobalConfigFile = '/../gconfig.php';

    /**
     * ������� ���������� ������ ������
     * @return cConfigSm object
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * ������� ��������� ��� �� ������� $name � ������ �������� ��� ���������
     * ���� ��� ������� ����� ������.
     * ���� �������� �������� �������, �������������� ������ � ����
     * @param string $name  - �������� �������
     * @param string $value - �������� �������, ���� ������ �� ������ ��� ����������� � ����
     *
     * @return mixed ������ ������� ��� ��������, ����� null
     */
    private function _instance($name, $value = NULL)
    {
        if (!is_null($value)) {
            if ($value === FALSE) {
                unset($this->aObjects[$name]);
                return NULL;
            } else {
                $this->aObjects[$name] = $value;
            }
        } elseif (isset($this->aObjects[$name])) {
            return $this->aObjects[$name];
        } else {
            return FALSE;
        }
        return $this->aObjects[$name];
    }

    /**
     * ������������� ��� ������� � ��������� ���������.
     * ���� $name ��� ���������� $default
     * @static
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed|string
     */
    public static function get($name, $default = '')
    {
        $obj = cConfigSm::getInstance();
        $res = $obj->_instance($name);
        if ($res === FALSE) {
            return $default;
        }
        return $res;
    }

    /**
     * ������������� ��� ������� ������ �������� ��������� ���������.
     * @static
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public static function set($name, $value = '')
    {
        $obj = cConfigSm::getInstance();
        $obj->_instance($name, $value);
    }

    /**
     * ������������� ��� ���������� �������� ���������� � �������
     * @static
     *
     * @param string $name
     * @param int    $count �� ������� ����������� ��������
     *
     * @return void
     */
    public static function inc($name, $count = 1)
    {
        $obj = cConfigSm::getInstance();
        $obj->_instance($name, $count + $obj->_instance($name));
    }


    /**
     * �������� ������� �������, ������� ���������
     * ������� ��������� ��� �� ������� $name � ������ �������� ��� ���������
     * ���� ��� ������� ����� ������.
     * ���� �������� �������� �������, �������������� ������ � ����
     * @param $name  - �������� �������
     * @param $value - �������� �������, ���� ������ �� ������ ��� ����������� � ����
     *
     * @return mixed ������ ������� ��� ��������, ����� null
     */
    public static function instance($name, $value = NULL)
    {
        $obj = cConfigSm::getInstance();
        return $obj->_instance($name, $value);
    }

    /***
     * ���������� ��� ������� � �������.. ����� �� ����� �������
     * @static
     */
    public static function vOutputAllObjects()
    {
        $obj = cConfigSm::getInstance();
        foreach ($obj->aObjects as $sStr => $aRow) {
            echo "$sStr ----> $aRow \r\n";
        }
    }

    public static function saveShowInfo()
    {
        $obj    = cConfigSm::getInstance();
        $sValue = $obj->aObjects['local_shown'] ? $obj->aObjects['local_shown'] : 0;
        $fh     = fopen(__SMDIR__ . cConfigSm::sLocalConfigFile, 'c+');
        if (!is_resource($fh)) {
            return;
        }
        fseek($fh, 36);
        fwrite($fh, sprintf('%-12s', $sValue));
        fclose($fh);
    }

    /***
     * ���������� ��� ������� � �������.. ����� �� ����� �������
     * @static
     *
     * @param bool $bCheck
     *
     * @return void
     */
    public static function saveLocalInfo($bCheck = FALSE)
    {

        $obj       = cConfigSm::getInstance();
        $sSaveData = '';
        $sValue    = $obj->aObjects['local_shown'] ? $obj->aObjects['local_shown'] : 0;
        $sSaveData .= "cConfigSm::set('local_shown', " . sprintf('%-12s', $sValue) . ");\r\n";
        foreach ($obj->aObjects as $sStr => $sValue) {
            if ($sStr == 'local_shown') continue;
            if (strpos($sStr, 'local') === 0) {
                // $sStr ���������� � local
                if (is_string($sValue)) {
                    $sValue = '\'' . $sValue . '\'';
                }
                $sSaveData .= "cConfigSm::set('$sStr', $sValue);\r\n";
            }
        }
        if ($bCheck && !$obj->bCheckFileChanged()) {
            return;
        }
        if (!empty($sSaveData)) {
            $sSaveData = "<?php\r\n" . $sSaveData . "?>";
            file_put_contents(__SMDIR__ . cConfigSm::sLocalConfigFile, $sSaveData);
        }
    }

    /**
     * @static
     * ��������� ��������� ������
     **/
    public static function loadLocalConfig()
    {
        if (file_exists(__SMDIR__ . cConfigSm::sLocalConfigFile)) {
            include_once(__SMDIR__ . cConfigSm::sLocalConfigFile);
        }
    }

    /**
     * @static
     * ��������� ��� ���� ������ �������
     */
    public static function loadGlobalConfig()
    {
        if (!file_exists(__SMDIR__ . cConfigSm::sGlobalConfigFile)) {
            throw new Exception('Unable to load global config. Check paths.' . __SMDIR__ . cConfigSm::sGlobalConfigFile);
        }
        include_once(__SMDIR__ . cConfigSm::sGlobalConfigFile);
    }

    /**
     * ���
     * @return bool
     **/
    public function bCheckFileChanged()
    {
        return FALSE;
    }
}

cConfigSm::instance('global_db2_prefix', 'data/bd2');
cConfigSm::instance('global_keys.db', 'data/keys.db');

?>
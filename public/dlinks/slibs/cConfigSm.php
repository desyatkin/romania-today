<?php
class cConfigSm
{
    private static $instance;
    private $aObjects;
    const sLocalConfigFile  = '/../data/lconfig.php';
    const sGlobalConfigFile = '/../gconfig.php';

    /**
     * Функция возвращает объект класса
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
     * Функция проверяет нет ли объекта $name в списке объектов уже созданных
     * если нет создает новый объект.
     * Если передано значение объекта, перезаписывает объект в кэше
     * @param string $name  - название объекта
     * @param string $value - значение объекта, если задоно то именно оно сохраняется в кэше
     *
     * @return mixed объект который был запрощен, иначе null
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
     * Предназначена для доступа к строковым инстансам.
     * Если $name нет возвращает $default
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
     * Предназначена для доступа записи значений строковым инстансам.
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
     * Предназначена для инкремента значения переменной в конфиге
     * @static
     *
     * @param string $name
     * @param int    $count на сколько увеличивать параметр
     *
     * @return void
     */
    public static function inc($name, $count = 1)
    {
        $obj = cConfigSm::getInstance();
        $obj->_instance($name, $count + $obj->_instance($name));
    }


    /**
     * Основная рабочая функция, внешний интерфейс
     * Функция проверяет нет ли объекта $name в списке объектов уже созданных
     * если нет создает новый объект.
     * Если передано значение объекта, перезаписывает объект в кэше
     * @param $name  - название объекта
     * @param $value - значение объекта, если задоно то именно оно сохраняется в кэше
     *
     * @return mixed объект который был запрощен, иначе null
     */
    public static function instance($name, $value = NULL)
    {
        $obj = cConfigSm::getInstance();
        return $obj->_instance($name, $value);
    }

    /***
     * Отображает все объёкты в конфиге.. нужна на время отладки
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
     * Отображает все объёкты в конфиге.. нужна на время отладки
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
                // $sStr начинается с local
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
     * загружает локальный конфиг
     **/
    public static function loadLocalConfig()
    {
        if (file_exists(__SMDIR__ . cConfigSm::sLocalConfigFile)) {
            include_once(__SMDIR__ . cConfigSm::sLocalConfigFile);
        }
    }

    /**
     * @static
     * загружаем еще одну секцию конфига
     */
    public static function loadGlobalConfig()
    {
        if (!file_exists(__SMDIR__ . cConfigSm::sGlobalConfigFile)) {
            throw new Exception('Unable to load global config. Check paths.' . __SMDIR__ . cConfigSm::sGlobalConfigFile);
        }
        include_once(__SMDIR__ . cConfigSm::sGlobalConfigFile);
    }

    /**
     * Про
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
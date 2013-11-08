<?php
if (!defined('__SMDIR__')) {
    define('__SMDIR__', dirname(__FILE__));
}

require_once(__SMDIR__ . '/cConfigSm.php');
require_once(__SMDIR__ . '/cFastDb.php');
/**
 *  ласс управл€ющий ключевиками
 */
class cKeysDb extends cFastDbWI
{
    const RAND_TRIES_LIMIT = 20;
    const RAND_TRIES_RAZE  = 10;

    /**
     * @var ќбща€ веро€тность
     */
    protected $iTotalFrequency;
    /**
     * @var  ол-во ключевиков
     */
    protected $iKeywordsCnt;
    protected $iTotalShown;
    protected $fAccuracy;
    protected $aFields = array(
        'keyword' => 50,
        'url'     => 32,
        'img'     => 124,
        'chance'  => 10,
        'shown'   => 10
    );

    /**
     *  онструктор
     * @param $sFileName   файл в котором находитс€ база данных
     * @param $iTotalFreq  обща€ веро€тность (!) FIXME: поточнее
     * @param $iFreqRows   кол-во строк в файле
     * @param $iTotalShown кол-во показов всех ключевиков
     */
    public function __construct($sFileName, $iTotalFreq = 0, $iFreqRows = 0, $iTotalShown = 0)
    {
        parent::__construct($sFileName);
        $this->iTotalFrequency = $iTotalFreq;
        $this->iKeywordsCnt    = $iFreqRows;
        $this->iTotalShown     = $iTotalShown;
        $this->fAccuracy       = ($this->iKeywordsCnt > 0) ? -0.01 / $this->iKeywordsCnt : -0.01;
    }

    /**
     * @static
     *
     * @param string $sUrl
     * @param int    $iTemplateId
     *
     * @return string текст дл€ отображени€
     * @throws Exception
     */
    public static function getBlock($sUrl, $iTemplateId = 1)
    {
        $sTemplate = FALSE;
        $bUTF8     = FALSE;
        try {
            cConfigSm::loadLocalConfig();
            cConfigSm::loadGlobalConfig();
            $iTotalShown = cConfigSm::get('local_shown', 0);
            $iFreqRows   = cConfigSm::get('local_rows', 0);
            $iTotalFreq  = cConfigSm::get('local_totalfreq', 0);
            $sKeysDb     = cConfigSm::get('global_keys.db', 'data/keys.db');
            $sDb2Prefix  = cConfigSm::get('global_db2_prefix', 'data/bd2');
            $bUTF8       = cConfigSm::get('global_utf8');

            if (($sSIDstr = cConfigSm::get('global_session_id')) !== FALSE) {
                $sSIDstr = trim($sSIDstr);
                if (!empty($sSIDstr)) {
                    $sSIDstr = str_replace(array(',', '\|', '&', '?', '.', '*', '#'), array('|', '\,', '\&', '\?', '\.', '\*', '\#'), $sSIDstr);
                    if (preg_match('#' . $sSIDstr . '#', $sUrl)) {
                        throw new Exception('SID found');
                    }
                }
            }
            $sUrl  = md5($sUrl);
            $oKeys = new cKeysDb(__SMDIR__ . '/../' . $sKeysDb, $iTotalFreq, $iFreqRows, $iTotalShown);
            $oLog  = new cFastDbDistributed(__SMDIR__ . '/../' . $sDb2Prefix, cConfigSm::get('global_db2_files', 10), TRUE);

            require_once 'cCustomDistribution.php';

            if ($iFoundRow = $oLog->mSearchName($sUrl)) {
                $aRand2 = $oLog->aReadFields($iFoundRow);
                $aRand  = array();
                foreach ($aRand2 as $sKey => $sVal) {
                    $aRand["{" . $sKey . "}"] = $sVal;
                }
            } else {
                // строка в базе не найдена
                $aRand = $oKeys->aGetNextRandomKey();
                if (empty($aRand['{comment}'])) {
                    // нет ошибки
                    $a = array(
                        'url'  => $sUrl,
                        'prod' => $aRand['{prod}'],
                        'img'  => $aRand['{img}'],
                        'key'  => $aRand['{key}'],
                        'tpl'  => $aRand['{tpl}']
                    );
                    cConfigSm::instance('local_shown', ++$iTotalShown);
                    $oLog->vAddRowToDb($a);
                    cConfigSm::saveShowInfo();
                }
            }
            $oLog->close();
            $sTemplate = self::sGetTemplate($iTemplateId);
        } catch (Exception $e) {
            $aKeyInfo['{text}']    = cConfigSm::get('global_default_text');
            $aKeyInfo['{url}']     = cConfigSm::get('global_default_anchor');
            $aKeyInfo['{img}']     = '';
            $aKeyInfo['{img_url}'] = cConfigSm::instance('global_default_img');
            $aKeyInfo['{comment}'] = "<!--" . $e->getMessage() . " -->";
            if ($sTemplate == FALSE) {
                $sTemplate = '<a href="{url}">{text}</a><br  />{comment}';
            }
            $str = str_replace(array_keys($aKeyInfo), $aKeyInfo, $sTemplate);
            if ($bUTF8) $str = self::sConvertToUTF8($str);
            return $str;
        }
        if (!empty($aRand['{img}'])) {
            $aRand['{img_url}'] = cConfigSm::get('global_img_url') . $aRand['{img}'];
            $aRand['{img}']     = '<img src="' . $aRand['{img_url}'] . '"><br />';
        } else {
            $aRand['{img}']     = '';
            $aRand['{img_url}'] = cConfigSm::instance('global_default_img');
        }
        $aRand['{url}']     = cConfigSm::get('global_url') . $aRand['{prod}'] . '.html';
        $aRand["{k}"]       = $aRand["{key}"];
        $aRand['{text}']    = str_replace(array_keys($aRand), $aRand, $aRand['{tpl}']);
        $aRand['{comment}'] = '';
        $str                = str_replace(array_keys($aRand), $aRand, $sTemplate);
        if ($bUTF8) $str = self::sConvertToUTF8($str);
        return $str;
    }

    /**
     * ƒревний способ конвертации в utf8
     * @static
     *
     * @param $sStr
     *
     * @return string
     */
    public static function sConvertToUTF8($sStr)
    {
        $c209       = chr(209);
        $c208       = chr(208);
        $c129       = chr(129);
        $c145       = chr(145);
        $sConverted = '';
        $len        = strlen($sStr);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($sStr[$i]);
            switch ($c) {
                case ($c >= 192 and $c <= 239):
                    $sConverted .= $c208 . chr($c - 48);
                    break;
                case ($c > 239):
                    $sConverted .= $c209 . chr($c - 112);
                    break;
                case 184:
                    $sConverted .= $c209 . $c145;
                    break;
                case 168:
                    $sConverted .= $c208 . $c129;
                    break;
                default:
                    $sConverted .= $sStr[$i];
                    break;
            }
        }
        return $sConverted;
    }

    /**
     * ¬озвращает шаблон под номером iTemplateId, если его нет возвращает первый шаблон
     * @static
     *
     * @param integer $iTemplateId
     *
     * @return boolean|string
     */
    public static function sGetTemplate($iTemplateId)
    {
        $sTemplate = cConfigSm::get('global_template' . $iTemplateId);
        if ($sTemplate == FALSE) {
            $sTemplate = cConfigSm::get('global_template1');
        }
        return $sTemplate;
    }

    /**
     * Ѕерет из базы следующий ключевик дл€ отображени€ информации о заказе.
     * @throws Exception
     * @return array
     *  array('text' => '', 'url' => '', 'img' => '');
     */
    public function aGetNextRandomKey()
    {
        $aKeyInfo = array();
        if ($this->iKeywordsCnt == 0) {
            throw new Exception('db1 empty');
        }
        $LastRand = $iRand = -1;
        if ($this->iTotalShown < $this->iKeywordsCnt) {
            $iRand = $this->iTotalShown + 1;
        }
        // ≈сли выбран iRand из iTotalShown то первое условие не выполнитс€, будет провер€тьс€ второе условие
        // ≈сли из второго услови€ получим нужные данные, то внутрь цикла заходить не будем
        // ¬се же необходимо использовать проверку на количество показов, возможны ошибки при не записи
        // ограничиваем количество записей в выборке через iTry и RAND_TRIES_LIMIT (масимальное число попыток выборки строки из файла)
        $iTry = 0;
        while (($LastRand == $iRand)
            || (($iTry++ < self::RAND_TRIES_LIMIT) && !($aKeyInfo = $this->aGetKeywordByRow($iRand)))) {
            /** @var $this cKeysDb */
            if ($iTry >= self::RAND_TRIES_RAZE) {
                $this->iTotalShown += 10;
                cConfigSm::instance('local_shown', $this->iTotalShown);
            }
            $iRand = $this->iGetPseudoNormalRandom($this->iKeywordsCnt, 3);
        }
        if ($iTry >= self::RAND_TRIES_LIMIT) {
            $this->close();
            include (__SMDIR__ . '/../scron.php');
            throw new Exception('failed to find rand');
        }
        $this->bWriteField($aKeyInfo['row'], 'shown', $aKeyInfo['shown'] + 1);
        $oDistrTemplates = new cCustomDistribution();
        $oDistrTemplates->vDistributionImport($aKeyInfo['keyword']);
        $sTextTemplate = $oDistrTemplates->sGetRandom('main');

        $aResult['{tpl}']     = $sTextTemplate;
        $aResult['{key}']     = $aKeyInfo['keyword'];
        $aResult['{prod}']    = $aKeyInfo['url'];
        $aResult['{img}']     = $aKeyInfo['img'];
        $aResult['{comment}'] = '';
        return $aResult;
    }

    /**
     * @param int $iRow Ќомер строки в базе
     *
     * @return array|bool false если не найдена строка, или строка не проходит по параметрам отображени€
     */
    public function aGetKeywordByRow($iRow)
    {
        $aArr = $this->aReadFields($iRow);
        // ≈сли текуща€ веро€тность показов больше веро€тности котора€ задана более чем на 2% возвращаем, что это объ€вление нельз€ показывать
        if ($aArr['shown'] != 0 && $this->iTotalShown != 0 && ($aArr['chance'] / $this->iTotalFrequency - $aArr['shown'] / $this->iTotalShown) < $this->fAccuracy) {
            return FALSE;
        }
        $this->iTotalShown++;
        $aArr['row'] = $iRow;
        return $aArr;
    }

    /**
     * ‘ункци€ вовзвращает случайное число, распределенное по следующей схеме
     * Ќаиболее часто встречающиес€ числа должны быть ближе к 0 и редко встречающиес€ около маскисального значени€
     * @static
     *
     * @param  int   $iMaximal  ћаксимальное значение.
     * @param int    $iLevel    ”ровень "нормальности" распределени€ функции „ем число больше тем больше значений будет попадать ближе к нулю
     *
     * @return int
     */
    public static function iGetPseudoNormalRandom($iMaximal, $iLevel = 3)
    {
        static $seeded;
        $iRandom = 0;

        if (!$seeded) {
            mt_srand((double)microtime() * 1000000);
            $seeded = TRUE;
        }

        for ($i = 1; $i <= $iLevel; $i++) {
            mt_srand();
            $iRandom += mt_rand(0, $iMaximal - 1);
        }
        // —двинем полученное случайное число на MO влево
        //return $iRandom;
        $iRandom -= ($iMaximal - 1) * $iLevel / 2;
        $iStartFrom = 0;
        if ($iRandom < 0) {
            $iRandom    = -$iRandom;
            $iStartFrom = -1;
        }
        $iTmp = ($iStartFrom + $iRandom * 2) * $iMaximal / ($iMaximal - 2);
        // и приводим к новой схеме
        $iRandom = floor($iTmp / $iLevel) - 1;
        if ($iRandom < 1 || $iRandom > $iMaximal) $iRandom = 1;
        return $iRandom;
    }
}

?>
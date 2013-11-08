<?php
/**
 * Класс содержит информацию о расположении файла ключевика на сервере сотмаркет
 */
class cRemoteFile
{
    const REMOTE_FILE_URL           = 'http://xml.sotmarket.ru/xml/hidden/kwrds.txt';
    const REMOTE_USER               = 'xml5';
    const REMOTE_PASS               = 'ksdh85430jf5786qhf';
    const REMOTE_FILE_POSITION_KWRD = 0;
    const REMOTE_FILE_POSITION_URL  = 1;
    const REMOTE_FILE_POSITION_IMG  = 2;
    const REMOTE_FILE_POSITION_FREQ = 3;
    const FILE_EXPIRE_TIME          = 86400; // 1 день

    /**
     * Функция получает данные из удалённого файла
     * Не используем curl так как он может быть отключен у удаленного пользователя
     * @static
     * @return string remote file data
     * @throws Exception if wget failed
     */
    public static function sGetRemoteFile()
    {
        // создаем контекст
        $context = stream_context_create(array(
            'http' => array(
                'header' => "Authorization: Basic "
                    . base64_encode(self::REMOTE_USER . ":" . self::REMOTE_PASS)
            )
        ));
        $sData   = file_get_contents(self::REMOTE_FILE_URL, FALSE, $context);
        if ($sData === FALSE) {
            throw new Exception('Error while fetching remote file');
        }
        self::checkFileData($sData);
        return $sData;
    }

    /**
     * Проверяет полученный файл на похожесть с оригинальным файлом
     * @static
     *
     * @param string $sData полученный файл
     *
     * @throws Exception
     */
    public static function checkFileData($sData)
    {
        $iLength = strlen($sData);
        if ($iLength < 100000) {
            throw new Exception('Wrong file');
        }
        $sPart  = substr($sData, 0, 200);
        $aParts = preg_split('/;/', $sPart);
        if (count($aParts) < 3) {
            throw new Exception('Wrong file');
        }
    }

    /**
     * @static
     *
     * @param $sPath
     *
     * @throw Exception if wget failed or write failed
     */
    public static function sUpdateFile($sPath)
    {
        @file_put_contents($sPath, self::sGetRemoteFile());
    }

    /**
     * @static
     *
     * @param      $sFilePath
     * @param bool $bSaveBackup
     *
     * @return bool true если все прекрасно, false
     */
    public static function  bActualizeFile($sFilePath, $bSaveBackup = FALSE)
    {
        if (!file_exists($sFilePath) || (self::FILE_EXPIRE_TIME + filemtime($sFilePath) < time())) {
            try {
                if ($bSaveBackup && file_exists($sFilePath)) {
                    /** @var $sFilePath string */
                    @rename($sFilePath, $sFilePath . '~');
                }
                self::sUpdateFile($sFilePath);
            } catch (Exception $e) {
                // случилась ошибка возвращаем из бекапа файл
                if (file_exists($sFilePath . '~')) {
                    @rename($sFilePath . '~', $sFilePath);
                }
                return FALSE;
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

?>
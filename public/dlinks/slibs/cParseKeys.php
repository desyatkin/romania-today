<?php
require_once 'cCustomDistribution.php';

class cParseKeys
{
    protected $file = '';
    /** @var cCustomDistribution */
    protected $oDistribution;
    protected $aRows = array();
    protected $iAccuracy = 10000;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function vProcess()
    {
        $this->vLoadFile();
    }

    public function vLoadFile()
    {
        $fh = fopen($this->file, 'r');
        if (!is_resource($fh)) {
            exit;
        }
        $this->aRows = array();
        $aInfo       = array();
        while (!feof($fh)) {
            $line    = fgets($fh);
            $aInfo[] = $line;
        }
        $this->oDistribution = new cCustomDistribution($this->iAccuracy);
        $this->oDistribution->parseInput($aInfo);
    }

    public function vSaveFile($sOutputFile)
    {
        $this->oDistribution->vDistributionExport($sOutputFile);
    }
}

?>
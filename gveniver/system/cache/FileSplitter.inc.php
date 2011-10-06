<?php
/**
 * File contains base abstract class for splitting files.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Cache;
\Gveniver\Loader::i('system/cache/packer/DataPacker.inc.php');
\Gveniver\Loader::i('system/exception/ArgumentException.inc.php');

/**
 * Base class for splitting files.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class FileSplitter
{
    /**
     * TimeToLive of cache in seconds.
     *
     * @var int
     */
    const CACHE_TTL = 1200;
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    
    /**
     * Current packer for files content.
     * 
     * @var DataPacker
     */
    private $_cPacker;
    //-----------------------------------------------------------------------------------
    
    /**
     * Array of file names for splitting.
     *
     * @var array
     */
    private $_aFileList = array();
    //-----------------------------------------------------------------------------------

    /**
     * Output file name.
     *
     * @var string
     */
    private $_sOutputFileName;
    //-----------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------
    
    /**
     * Class constructor.
     * 
     * @param string     $sOutputFileName Output cache file name.
     * @param DataPacker $cPacker         Packer for data. Optional.
     */
    public function __construct($sOutputFileName, DataPacker $cPacker = null)
    {
        // Checks.
        $sOutputFileName = \Gveniver\Loader::correctPath($sOutputFileName);
        $sOutputDir = dirname($sOutputFileName);
        if (!file_exists($sOutputDir))
            if (!mkdir($sOutputDir, null, true))
                throw new \Gveniver\Exception\ArgumentException(
                    sprintf('[%s] Error in creating file directory!', __CLASS__)
                );
        if (!file_exists($sOutputDir) || !is_dir($sOutputDir) || !is_writable($sOutputDir))
            throw new \Gveniver\Exception\ArgumentException(
                sprintf('[%s] Incorrect target cache file name or path!', __CLASS__)
            );

        $this->_sOutputFileName = $sOutputFileName;
        $this->_cPacker = $cPacker;
            
    } // End function
    //-----------------------------------------------------------------------------------

    /**
     * Adding file for caching.
     *
     * @param string   $sAbsoluteFilePath Absolute file path to add.
     * @param callback $fModifier         Modifier for file.
     *
     * @return boolean
     */
    public function addFile($sAbsoluteFilePath, $fModifier = null)
    {
        $sAbsoluteFilePath = \Gveniver\Loader::correctPath($sAbsoluteFilePath);
        if (!file_exists($sAbsoluteFilePath) || is_dir($sAbsoluteFilePath))
            return false;

        $this->_aFileList[] = array(
            'file'     => $sAbsoluteFilePath,
            'modifier' => $fModifier
        );
        return true;

    } // End function
    //-----------------------------------------------------------------------------------

    /**
     * Checking correctness of existing cache.
     *
     * @param string $sOutputFileName File name to check.
     *
     * @return bool True if correct.
     * @static
     */
    public static function isCorrectCache($sOutputFileName)
    {
        // File existance.
        if (!file_exists($sOutputFileName))
            return false;
        
        return (time() - filemtime($sOutputFileName)) < self::CACHE_TTL;
                
    } // End function
    //-----------------------------------------------------------------------------------
    
    /**
     * Clear cuurent cache if exists.
     *
     * @return void
     */
    protected function flush()
    {
        if (file_exists($this->_sOutputFileName))
            unlink($this->_sOutputFileName);
        
    } // End function
    //-----------------------------------------------------------------------------------

    /**
     * Save alreadey added files to output file.
     * If specified data packer, use them for pack content of files.
     *
     * @return boolean
     */
    public function save()
    {
        $fp = fopen($this->_sOutputFileName, 'w');
        if (!$fp)
            return false;
    
        flock($fp, LOCK_EX);
        foreach ($this->_aFileList as $aFileData) {

            $sSaveContent = $this->_cPacker
                ? $this->_cPacker->pack(file_get_contents($aFileData['file']))
                : file_get_contents($aFileData['file']);

            if ($aFileData['modifier'])
                $sSaveContent = $aFileData['modifier']($sSaveContent);

            fwrite($fp, $sSaveContent);
        }
        
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;    
        
    } // End function
    //-----------------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------------
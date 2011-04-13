<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvKernelInclude::instance()->includeFile('src/system/cache/provider/CacheProvider.inc.php');

/**
 *
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class FileCacheProvider extends CacheProvider
{
    /**
     * Path to directory for cache.
     * 
     * @var string
     */
    private $_sBaseCacheDirectory;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     *
     * @param GvKernel $cKernel  Current kernel.
     * @param array    $aOptions Options for cache provider.
     *
     * @return void
     */
    public function __construct(GvKernel $cKernel, array $aOptions)
    {
        // Use parent constructor.
        parent::__construct($cKernel, $aOptions);

        // Load cache folder.
        $this->_sBaseCacheDirectory = (string)$this->cKernel->cConfig->get('Profile/Path/AbsCache');
        if (!$this->_sBaseCacheDirectory || !file_exists($this->_sBaseCacheDirectory) || !is_dir($this->_sBaseCacheDirectory))
            throw new GvException('Wrong cache directory.');

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Load data form cache.
     *
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param mixed  &$cRef         Reference variable for loading cached data.
     *
     * @return boolean True on success loading
     */
    public function get($sCacheId, $sCacheGroupId, &$cRef)
    {
        // Try to load data from cache file.
        $mData = null;
        $nTime = 0;
        if (!$this->_readFile($sCacheId, $sCacheGroupId, $nTime, $mData))
            return false;

        // Check TTL.
        if ($nTime < GV_DATE_NOW)
            return false;

        // Restore data and returns result.
        $cRef = unserialize($mData);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save data to cache.
     *
     * @param mixed  $mData         Data to save.
     * @param strin  $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    $nTtl          Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sCacheGroupId, $nTtl)
    {
        // Save data to cache file.
        return $this->_writeFile(
            $sCacheId,
            $sCacheGroupId,
            GV_TIME_NOW + $nTtl,
            serialize($mData)
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Flush cache data.
     *
     * @param string $sCacheGroupId Identifier of cache group.
     *
     * @return boolean True on success.
     */
    public function flush($sCacheGroupId)
    {
        $sCacheDirectory = $this->_sBaseCacheDirectory.$sCacheGroupId.GV_DS;
        if (!is_dir($sCacheDirectory))
            return false;

        // Remove all files form cache directory.
        foreach(array_diff(scandir($sCacheDirectory), array('.', '..')) as $sCacheFile)
            unlink($sCacheDirectory.GV_DS.$sCacheFile);

        return true;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Read cache file.
     * 
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    &$nTtl         Time to live for cache.
     * @param string &$mData        Cache data.
     *
     * @return boolean True on success.
     */
    private function _readFile($sCacheId, $sCacheGroupId, &$nTtl, &$mData)
    {
        // Build cache file name. Check ixistence and correctness of cache file.
        $sFileName = $this->_sBaseCacheDirectory.$sCacheGroupId.GV_DS.$sCacheId;
        $bFileExist = file_exists($sFileName);
        $bFileIsDir = is_dir($sFileName);
        if (!$bFileExist || $bFileIsDir)
            return false;

        // Open and block cache file for reading.
        $file = fopen($sFileName, 'r');
        flock($file, LOCK_SH);

        // TTL.
        $aTemp = unpack('L', fread($file, 4));
        $nTtl = $aTemp[1];

        // Length.
        $aTemp = unpack('L', fread($file, 4));
        $nSize = $aTemp[1];

        // Data.
        if ($nSize > 0) {
            $aTemp = unpack('a*', fread($file, $nSize));
            $mData = $aTemp[1];
        } else {
            return false;
        }

        // Unlock and close file.
        flock($file, LOCK_UN);
        fclose($file);
        return true;

    } // End function
    //------------------------------------------------------------------------------------

    /**
     * Write cache file.
     *
     * @param string $sCacheId      Identifier of cache.
     * @param string $sCacheGroupId Identifier of cache group.
     * @param int    $nTtl          Time to live for cache.
     * @param string $sData         Cache data.
     *
     * @return boolean True on success.
     */
    private function _writeFile($sCacheId, $sCacheGroupId, $nTtl, $sData)
    {

        // Build path to cache directory. Create, if not exists.
        $sCacheDirectory = $this->_sBaseCacheDirectory.$sCacheGroupId.GV_DS;
        if (!file_exists($sCacheDirectory)) {
            $this->cKernel->trace->addLine('[%s] Cache directory "%s" not found. Creating.', __CLASS__, $sCacheDirectory);
            if (!!mkdir($sCacheDirectory, 0777, true)) {
                 $this->cKernel->trace->addLine('[%s] Cache directory "%s" not created.', __CLASS__, $sCacheDirectory);
                return false;
            }
        }

        // Build path to cache file.
        $sFileName = $sCacheDirectory.$sCacheId;

        // Write cache data with exclusive lock.
        $fp = fopen($sFileName, 'wb');
        flock($fp, LOCK_EX);

        fwrite($fp, pack('L', $nTtl));                  // TTL.
        fwrite($fp, pack('L', strlen($sData)));         // Length.
        fwrite($fp, pack('a*', $sData));                // Data.

        // Unlock and close file.
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;

    } // End function
    //--------------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
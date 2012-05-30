<?php
/**
 * File contains file cache provider class.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Cache\Provider;

/**
 * File cache provider class.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class FileCacheProvider extends BaseCacheProvider
{
    /**
     * Access mask for cacke files.
     *
     * @var int
     */
    private $_nFileMode = 0777;
    //-----------------------------------------------------------------------------

    /**
     * Access mask for cache directories.
     *
     * @var int
     */
    private $_nDirMode = 0777;
    //-----------------------------------------------------------------------------

    /**
     * Path to base cache directory.
     *
     * @var string
     */
    private $_sBaseCacheDirectory;
    //-----------------------------------------------------------------------------

    /**
     * Path to directory with cached data.
     *
     * @var string
     */
    private $_sBaseCacheDataDirectory;
    //-----------------------------------------------------------------------------

    /**
     * Path to dirrectory with tags.
     *
     * @var string
     */
    private $_sBaseCacheTagsDirectory;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     * @param array                        $aOptions     Options for cache provider.
     *
     * @throws \Gveniver\Exception\BaseException Throws on errors in directory structure.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, array $aOptions)
    {
        // Use parent constructor.
        parent::__construct($cApplication, $aOptions);

        // File and diretory access rights.
        if (isset($aOptions['FileMode']) && $aOptions['FileMode']) {
            $this->_nFileMode = octdec($aOptions['FileMode']);
        }
        if (isset($aOptions['DirMode']) && $aOptions['DirMode']) {
            $sDirMode = $aOptions['DirMode'];
            $this->_nDirMode = octdec($sDirMode);
        }
        $this->getApplication()->trace->addLine('[%s] Using file_mode: %d, dir_mode: %d.', __CLASS__, $this->_nFileMode, $this->_nDirMode);

        // Loading the name of cache directory.
        $this->_sBaseCacheDirectory = (string)$this->getApplication()->getConfig()->get('Profile/Path/AbsCache');
        if (!$this->_sBaseCacheDirectory) {
            $sMessage = sprintf('[%s] The name of cache directory is not found in configuration.', __CLASS__);
            $this->getApplication()->trace->addLine($sMessage);
            throw new \Gveniver\Exception\BaseException($sMessage);
        }

        // Building cache directories.
        $this->_initDirectories();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method initializes directories for cache.
     *
     * @return void
     */
    private function _initDirectories()
    {
        $this->_sBaseCacheDirectory = \Gveniver\correctPath($this->_sBaseCacheDirectory, true);
        if (!file_exists($this->_sBaseCacheDirectory) || !is_writeable($this->_sBaseCacheDirectory)) {
            $this->getApplication()->trace->addLine('[%s] Start creating base cache directory ("%s") is not exist.', __CLASS__, $this->_sBaseCacheDirectory);
            $this->_createDirectory($this->_sBaseCacheDataDirectory);
        }

        // Build directory for cache data.
        $this->_sBaseCacheDataDirectory = $this->_sBaseCacheDirectory.'data'.GV_DS;
        if (!file_exists($this->_sBaseCacheDataDirectory) || !is_writeable($this->_sBaseCacheDataDirectory)) {
            $this->getApplication()->trace->addLine('[%s] Start creating cache directory for data ("%s") is not exist.', __CLASS__, $this->_sBaseCacheDataDirectory);
            $this->_createDirectory($this->_sBaseCacheDataDirectory);
        }

        // Build directory for cache tags.
        $this->_sBaseCacheTagsDirectory = $this->_sBaseCacheDirectory.'tags'.GV_DS;
        if (!file_exists($this->_sBaseCacheTagsDirectory) || !is_writeable($this->_sBaseCacheTagsDirectory)) {
            $this->getApplication()->trace->addLine('[%s] Start creating cache directory for tags ("%s") is not exist.', __CLASS__, $this->_sBaseCacheTagsDirectory);
            $this->_createDirectory($this->_sBaseCacheTagsDirectory);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method creates directory if it does not exists.
     *
     * @param string $sDirName Directory name for creating.
     *
     * @throws \Gveniver\Exception\BaseException
     * @return void
     */
    private function _createDirectory($sDirName)
    {
        $sDirName = \Gveniver\correctPath($sDirName, true);
        if (!file_exists($sDirName)) {
            $this->getApplication()->trace->addLine('[%s] The directory ("%s") is not exist. Creating.', __CLASS__, $sDirName);
            $nOldUmask = umask(0);
            if (!mkdir($sDirName, $this->_nDirMode, true)) {
                umask($nOldUmask);
                $sMessage = sprintf('[%s] Error in creating directory ("%s").', __CLASS__, $sDirName);
                $this->getApplication()->trace->addLine($sMessage);
                throw new \Gveniver\Exception\BaseException($sMessage);
            }
            chmod($sDirName, $this->_nDirMode);
            umask($nOldUmask);
        } else if (!is_dir($sDirName)) {
            $sMessage = sprintf('[%s] Specified directory ("%s") is not a directory.', __CLASS__, $sDirName);
            $this->getApplication()->trace->addLine($sMessage);
            throw new \Gveniver\Exception\BaseException($sMessage);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method creates directory tree by hash of file name.
     *
     * @param string  $sBaseName Base root directory of tree.
     * @param string  $sFileName File name for building tree.
     * @param boolean $bCreate   The flag determines whether to create directories.
     *
     * @return string Full path in tree to file with name as hash of specified name.
     */
    private function _createDirectoryTree($sBaseName, $sFileName, $bCreate = true)
    {
        $sCacheFileName = md5($sFileName);
        $sBuildedPath = \Gveniver\correctPath($sBaseName, true);
        for ($i = 0; $i < 2; $i++) {
            $sBuildedPath .= $sCacheFileName[$i].GV_DS;
            if ($bCreate)
                $this->_createDirectory($sBuildedPath);
        }

        return $sBuildedPath.$sCacheFileName;

    } // End function
    //--------------------------------------------------------------------------------

    /**
     * Method loads cached data by identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     * @param mixed  &$cRef    Reference variable for loading cached data.
     *
     * @return boolean True on success loading.
     */
    public function get($sCacheId, &$cRef)
    {
        // Building cache file name and checking for existence.
        $sDataFileName = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sCacheId, false);
        $bFileExist = file_exists($sDataFileName);
        $bFileIsDir = is_dir($sDataFileName);
        if (!$bFileExist || $bFileIsDir)
            return false;

        // Loading cached data and checking structure.
        try {
            $aCacheData = unserialize(file_get_contents($sDataFileName));
        } catch (\Exception $cEx) {
            return false;
        }

        if (!is_array($aCacheData)
            || !isset($aCacheData['value'])
            || !isset($aCacheData['tags'])
            || !isset($aCacheData['alive'])
        )
            return false;

        // Checking if cache is alive.
        if (time() > $aCacheData['alive'])
            return false;

        // Checking if tags is alive.
        foreach ($aCacheData['tags'] as $sTag => $fVersion)
            if ($this->_getTagVersion($this->_getTagCacheId($sTag)) > $fVersion)
                return false;

        $cRef = $aCacheData['value'];
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method saves data to the cache.
     *
     * @param mixed  $mData    Data for caching.
     * @param string $sCacheId The identifier of cached data.
     * @param array  $aTags    List of tags for this cache record.
     * @param int    $nTtl     Time to live for cache in seconds.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, array $aTags, $nTtl)
    {
        // Building path to cache data file.
        $sDataFileName = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sCacheId, true);

        // Building data of tags.
        $aTagData = array();
        foreach ($aTags as $sTag)
            $aTagData[$sTag] = $this->_getTagVersion($this->_getTagCacheId($sTag));

        // Building data for caching.
        $aCacheData = array(
            'value' => $mData,
            'tags'  => $aTagData,
            'alive' => time() + $nTtl
        );

        // Try to serialize data for saving.
        try {
            $sData = serialize($aCacheData);
        } catch (\Exception $cEx) {
            return false;
        }

        // Opening data file for writing with exclusive lock.
        $nOldUmask = umask(0);
        $fData = fopen($sDataFileName, 'wb');
        if (!$fData) {
            $this->getApplication()->trace->addLine('[%s] Error in opening data file "%s" for writing.', __CLASS__, $sDataFileName);
            return false;
        }
        if (!flock($fData, LOCK_EX)) {
            $this->getApplication()->trace->addLine('[%s] Error in exclusive locking of data file "%s".', __CLASS__, $sDataFileName);
            return false;
        }

        // Writing data.
        fwrite($fData, $sData);

        // Changing permissions and ulocking
        chmod($sDataFileName, $this->_nFileMode);
        flock($fData, LOCK_UN);
        fclose($fData);
        umask($nOldUmask);

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by identifier.
     *
     * @param string $sCacheId The identifier of cached data.
     *
     * @return boolean True on success.
     */
    public function clean($sCacheId)
    {
        $sDataFileName = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sCacheId, false);
        if (!file_exists($sDataFileName))
            return false;

        return unlink($sDataFileName);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans all cache data.
     *
     * @return boolean True on success.
     */
    public function cleanAll()
    {
        // Removing directories with data and tags.
        \Gveniver\rrmdir($this->_sBaseCacheDataDirectory);
        \Gveniver\rrmdir($this->_sBaseCacheTagsDirectory);

        // Rebbuilding directory structure.
        $this->_initDirectories();
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cached data by specified tags.
     *
     * Removes related cache data files and content of cache tag file.
     * Atomic operation with locking tag file for reading.
     *
     * @param array $aTags List of tags for cleaning.
     *
     * @throws \Gveniver\Exception\BaseException Throws on wrong content of cache tag file.
     * @return boolean True on success.
     */
    public function cleanByTags(array $aTags)
    {
       foreach ($aTags as $sTag)
           $this->_invalidateTag($this->_getTagCacheId($sTag));

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method builds identifier of record with metadata of specified cache tag.
     *
     * @param string $sTag Name of tag.
     *
     * @return string
     */
    private function _getTagCacheId($sTag)
    {
        return 'tag_'.md5($sTag);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns version of specified tag. If tag is not exists in cache, it will be created.
     *
     * @param string $sTag The name of the tag.
     *
     * @return float
     */
    private function _getTagVersion($sTag)
    {
        $sTagFileName = $this->_createDirectoryTree($this->_sBaseCacheTagsDirectory, $sTag, false);
        if (file_exists($sTagFileName)) {
            $mVersion = file_get_contents($sTagFileName);
            if ($mVersion && !is_double($mVersion))
                return floatval($mVersion);
        }

        return $this->_invalidateTag($sTag);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Increase version of tag. This operation automatically invalidates all data that are related to the tag.
     *
     * @param string $sTag The name of the tag.
     *
     * @return float New version of tag.
     */
    private function _invalidateTag($sTag)
    {
        $fVersion = round(microtime(true), 3);
        $sTagFileName = $this->_createDirectoryTree($this->_sBaseCacheTagsDirectory, $sTag, true);
        file_put_contents($sTagFileName, $fVersion, LOCK_EX);
        return $fVersion;

    } // End function
    //-----------------------------------------------------------------------------


} // End class
//-----------------------------------------------------------------------------
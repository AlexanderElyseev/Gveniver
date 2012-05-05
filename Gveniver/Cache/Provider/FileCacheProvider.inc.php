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
     * Load data form cache.
     *
     * @param string $sCacheId   Identifier of cache.
     * @param string $sNamespace Namespace of cache.
     * @param mixed  &$cRef      Reference variable for loading cached data.
     *
     * @return boolean True on success loading
     */
    public function get($sCacheId, $sNamespace, &$cRef)
    {
        // Build cache file name with namespace.
        $sNamespaceDir = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sNamespace, false);
        $sDataFileName = $this->_createDirectoryTree($sNamespaceDir, $sCacheId, false);

        $bFileExist = file_exists($sDataFileName);
        $bFileIsDir = is_dir($sDataFileName);
        if (!$bFileExist || $bFileIsDir)
            return false;

        // Open and block cache file for reading.
        $file = fopen($sDataFileName, 'r');
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

        // Undefined (or zero) TTL means that cache should live forever.
        if ($nTtl && $nTtl < GV_TIME_NOW)
            return false;

        $cRef = unserialize($mData);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save data to cache.
     *
     * @param mixed  $mData      Data to save.
     * @param string $sCacheId   Identifier of cache.
     * @param string $sNamespace Namespace of cache.
     * @param array  $aTags      List of tags for this cache record.
     * @param int    $nTtl       Time to live for cache.
     *
     * @return boolean True on success.
     */
    public function set($mData, $sCacheId, $sNamespace, array $aTags, $nTtl)
    {
        // Building path to cache data file and cache tag file with subdirectories.
        $sNamespaceDir = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sNamespace);
        $this->_createDirectory($sNamespaceDir);
        $sDataFileName = $this->_createDirectoryTree($sNamespaceDir, $sCacheId);

        // Try to serialize data for saving.
        try {
            $sData = serialize($mData);
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
        fwrite($fData, pack('L',  GV_TIME_NOW + $nTtl));    // TTL.
        fwrite($fData, pack('L', strlen($sData)));          // Length.
        fwrite($fData, pack('a*', $sData));                 // Data.

        // Changing permissions and ulocking
        chmod($sDataFileName, $this->_nFileMode);
        flock($fData, LOCK_UN);
        fclose($fData);
        umask($nOldUmask);

        // Updating tags.
        foreach ($aTags as $sTag)
            $this->_appendTag($sTag, $sDataFileName);

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified parameters.
     *
     * @param string $sNamespace Namespace of cache.
     * @param string $sCacheId   Identifier of cache. If it is specified, clean only record with specified identifier.
     * Otherwise, clean all namespace.
     *
     * @return boolean True on success.
     */
    public function clean($sNamespace, $sCacheId = null)
    {
        $sNamespaceDir = $this->_createDirectoryTree($this->_sBaseCacheDataDirectory, $sNamespace, false);
        if ($sCacheId) {
            $sDataFileName = $this->_createDirectoryTree($sNamespaceDir, $sCacheId, false);
            if (file_exists($sDataFileName))
                unlink($sDataFileName);
        } else {
            \Gveniver\rrmdir($sNamespaceDir);
        }

        return true;

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

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method cleans cache data by specified tags.
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
        foreach ($aTags as $sTag) {
            $sTagFileName = $this->_createDirectoryTree($this->_sBaseCacheTagsDirectory, $sTag, false);
            if (!file_exists($sTagFileName))
                return array();

            $hTagFile = fopen($sTagFileName, 'w+');
            if (!$hTagFile) {
                $this->getApplication()->trace->addLine('[%s] Error in opening tag file "%s" for reading.', __CLASS__, $sTagFileName);
                return false;
            }
            if (!flock($hTagFile, LOCK_SH)) {
                $this->getApplication()->trace->addLine('[%s] Error in exclusive locking of tag file "%s".', __CLASS__, $sTagFileName);
                return false;
            }
            $nFileSize = filesize($sTagFileName);
            if ($nFileSize > 0) {
                $aKeys = unserialize(fread($hTagFile, $nFileSize));
                if (!is_array($aKeys)) {
                    $sMessage = sprintf('[%s] Wrong tag content.', __CLASS__);
                    $this->getApplication()->trace->addLine($sMessage);
                    throw new \Gveniver\Exception\BaseException($sMessage);
                }
            } else
                $aKeys = array();

            ftruncate($hTagFile, 0);
            flock($hTagFile, LOCK_UN);
            fclose($hTagFile);

            foreach ($aKeys as $sDataFileName)
                if (file_exists($sDataFileName))
                    unlink($sDataFileName);
        }

        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method adds the name of data file to specified tag.
     * Atomic operation with locking tag file for writing.
     *
     * @param string $sTag          The name of tag for adding data file.
     * @param string $sDataFileName The name of data file for adding to tag.
     *
     * @throws \Gveniver\Exception\BaseException Throws if data of tag file is wrong.
     *
     * @return bool
     */
    private function _appendTag($sTag, $sDataFileName)
    {
        $oldUmask = umask(0);
        $sTagFileName = $this->_createDirectoryTree($this->_sBaseCacheTagsDirectory, $sTag);
        $hTagFile = fopen($sTagFileName, 'a+');
        if (!$hTagFile) {
            $this->getApplication()->trace->addLine('[%s] Error in opening tag file "%s" for writing.', __CLASS__, $sTagFileName);
            return false;
        }
        if (!flock($hTagFile, LOCK_EX)) {
            $this->getApplication()->trace->addLine('[%s] Error in exclusive locking of tag file "%s".', __CLASS__, $sTagFileName);
            return false;
        }

        $nFileSize = filesize($sTagFileName);
        if ($nFileSize > 0) {
            $aKeys = unserialize(fread($hTagFile, $nFileSize));
            if (!is_array($aKeys)) {
                $sMessage = sprintf('[%s] Wrong tag content.', __CLASS__);
                $this->getApplication()->trace->addLine($sMessage);
                throw new \Gveniver\Exception\BaseException($sMessage);
            }

            if (!in_array($sDataFileName, $aKeys))
                $aKeys[] = $sDataFileName;
        } else
            $aKeys = array($sDataFileName);


        // Writing data, changing permissions and ulocking.
        fseek($hTagFile, 0);
        fwrite($hTagFile, serialize($aKeys));
        chmod($sTagFileName, $this->_nFileMode);
        flock($hTagFile, LOCK_UN);
        fclose($hTagFile);
        umask($oldUmask);

        return true;

    } // End function
    //--------------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
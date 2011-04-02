<?php
/**
 * 
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * TODO: Проблема переинициализации кэша кода, если кэш включен и файл был изменен.
 * TODO: Вырезание php тэгов только из начала и конца файла при кэшировании.
 * 
 * Provide Singleton design pattern.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
final class GvKernelInclude
{
    /**
     * Error code. No errors.
     *
     * @var int
     */
    const ERRROR_NONE = 0;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Unknown error.
     *
     * @var int
     */
    const ERRROR_UNKNOWN = 1;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Error in include file.
     *
     * @var int
     */
    const ERRROR_FILE_INCLUDE = 2;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Target class not exists.
     *
     * @var int
     */
    const ERRROR_CLASS_NOT_EXISTS = 3;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Wrong base class.
     *
     * @var int
     */
    const ERRROR_WRONG_BASE_CLASS = 4;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Error in create object: exception in constructor.
     *
     * @var int
     */
    const ERRROR_CONSTRUCTOR = 5;
    //-----------------------------------------------------------------------------

    /**
     * Error code. Error in create object: try to create instance of abstract class.
     *
     * @var int
     */
    const ERRROR_ABSTRACT_CLASS = 6;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Singleton instance of {@see GvKernelInclude}.
     *
     * @var GvKernelInclude
     */
    private static $_cInstance;
    //-----------------------------------------------------------------------------

    /**
     * List of metadata about inclueded files.
     * @var array
     */
    private $_aIncludeMeta;
    //-----------------------------------------------------------------------------

    /**
     * List of metadata about skip files.
     * This files will not include.
     * 
     * @var array
     */
    private $_aSkipIncludeMeta;
    //-----------------------------------------------------------------------------

    /**
     * Flag of first loading cache.
     *
     * @var bool
     */
    private $_bFirstLoadCache;
    //-----------------------------------------------------------------------------

    /**
     * Flag of changed cache.
     * 
     * @var bool
     */
    private $_bCacheChanged;
    //-----------------------------------------------------------------------------

    /**
     * Flag of enabled cache.
     * 
     * @var bool
     */
    private $_bCacheEnabled;
    //-----------------------------------------------------------------------------

    /**
     * Path to code cache file.
     * 
     * @var string
     */
    private $_sCacheCodeFilePath;
    //-----------------------------------------------------------------------------

    /**
     * Path to metadata cache file
     * 
     * @var string
     */
    private $_sCacheMetaFilePath;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Private singleton constructor of {@see GvKernelInclude}.
     * Initialize member fields.
     *
     * @return void
     */
    private function __construct()
    {
        $this->_sCacheCodeFilePath = GV_PATH_CACHE.'include.inc.php';
        $this->_sCacheMetaFilePath = GV_PATH_CACHE.'include.dat';

        $this->_aIncludeMeta = array();
        $this->_aSkipIncludeMeta = array();
        $this->_bFirstLoadCache = true;
        $this->_bCacheChanged = false;
        $this->_bCacheEnabled = false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Class destructor.
     * Save cache by metadata if it was changed.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->_bCacheChanged && $this->_bCacheEnabled)
            $this->_saveCache();
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns current singleton instance of {@see GvKernelInclude}.
     *
     * @return GvKernelInclude
     * @static
     */
    public static function instance()
    {
        if (!self::$_cInstance)
            self::$_cInstance = new self;

        return self::$_cInstance;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Include code file.
     *
     * @param string $sFileName File name for adding to skip list.
     *
     * @return bool True on success.
     */
    public function includeFile($sFileName)
    {
        // On first call of this function, try to load data from cache, then change
        // flag of first function call.
        // Do not load the cache in the class constructor. This can lead to recursion in
        // constructor if the code from cache use {@see GvKernelInclude}.
        if ($this->_bCacheEnabled && $this->_bFirstLoadCache) {
            $this->_bFirstLoadCache = false;
            $this->_loadCache();
        }
        
        // Build metadata for file.
        $aMeta = $this->_buildMeta($sFileName);
        if (!$aMeta)
            return false;
        
        // If file is not included, include file and add include metadata.
        // Otherwise, do nothing.
        $aMeta = $this->_buildMeta($sFileName);
        $bIncluded = isset($this->_aIncludeMeta[$aMeta['hash']]);
        $bSkip = isset($this->_aSkipIncludeMeta[$aMeta['hash']]);
        if (!$bIncluded && !$bSkip) {
            $this->_bCacheChanged = true;
            $this->_aIncludeMeta[$aMeta['hash']] = $aMeta;
            include $sFileName;
            return true;
        }

        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add file to skip list.
     * These files will not be included at {@see GvKernelInclude::includeFile}.
     *
     * @param string $sFileName File name for adding to skip list.
     *
     * @return bool True of successful operation.
     */
    public function skipFile($sFileName)
    {
        // Build metadata for file.
        $aMeta = $this->_buildMeta($sFileName);
        if (!$aMeta)
            return false;

        // Add to skip list.
        $this->_aSkipIncludeMeta[$aMeta['hash']] = $aMeta;
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cached code files and metadata.
     *
     * @return bool
     */
    private function _loadCache()
    {
        $bExistsCodeFile = file_exists($this->_sCacheCodeFilePath);
        $bExistsMetaFile = file_exists($this->_sCacheMetaFilePath);
        if (!$bExistsCodeFile || !$bExistsMetaFile)
            return false;

        $this->_aIncludeMeta = unserialize(file_get_contents($this->_sCacheMetaFilePath));
        require $this->_sCacheCodeFilePath;
		return true;
		
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save all include files to one cache file by include metadata.
     * Save metadata with serialization.
     * From each file removes php open and close tags.
     *
     * @return void
     */
    private function _saveCache()
    {
        // Check, is cache enabled.
        if (!$this->_bCacheEnabled)
            return;
        
        $sCodeContent = '<?php'.PHP_EOL;
        foreach (array_reverse($this->_aIncludeMeta) as $aMeta) {
            $sFileContent = file_get_contents($aMeta['path']);

            // Cut php open and close tags.
            $sFileContent = str_replace('<?php', '', $sFileContent);
            $sFileContent = str_replace('<?', '', $sFileContent);
            $sFileContent = str_replace('?>', '', $sFileContent);

            $sCodeContent .= $sFileContent.PHP_EOL;
        }

        // Create cache directory, if not exists.
        $sCacheDir = dirname($this->_sCacheCodeFilePath);
        if (!file_exists($sCacheDir))
            mkdir($sCacheDir, 0666, true);

        file_put_contents($this->_sCacheCodeFilePath, $sCodeContent, LOCK_EX);
        file_put_contents($this->_sCacheMetaFilePath, serialize($this->_aIncludeMeta), LOCK_EX);

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Build metadata for include file.
     * File must exists.
     *
     * @param string $sFileName Full file name.
     *
     * @return array Metadata for include file.
     */
    private function _buildMeta($sFileName)
    {
        // Correct file path.
        $sFileName = self::correctPath($sFileName);

        // Check the existence of file for include by relative or absolute path.
        // If file is not exists, do nothing.
        if (!$this->isAbsolutePath($sFileName))
            $sFileName = GV_PATH_BASE.$sFileName;
        if (!file_exists($sFileName))
            return null;

        return array(
            'hash'  => md5($sFileName),
            'mtime' => filemtime($sFileName),
            'path'  => $sFileName,
        );

    } // End function
    //-----------------------------------------------------------------------------
        
    /**
     * Correction method of the directory separator character.
     * Replace all wrong characters to correct directory separator.
     *
     * @param string $sFileName Name of file.
     *
     * @return string
     */
    public static function correctPath($sFileName)
    {
        return (GV_DS === '/')
            ? str_replace('\\', GV_DS, $sFileName)
            : str_replace('/', GV_DS, $sFileName);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Check, is this path is absolute.
     *
     * @param string $sPath Path to check.
     *
     * @return bool True if absolute.
     */
    public static function isAbsolutePath($sPath)
    {
        if (defined('GV_OS_WIN') && GV_OS_WIN)
            return preg_match('/^[a-z]:/i', $sPath);
        
        return $sPath[0] === '/';

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method for dynamically load class file and create new object of class.
     * If class is not exists, file will be included automatically.
     *
     * Parameters:
     * - class   - Class name for create object.
     * - path    - Path to include file, if class not exists.
     *             May contain %class% placeholder. It will be replaced by specified class name.
     * - [base]  - Base class name.
     * - [args]  - Arguments to constructor.
     *
     *
     * @param array $aParams
     * @param int   &$nErrorCode

     * @return mixed
     * @static
     */
    public static function createObject(array $aParams, &$nErrorCode = null)
    {
        // Include class file, if class is not exists.
        $sClassName = isset($aParams['class']) ? $aParams['class'] : null;
        if (!class_exists($sClassName)) {

            // Build path to file by template.
            $sPathTpl = isset($aParams['path']) ? $aParams['path'] : null;
            $sPathTpl = str_replace('%class%', $sClassName, $sPathTpl);

            // Try to include file by builded path.
            if (!GvKernelInclude::instance()->includeFile($sPathTpl)) {
                $nErrorCode = self::ERRROR_FILE_INCLUDE;
                return null;
            }

            // Check. Class must exists after including.
            if (!class_exists($sClassName)) {
                $nErrorCode = self::ERRROR_CLASS_NOT_EXISTS;
                return null;
            }

        } // End if

        // Class cannot be abstract.
        $cRc = new ReflectionClass($sClassName);
        if ($cRc->isAbstract()) {
            $nErrorCode = self::ERRROR_ABSTRACT_CLASS;
            return null;
        }

        // Check base class.
        $sBaseClassName = isset($aParams['base']) ? $aParams['base'] : null;
        if ($sBaseClassName) {
            if (!in_array('GvKernelModule', class_parents($sClassName))) {
                $nErrorCode = self::ERRROR_WRONG_BASE_CLASS;
                return null;
            }
        }
        
		// Create object instance.
        $aArguments = isset($aParams['args']) ? $aParams['args'] : null;
		try {
            if ($aArguments) {
                $cObj = $cRc->newInstanceArgs($aArguments);
            } else {
                $cObj = new $sClassName;
            }
		} catch (Exception $cEx) {
            $nErrorCode = self::ERRROR_CONSTRUCTOR;
			return null;
		}

        $nErrorCode = self::ERRROR_NONE;
        return $cObj;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
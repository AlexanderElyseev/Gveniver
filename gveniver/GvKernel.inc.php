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
 *
 *
 *
 * PHP version 5
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
final class GvKernel
{
    /**
     * Configuration of kernel.
     *
     * @var GvKernelConfig
     */
    public $cConfig;
    //-----------------------------------------------------------------------------

    /**
     * Profile of kernel.
     *
     * @var GvKernelProfile
     */
    private $_cProfile;
    //-----------------------------------------------------------------------------

    /**
     * List of loaded modules.
     *
     * @var array
     */
    private $_aModules = array();
    //-----------------------------------------------------------------------------

    /**
     * Hash table of module names.
     *
     * @var array
     */
    private $_aModuleNameHash = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see GvKernel} class.
     * Initialize new instance of kernel and PHP environment by kernel configuration.
     *
     * @return void
     */
    public function __construct($sProfileName)
    {
        // Initialize and load confiuration.
        $this->cConfig = new GvKernelConfig();

        // Initialization of environment.
        $this->initEnvironment();

        // Load profile.
        $this->_cProfile = $this->_loadProfile($sProfileName);
        if (!$this->_cProfile)
            throw new GvException(sprintf('Kernel profile with name "%s" not found.', $sProfileName));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Overloaded getter method for non existing class fields.
     * Returns kernel module by field name.
     *
     * @param string $sName Parameter name for reading.
     *
     * @return GvKernelModule Null on error.
     */
    public function __get($sName)
    {
        $cModule = $this->getModule($sName);
        if (!$cModule)
            throw new GvException(sprintf('Module ("%s") not loaded.', $sName));

        return $cModule;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Initialization of environment by configuration of current profile.
     *
     * @return void
     */
    public function initEnvironment()
    {
        $this->trace->addLine('[%s] Start initialize environement of the kernel.', __CLASS__);

        // Error reporting.
        $nErrorReporting = $this->cConfig->get('Kernel/ErrorReporting');
        ini_set('error_reporting', $nErrorReporting);
        $this->trace->addLine('[%s] Error reporting: %d.', __CLASS__, $nErrorReporting);

        // Display errors.
        $bDisplayErrors = self::toBoolean($this->cConfig->get('Kernel/DisplayErrors'));
        ini_set('display_errors', $bDisplayErrors);
        if ($bDisplayErrors)
            $this->trace->addLine('[%s] Display errors: %s.', __CLASS__, $bDisplayErrors);

        // Start session.
        $bStartSession = self::toBoolean($this->cConfig->get('Kernel/StartSession'));
        if ($bStartSession) {
            session_start();
            $this->trace->addLine('[%s] Session started.', __CLASS__);
        }

        // Used locale.
        $sLocale = $this->cConfig->get('Kernel/Locale');
        setlocale(LC_ALL, $sLocale);
        $this->trace->addLine('[%s] Locale: %s.', __CLASS__, $sLocale);

        // Used timezone.
        $sTimezone = $this->cConfig->get('Kernel/Timezone');
        date_default_timezone_set($sTimezone);
        $this->trace->addLine('[%s] Timezone: %s.', __CLASS__, $sTimezone);

        // Multibyte encoding.
        $sEncoding = $this->cConfig->get('Kernel/Encoding');
        mb_internal_encoding($sEncoding);
        mb_regex_encoding($sEncoding);
        $this->trace->addLine('[%s] Encoding: %s.', __CLASS__, $sEncoding);

        // Use output buffering.
        $bUseBuffering = self::toBoolean($this->cConfig->get('Kernel/UseOutputBuffering'));
        if ($bUseBuffering) {
            $this->trace->addLine('[%s] Use output buffering.', __CLASS__);

            // Force compression even when client does not report support.
            $bForceCompression = self::toBoolean($this->cConfig->get('Kernel/ForceCompression'));

            // Prefer deflate over gzip when both are supported.
            $bPreferDeflate = self::toBoolean($this->cConfig->get('Kernel/PreferDeflate'));

            // Handle the output stream and set a handler function.
            if(isset($_SERVER['HTTP_ACCEPT_ENCODING']))
                $sAE = $_SERVER['HTTP_ACCEPT_ENCODING'];
            else
                $sAE = $_SERVER['HTTP_TE'];

            $bGzipSupport = (strpos($sAE, 'gzip') !== false) || $bForceCompression;
            $bDeflateSupport = (strpos($sAE, 'deflate') !== false) || $bForceCompression;
            if($bGzipSupport && $bDeflateSupport)
                $bDeflateSupport = $bPreferDeflate;

            if ($bDeflateSupport) {                     // Defalte compression.
                header('Content-Encoding: deflate');
                ob_start('GvKernel::obHandlerDeflate');
            } elseif($bGzipSupport) {                   // Gzip compression.
                header('Content-Encoding: gzip');
                ob_start('GvKernel::obHandlerGzip');
            } else                                      // No compression.
                ob_start();

        } // End if

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load kernel profile instance by name.
     *
     * @param string $sProfileName Name of profile for loading.
     *
     * @return GvKernelProfile|null Returns kernel profile by specified name or null, if module not loaded.
     */
    private function _loadProfile($sProfileName)
    {
        $this->trace->addLine('Start loading profile ("%s").', $sProfileName);

        // Check profile directory.
        $sProfilePath = GV_PATH_BASE.'profile'.GV_DS.$sProfileName.GV_DS;
        if (!is_dir($sProfilePath)) {
            $this->trace->addLine('[%s] Profile directory ("%s") is not exists.', __CLASS__, $sProfilePath);
            return null;
        }

        // Check profile module file.
        $sProfilePhpFile = $sProfilePath.$sProfileName.'.inc.php';
        if (!is_file($sProfilePhpFile)) {
            $this->trace->addLine('[%s] Profile file ("%s") is not exists.', __CLASS__, $sProfilePhpFile);
            return null;
        }

        // Include profile file with class, if target class is not exists.
        $cProfileClass = $sProfileName.'KernelProfile';
        if (!class_exists($cProfileClass)) {
            $this->trace->addLine(
                '[%s] Profile class ("%s") is not exists. Start of including file: "%s".',
                __CLASS__,
                $cProfileClass,
                $sProfilePhpFile
            );

            GvKernelInclude::instance()->includeFile($sProfilePhpFile);
            if (!class_exists($cProfileClass)) {
                $this->trace->addLine(
                    '[%s] Profile class ("%s") is not exists after including file ("%s").',
                    __CLASS__,
                    $cProfileClass,
                    $sProfilePhpFile
                );
                return null;
            }
        }

        // Profile class must extend base profile class.
        if (!in_array('GvKernelProfile', class_parents($cProfileClass))) {
            $this->trace->addLine('[%s] Profile class ("%s") must extends base profile class.', __CLASS__, $cProfileClass);
            return null;
        }

        $this->trace->addLine('[%s] Profile class ("%s") successfully loaded.', __CLASS__, $cProfileClass);

        // Create instance of profile.
        try {
            $cProfile = new $cProfileClass($this);
        } catch (Exception $cEx) {
            $this->trace->addLine('[%s] Exception in profile ("%s") constructor: "%s".', __CLASS__, $sProfileName, $cEx->getMessage());
            return null;

        } // End catch

        $this->trace->addLine('[%s] Profile instance ("%s") successfully created.', __CLASS__, $cProfileClass);

        // Load configuration of profile append to main configuration.
        $sProfileXmlFile = $sProfilePath.'config.xml';
        if ($this->cConfig->mergeXmlFile($sProfileXmlFile)) {
            $this->trace->addLine(
                '[%s] Configuration parameters of profile ("%s") successfully loaded (from "%s").',
                __CLASS__,
                $sProfileName,
                $sProfileXmlFile
            );
        }

        return $cProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load kernel module by name.
     *
     * !!! Do not use modules in this function for preven recursions !!!
     *
     * At first, try to load module from loaded list. If module with specified name not found,
     * load module instance.
     *
     * @param string $sModuleName Name of module for loading.
     *
     * @return GvKernelModule|null Returns kernel module by specified name or null, if module not loaded.
     */
    private function _loadModule($sModuleName)
    {
        // If was an attempt of loading module, only return saved result.
        if (array_key_exists($sModuleName, $this->_aModules))
            return $this->_aModules[$sModuleName];

        // Mark attemp of loading module as failed by default.
        $this->_aModules[$sModuleName] = null;

        // If module class is not exists, include module file.
        if (!class_exists($sModuleName))
            if (!GvKernelInclude::instance()->includeFile('gveniver'.GV_DS.'module'.GV_DS.$sModuleName.'.inc.php'))
                return null;

        // After including module file, class of module must exists.
        if (!class_exists($sModuleName))
            return null;

        // Class of module must extends base kernel module class.
        if (!in_array('GvKernelModule', class_parents($sModuleName)))
            return null;

        // Check module relations.
        $aRelations = $this->cConfig->get(array('Module', $sModuleName, 'Relations'));
        if (is_array($aRelations) && count($aRelations) > 0)
            foreach ($aRelations as $sRelationModule)
                if (!$this->_loadModule($sRelationModule['Name']))
                    return null;

        // Try to create new instance of kernel module.
        try {
            $cModule = new $sModuleName($this);
        } catch (GvException $cEx) {
            return null;
        }

        // Save to cache and return result.
        $this->_aModules[$sModuleName] = $cModule;
        return $cModule;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns kernel module by name.
     * Analyze short module names and save to cache.
     *
     * @param string $sModuleName Name of module. May be short (ex. trace -> TraceModule).
     *
     * @return GvKernelModule|null
     */
    public function getModule($sModuleName)
    {
        // Try to load from correct module name from cache or build.
        if (isset($this->_aModuleNameHash[$sModuleName])) {
            $sCorrectName = $this->_aModuleNameHash[$sModuleName];
        } else {
            $sCorrectName = $sModuleName;
            if (!preg_match('/^\w+Module$/i', $sCorrectName))
                $sCorrectName .= 'Module';

            $sCorrectName = str_replace('module', 'Module', $sCorrectName);
            $sCorrectName[0] = strtoupper($sCorrectName[0]);
            $this->_aModuleNameHash[$sModuleName] = $sCorrectName;
        }

        return $this->_loadModule($sCorrectName);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns current kernel profile.
     *
     * @return GvKernelProfile
     */
    public function getProfile()
    {
        return $this->_cProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Output buffer handler with GZip compression.
     *
     * @param string $sBuffer Buffer data.
     *
     * @return string
     */
    public static function obHandlerGzip($sBuffer)
    {
        return gzencode($sBuffer, 9);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Output buffer handler with deflate compression.
     *
     * @param string $sBuffer Buffer data.
     *
     * @return string
     */
    public static function obHandlerDeflate($sBuffer)
    {
        return gzdeflate($sBuffer, 9);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Convert value to boolean.
     *
     * @param mixed $mValue Value to convert.
     *
     * @return bool Convert result.
     * @static
     */
    public static function toBoolean($mValue)
    {
        if ($mValue === true || $mValue === 1 || $mValue === '1' || $mValue === 'true')
            return true;

        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Convert value to integer, if value is not null.
     *
     * @param mixed $mValue Value to convert.
     *
     * @return integer|null Convert result.
     * @static
     */
    public static function toIntegerOrNull($mValue)
    {
        return is_null($mValue) ? null : intval($mValue);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------